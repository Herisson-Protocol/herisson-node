<?php

namespace Herisson\Service\Protocol;


use Doctrine\ORM\EntityManager;
use Herisson\Entity\Friend;
use Herisson\Entity\Site;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Encryption\EncryptionException;
use Herisson\Service\Message;
use Herisson\Service\Network\AbstractGrabber;
use Herisson\Service\Network\GrabberCurl;
use Herisson\Service\Network\GrabberInterface;
use Herisson\Service\Network\Response;
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
     * @var GrabberInterface
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
     * @param AbstractGrabber $grabber
     * @param Message $message
     */
    public function __construct(OptionLoader $optionLoader, Encryptor $encryptor, GrabberInterface $grabber, Message $message)
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
     * @throws ProtocolException
     * @throws \Herisson\Service\Encryption\EncryptionException
     * @throws \Herisson\Service\Network\NetworkException
     */
    public function askForFriend(Site $site, Friend $friend) : Response
    {
        $askUrl = $friend->getActionUrl(static::ASK_PATH);
        $mysite = $site->getFullSitepath();
        $signature = $this->encryptor->privateEncrypt($mysite, $site->privateKey); //options['privateKey']);

        $postData = [
            'url'       => $mysite,
            'signature' => $signature
        ];
        $response = $this->grabber->getResponse($askUrl, $postData);
        $code = $response->getStatusCode();

        //error_log("Response code : $code");
        switch ($code) {
            case 200:
                $friend->setIsValidatedByUs(true);
                break;
            case 202:
                $friend->setIsValidatedByUs(true);
                $friend->setIsValidatedByHim(true);
                break;
            case 403:
                $this->message->addError("This site refuses new friends.");
                break;
            case 404:
                $this->message->addError("This site is not a Herisson site or is closed.");
                break;
            case 417:
                $this->message->addError("Friend say you dont communicate correctly (key problems?).");
                break;
            default:
                throw new ProtocolException("Unknown code $code");
        }
        return $response;
        /*
        $dispath = [
            200 => [$this, "pendingForFriendsValidation", $friend],
            202 => [$this, "friendValidateOurRequest", $friend],
            403 => [$this->message, "addError", "This site refuses new friends."],
            404 => [$this->message, "addError", "This site is not a Herisson site or is closed."],
            417 => [$this->message, "addError", "Friend say you dont communicate correctly (key problems?)."],

        ];
        dump($response);
        $this->dispatchResponse($response, $dispath);
        */
    }

    /**
     * Ask a site for friend
     *
     * Do network hit to the friend's url and add it to our friends list
     *
     * @param Friend $friend
     * @return void
     * @throws ProtocolException
     * @throws \Herisson\Service\Encryption\EncryptionException
     * @throws \Herisson\Service\Network\NetworkException
     */
    public function reloadPublicKey(Friend $friend)
    {
        $publicKeyUrl = $friend->getActionUrl(static::PUBLICKEY_PATH);
        $response = $this->grabber->getResponse($publicKeyUrl);

        $code = $response->getStatusCode();
        switch ($code) {
            case 200:
                $friend->setPublicKey($response->getContent());
                break;
            case 404:
                $this->message->addError("This site is not a Herisson site or is closed.");
                break;
            default:
                throw new ProtocolException("Unknown code $code");
        }
    }


    /**
     * Get info data from a friend
     *
     * Do a network hit to retrieve friend's info
     *
     * @param Friend $friend
     * @return void
     * @throws ProtocolException
     */
    public function getInfo(Friend $friend)
    {
        $infoUrl = $friend->getActionUrl(static::INFO_PATH);
        $response = $this->grabber->getResponse($infoUrl);

        $code = $response->getStatusCode();
        switch ($code) {
            case 200:
                $jsonString = $response->getContent();
                $json = json_decode($jsonString);
                $friend->setEmail($json->{Site::PARAM_EMAIL});
                $friend->setName($json->{Site::PARAM_SITENAME});
                break;
            case 404:
                $friend->setIsActive(false);
                $this->message->addError("This site is not a Herisson site or is closed.");
                break;
            default:
                throw new ProtocolException("Unknown code $code");
        }

    }


    /**
     * Validate a friend
     *
     * Do network hit to the friend's url and validate it's request
     *
     * @return bool true if validation was succesful, false otherwise
     */
    public function autorizeFriendRequest(Site $site, Friend $friend)
    {
        $validateUrl = $friend->getActionUrl(static::VALIDATE_PATH);
        $response = $this->grabber->getResponse($validateUrl);

        $signature = $this->encryptor->privateEncrypt($site->getFullSitepath(), $site->privateKey);
        $postData = array(
            'url'       => $site->getFullSitepath(),
            'signature' => $signature
        );
        switch ($response->getStatusCode()) {
            case 200:
                //$friend->setIsValidatedByHim(true);
                $friend->setIsValidatedByUs(true);
                break;
            default:
                break;
        }
    }


    /**
     * Validate a friend
     *
     * Do network hit to the friend's url and validate it's request
     *
     * @return Response
     */
    public function handleFriendValidation(Site $site, Friend $friend, string $signature)
    {
        $this->reloadPublicKey($friend);
        try {
            $uncipheredUrl = $this->encryptor->publicDecrypt($signature, $friend->getPublicKey());
        } catch (EncryptionException $e) {
            return new Response("", 417, []);
        }

        if ($friend->getUrl() == $uncipheredUrl) {
            $friend->setIsValidatedByUs(true);
            return new Response("", 200, []);
        } else {
            return new Response("", 417, []);
        }
    }


    /**
     * Validate a friend
     *
     * Do network hit to the friend's url and validate it's request
     *
     * @return bool true if validation was succesful, false otherwise
     */
    /*
    public function validateFriend(Encryptor $encryptor, Message $message)
    {
        $signature = $encryptor->privateEncrypt(HERISSON_LOCAL_URL);
        $postData = array(
            'url'       => HERISSON_LOCAL_URL,
            'signature' => $signature
        );
        $network = new GrabberCurl();
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
    */


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
        $network = new GrabberCurl();
        try {
            $content = $network->getResponse($this->url."/acceptsbackups", $postData);
            return intval($content['data']);
        } catch (\Herisson\Service\Network\NetworkException $e) {
            switch ($e->getStatusCode()) {
                case 403:
                    return 0;
                    break;
                case 406:
                    return 2;
                    break;
            }
            return $e->getStatusCode();
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
        $network = new GrabberCurl();
        try {
            $content = $network->download($this->url."/sendbackup", $postData);
            return intval($content['data']);
        } catch (\Herisson\Service\Network\NetworkException $e) {
            switch ($e->getStatusCode()) {
                case 417:
                    return 0;
                    break;
            }
            return $e->getStatusCode();
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
        $network = new GrabberCurl();
        try {
            $content = $network->download($this->url."/downloadbackup", $postData);

            // FIXME We should not have to use stripslashes here !!
            $encryptionData = unserialize(stripslashes($content['data']));

            $data = $encryptor->privateDecryptLongData($encryptionData['data'], $encryptionData['hash'], $encryptionData['iv']);
            return $data;

        } catch (\Herisson\Service\Network\NetworkException $e) {
            switch ($e->getStatusCode()) {
                case 417:
                    return 0;
                    break;
            }
            return $e->getStatusCode();
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
            $network = new GrabberCurl();
            $params['key'] = $my_public_key;
            try {

                $content = $network->download($this->url."/retrieve", $params);
                $encryption_data = json_decode($content['data'], true);
                $json_data = Encryption::i()->privateDecryptLongData($encryption_data['data'], $encryption_data['hash'], $encryption_data['iv']);
                $bookmarks = json_decode($json_data, 1);
                return $bookmarks;

            } catch (Network\Exception $e) {
                switch ($e->getStatusCode()) {
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
        } catch (\Herisson\Service\Encryption\EncryptionException $e) {
            AbstractGrabber::reply(417);
            echo $e->getMessage();
        }
        return json_encode($json_display);
    }



}