<?php

namespace Herisson\Entity;

use Herisson\Repository\FriendRepository;
use Herisson\Service\Network;
use Doctrine\ORM\Mapping as ORM;
use Herisson\Encryption;
use Herisson\Message;
use Herisson\Model\BookmarkTable;
use Herisson\Network\Exception as NetworkException;

/**
 * @ORM\Entity(repositoryClass=FriendRepository::class)
 */
class Friend
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2048)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $alias;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     */
    private $public_key;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_active;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_youwant;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_wantsyou;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }
    /**
     * Set the Url of a friend, and retrieve the public key from it
     *
     * @param string $url the url of the friend
     *
     * @return Friend
     */
    public function setUrl(string $url): self
    {
        $this->url = rtrim($url, '/');
        $this->reloadPublicKey();
        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->public_key;
    }

    public function setPublicKey(string $public_key): self
    {
        $this->public_key = $public_key;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getIsYouwant(): ?bool
    {
        return $this->is_youwant;
    }

    public function setIsYouwant(bool $is_youwant): self
    {
        $this->is_youwant = $is_youwant;

        return $this;
    }

    public function getIsWantsyou(): ?bool
    {
        return $this->is_wantsyou;
    }

    public function setIsWantsyou(bool $is_wantsyou): self
    {
        $this->is_wantsyou = $is_wantsyou;

        return $this;
    }

    /**
     * Get info data from a friend
     *
     * Do a network hit to retrieve friend's info
     *
     * @return void
     */
    public function getInfo()
    {
        $url = $this->url."/info";
        $network = new Network();
        try  {
            $json_data = $network->download($url);
            $data = json_decode($json_data['data'], 1);

            if (sizeof($data)) {
                $this->name  = $data['sitename'];
                $this->email = $data['adminEmail'];
            } else {
                $this->is_active=0;
            }

        } catch (NetworkException $e) {
            $this->is_active=0;
            switch ($e->getCode()) {
                case 404:
                    Message::i()->addError("This site is not a Herisson site or is closed.");
                    break;
            }
        }
    }





    /**
     * Reload the public key from the friend
     *
     * Do a network hit to retrieve the public key
     *
     * @return void
     */
    public function reloadPublicKey()
    {
        $network = new Network();
        try {

            $content = $network->download($this->url."/publickey");
            $this->setPublicKey($content['data']);

        } catch (NetworkException $e) {
            switch ($e->getCode()) {
                case 404:
                    Message::i()->addError("This site is not a Herisson site or is closed.");
                    break;
            }
        }
    }


    /**
     * Get all the bookmarks from a friend
     *
     * @param array $params the optional parameters to specify which bookmarks to retrieve
     *
     * @return string the json encode data for friend's bookmarks
     */
    public function retrieveBookmarks($params=array())
    {

        $options = get_option('HerissonOptions');
        $my_public_key = $options['publicKey'];
        if (function_exists('curl_init')) {
            $network = new Network();
            $params['key'] = $my_public_key;
            try {

                $content = $network->download($this->url."/retrieve", $params);
                $encryption_data = json_decode($content['data'], true);
                $json_data = Encryption::i()->privateDecryptLongData($encryption_data['data'], $encryption_data['hash'], $encryption_data['iv']);
                $bookmarks = json_decode($json_data, 1);
                return $bookmarks;

            } catch (Network\Exception $e) {
                switch ($e->getCode()) {
                    case 404:
                        Message::i()->addError("This site is not a Herisson site or is closed.");
                        break;
                }
            }
        }
    }


    /**
     * Generate bookmarks data
     *
     * @param array $params the optional parameters to specify which bookmarks to retrieve
     *
     * @return string the json encode data for friend's bookmarks
     */
    public function generateBookmarksData($params=array())
    {
        $options = get_option('HerissonOptions');
        $my_private_key = $options['privateKey'];
        $bookmarks = BookmarksTable::getBookmarksData($param, 1);

        $data_bookmarks = array();
        foreach ($bookmarks as $bookmark) {
            $data_bookmarks[] = $bookmark->toSmallArray();
        }
        $json_data = json_encode($data_bookmarks);
        try {
            $json_display = Encryption::i()->publicEncryptLongData($json_data, $this->public_key);
        } catch (Encryption\Exception $e) {
            Network::reply(417);
            echo $e->getMessage();
        }
        return json_encode($json_display);
    }


    /**
     * Ask a site for friend
     *
     * Do network hit to the friend's url and add it to our friends list
     *
     * @return void
     */
    public function askForFriend()
    {
        $options    = get_option('HerissonOptions');
        $url        = $this->url."/ask";
        $mysite     = get_option('siteurl')."/".$options['basePath'];
        $signature  = Encryption::i()->privateEncrypt($mysite);
        $postData = array(
            'url'       => $mysite,
            'signature' => $signature
        );
        $network = new Network();
        try {
            $content = $network->download($url, $postData);
            switch ($content['code']) {
                case 200:
                    // Friend need to process the request manually, you will be notified when validated.
                    $this->b_youwant=1;
                    $this->save();
                    break;
                case 202:
                    // Friend automatically accepted the request. Adding now.
                    $this->is_active=1;
                    $this->save();
                    break;
            }
        } catch (Network\Exception $e) {
            switch ($e->getCode()) {
                case 403:
                    Message::i()->addError("This site refuses new friends.");
                    break;
                case 404:
                    Message::i()->addError("This site is not a Herisson site or is closed.");
                    break;
                case 417:
                    Message::i()->addError("Friend say you dont communicate correctly (key problems?).");
                    break;

            }
        }
    }


    /**
     * Validate a friend
     *
     * Do network hit to the friend's url and validate it's request
     *
     * @return true if validation was succesful, false otherwise
     */
    public function validateFriend()
    {
        $signature = Encryption::i()->privateEncrypt(HERISSON_LOCAL_URL);
        $postData = array(
            'url'       => HERISSON_LOCAL_URL,
            'signature' => $signature
        );
        $network = new Network();
        try {
            $content = $network->download($this->url."/validate", $postData);
            if ($content['data'] === "1") {
                $this->b_wantsyou=0;
                $this->is_active=1;
                $this->save();
                return true;
            } else {
                return false;
            }
        } catch (Network\Exception $e) {
            Message::i()->addError($e->getMessage());
            return false;
        }
    }


    /**
     * Check if the friend accepts backups
     *
     * Do network hit to the friend's url
     *
     * @return true if validation was succesful, false otherwise
     */
    public function acceptsBackups()
    {
        $signature = Encryption::i()->privateEncrypt(HERISSON_LOCAL_URL);
        $postData = array(
            'url'       => HERISSON_LOCAL_URL,
            'signature' => $signature
        );
        $network = new Network();
        try {
            $content = $network->download($this->url."/acceptsbackups", $postData);
            return intval($content['data']);
        } catch (Network\Exception $e) {
            switch ($e->getCode()) {
                case 403:
                    return 0;
                    break;
                case 406:
                    return 2;
                    break;
            }
            return $e->getCode();
        }
    }


    /**
     * Send backup data to this friend
     *
     * Do network hit to the friend's url
     * We cipher our bookmarks data with our public key, so only we can read our bookmarks with our private key
     *
     * @param string $data the bookmark json data
     *
     * @return true if backup was succesful, false otherwise
     */
    public function sendBackup($data)
    {
        $signature   = Encryption::i()->privateEncrypt(HERISSON_LOCAL_URL);
        $cryptedData = Encryption::i()->publicEncryptLongData($data);
        //print_r($data);
        $postData    = array(
            'url'        => HERISSON_LOCAL_URL,
            'signature'  => $signature,
            'backupData' => serialize($cryptedData),
        );
        $network = new Network();
        try {
            $content = $network->download($this->url."/sendbackup", $postData);
            return intval($content['data']);
        } catch (Network\Exception $e) {
            switch ($e->getCode()) {
                case 417:
                    return 0;
                    break;
            }
            return $e->getCode();
        }
    }


    /**
     * Download backup data from this friend
     *
     * Do network hit to the friend's url
     * We decipher our bookmarks data with our private key, because only we can read our bookmarks
     *
     * @return true if backup was succesful, false otherwise
     */
    public function downloadBackup()
    {
        $signature   = Encryption::i()->privateEncrypt(HERISSON_LOCAL_URL);
        $postData    = array(
            'url'        => HERISSON_LOCAL_URL,
            'signature'  => $signature,
        );
        $network = new Network();
        try {
            $content = $network->download($this->url."/downloadbackup", $postData);

            // FIXME We should not have to use stripslashes here !!
            $encryptionData = unserialize(stripslashes($content['data']));

            $data = Encryption::i()->privateDecryptLongData($encryptionData['data'], $encryptionData['hash'], $encryptionData['iv']);
            return $data;

        } catch (Network\Exception $e) {
            switch ($e->getCode()) {
                case 417:
                    return 0;
                    break;
            }
            return $e->getCode();
        }
    }

}
