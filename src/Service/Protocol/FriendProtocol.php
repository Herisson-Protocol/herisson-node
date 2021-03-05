<?php

namespace Herisson\Service\Protocol;


use Doctrine\ORM\EntityManager;
use Herisson\Entity\Friend;
use Herisson\Repository\FriendRepository;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Message;
use Herisson\Service\Network\Exception as NetworkException;
use Herisson\Service\Network\Grabber;
use Herisson\Service\OptionLoader;

class FriendProtocol extends HerissonProtocol
{
    public const ASK_PATH = '/ask';
    public const INFO_PATH = '/info';
    public const PUBLICKEY_PATH = '/publicKey';
    public const VALIDATE_PATH = '/validate';

    /**
     * @var Friend
     */
    private $friend;

    /**
     * @var OptionLoader
     */
    private $optionLoader;

    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * @var Grabber
     */
    private $grabber;

    /**
     * @var Message
     */
    private $message;

    /**
     * FriendProtocol constructor.
     * @param OptionLoader $optionLoader
     * @param Encryptor $encryptor
     * @param Grabber $grabber
     * @param Message $message
     */
    public function __construct(OptionLoader $optionLoader, Encryptor $encryptor, Grabber $grabber, Message $message)
    {
        $this->optionLoader = $optionLoader;
        $this->encryptor = $encryptor;
        $this->grabber = $grabber;
        $this->message = $message;
    }

    public function pendingForFriendsValidation(Friend $friend, EntityManager $em)
    {
        $friend->pendingForFriendsValidation();
        $em->persist($friend);
        $em->flush();
    }
    public function friendValidateOurRequest(Friend $friend, EntityManager $em)
    {
        $friend->friendValidateOurRequest();
        $em->persist($friend);
        $em->flush();
    }


    /**
     * Ask a site for friend
     *
     * Do network hit to the friend's url and add it to our friends list
     *
     * @param Friend $friend
     * @return void
     * @throws Exception
     * @throws \Herisson\Service\Encryption\Exception
     * @throws \Herisson\Service\Network\Exception
     */
    public function ask(Friend $friend)
    {
        $options = $this->optionLoader->load(['siteurl', 'basePath', 'privateKey']);
        $askUrl = $friend->getActionUrl(static::ASK_PATH);
        dump($askUrl);
        $mysite = $options['siteurl']."/".$options['basePath'];
        $signature = $this->encryptor->privateEncrypt($mysite, $options['privateKey']);

        $postData = [
            'url'       => $mysite,
            'signature' => $signature
        ];
        dump($postData);
        $dispath = [
            200 => [$this, "pendingForFriendsValidation", $friend],
            202 => [$this, "friendValidateOurRequest", $friend],
            403 => [$this->message, "addError", "This site refuses new friends."],
            404 => [$this->message, "addError", "This site is not a Herisson site or is closed."],
            417 => [$this->message, "addError", "Friend say you dont communicate correctly (key problems?)."],

        ];
        $response = $this->grabber->getResponse($askUrl, $postData);
        dump($response);
        $this->dispatchResponse($response, $dispath);
    }

    /**
     * Ask a site for friend
     *
     * Do network hit to the friend's url and add it to our friends list
     *
     * @param Friend $friend
     * @return void
     * @throws Exception
     * @throws \Herisson\Service\Encryption\Exception
     * @throws \Herisson\Service\Network\Exception
     */
    public function reloadPublicKey(Friend $friend, Grabber $grabber)
    {
            $publicKeyUrl = $friend->getActionUrl(static::PUBLICKEY_PATH);
            $response = $grabber->getResponse($publicKeyUrl);
            $friend->setPublicKey($response->getContent());

            $dispath = [
                200 => [$this, "updatePublicKey"],
                404 => [$this->message, "addError", "This site is not a Herisson site or is closed."],
            ];
            $response = $this->grabber->getResponse($publicKeyUrl);
            dump($response);
            $this->dispatchResponse($response, $dispath);
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
        $network = new Grabber();
        try  {
            $json_data = $network->getContent($url);
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
     * Validate a friend
     *
     * Do network hit to the friend's url and validate it's request
     *
     * @return bool true if validation was succesful, false otherwise
     */
    public function validateFriend(Encryptor $encryptor, Message $message)
    {
        $signature = $encryptor->privateEncrypt(HERISSON_LOCAL_URL);
        $postData = array(
            'url'       => HERISSON_LOCAL_URL,
            'signature' => $signature
        );
        $network = new Grabber();
        try {
            $content = $network->getResponse($this->url."/validate", $postData);
            if ($content['data'] === "1") {
                $this->b_wantsyou=0;
                $this->is_active=1;
                $this->save();
                return true;
            } else {
                return false;
            }
        } catch (\Herisson\Service\Network\Exception $e) {
            $message->addError($e->getMessage());
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
    public function acceptsBackups(Encryptor $encryptor)
    {
        $signature = $encryptor->privateEncrypt(HERISSON_LOCAL_URL);
        $postData = array(
            'url'       => HERISSON_LOCAL_URL,
            'signature' => $signature
        );
        $network = new Grabber();
        try {
            $content = $network->getResponse($this->url."/acceptsbackups", $postData);
            return intval($content['data']);
        } catch (\Herisson\Service\Network\Exception $e) {
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
    public function sendBackup($data, Encryptor $encryptor)
    {
        $signature   = $encryptor->privateEncrypt(HERISSON_LOCAL_URL);
        $cryptedData = $encryptor->publicEncryptLongData($data);
        //print_r($data);
        $postData    = array(
            'url'        => HERISSON_LOCAL_URL,
            'signature'  => $signature,
            'backupData' => serialize($cryptedData),
        );
        $network = new Grabber();
        try {
            $content = $network->download($this->url."/sendbackup", $postData);
            return intval($content['data']);
        } catch (\Herisson\Service\Network\Exception $e) {
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
    public function downloadBackup(Encryptor $encryptor)
    {
        $signature   = $encryptor->privateEncrypt(HERISSON_LOCAL_URL);
        $postData    = array(
            'url'        => HERISSON_LOCAL_URL,
            'signature'  => $signature,
        );
        $network = new Grabber();
        try {
            $content = $network->download($this->url."/downloadbackup", $postData);

            // FIXME We should not have to use stripslashes here !!
            $encryptionData = unserialize(stripslashes($content['data']));

            $data = $encryptor->privateDecryptLongData($encryptionData['data'], $encryptionData['hash'], $encryptionData['iv']);
            return $data;

        } catch (\Herisson\Service\Network\Exception $e) {
            switch ($e->getCode()) {
                case 417:
                    return 0;
                    break;
            }
            return $e->getCode();
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
            $network = new Grabber();
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
    public function generateBookmarksData($params=array(), Encryptor $encryptor)
    {
        $options = get_option('HerissonOptions');
        $my_private_key = $options['privateKey'];
        $bookmarks = BookmarksTable::getBookmarksData($params, 1);

        $data_bookmarks = array();
        foreach ($bookmarks as $bookmark) {
            $data_bookmarks[] = $bookmark->toSmallArray();
        }
        $json_data = json_encode($data_bookmarks);
        try {
            $json_display = $encryptor->publicEncryptLongData($json_data, $this->public_key);
        } catch (\Herisson\Service\Encryption\Exception $e) {
            Grabber::reply(417);
            echo $e->getMessage();
        }
        return json_encode($json_display);
    }



}