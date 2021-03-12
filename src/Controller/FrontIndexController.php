<?php

namespace Herisson\Controller;

use Herisson\Entity\Friend;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Network\AbstractGrabber;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Herisson\Repository\BookmarkRepository;
use Herisson\Service\OptionLoader;
use Herisson\Service\Protocol\ProtocolException as ProtocolException;
use Herisson\Service\Encryption\EncryptionException as EncryptionException;

class FrontIndexController extends AbstractController
{

    public $optionLoader;
    public $encryptionService;

    public function __construct(OptionLoader $optionLoader, Encryptor $encryptionService)
    {
        $this->optionLoader = $optionLoader;
        $this->encryptionService = $encryptionService;
    }
    /**
     * Action to display homepage of Herisson site
     *
     * @Route("/front/index/", name="front.index")
     */
    public function indexAction(BookmarkRepository $bookmarkRepository): Response
    {

        if ($tag = false) {
            $bookmarks = BookmarkRepository::getTag(array($tag),true);
        } else if ($search = false) {
            $bookmarks = BookmarkRepository::getSearch($search,true);
        } else {
        }
        $bookmarks = $bookmarkRepository->findAll();



        //$this->view->title = $this->options['sitename'];

        /*
        $this->view->friends = FriendRepository::getWhere("is_active=1");
        foreach ($this->view->friends as $friendId => $friend) {
            $this->view->friendBookmarks[$friend->id] = $friend->retrieveBookmarks($_GET);

        }
         */

        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontIndexController',
            'bookmarks' => $bookmarks,
            'sitename' => 'Wilkins',
        ]);

    }


    /**
     * Action to handle the accepsBackups requests
     *
     * Handled via HTTP Response code
     *
     * TODO: Handle Grabber replies as Exceptions
     *
     * @return void
     */
    function acceptsbackupsAction()
    {
        $this->_checkBackup();

        echo "1";

    }


    /**
     * Action to handle the sendBackup requests
     *
     * Handled via HTTP Response code
     *
     * @return void
     */
    function sendbackupAction()
    {

        // TODO : Check ini_get(post_max_size)
        $this->_checkBackup();
        
        $signature  = post('signature');
        $url        = post('url');
        $backupData = post('backupData');
        //error_log($backupData);

        $friend = FriendRepository::getOneWhere("url=?", array($url));
        try {
            if ($this->encryptionService->publicDecrypt($signature, $friend->public_key) == $url) {

                // Save the file in backup folder
                $filename = hash('md5', $backupData).".data";
                $fullfilename = HERISSON_BACKUP_DIR."/".$filename;
                file_put_contents($fullfilename, $backupData);
                
                // Insert localbackup into
                $backup            = new Localbackup();
                $backup->friend_id = $friend->id;
                $backup->size      = strlen($backupData);
                $backup->filename  = $fullfilename;
                $backup->creation  = date('Y-m-d H:i:s');
                $backup->save();
                
                AbstractGrabber::reply(200);
                echo "1";
                exit;
            } else {
                AbstractGrabber::reply(417, HERISSON_EXIT);
            }
        } catch (EncryptionException $e) {
            AbstractGrabber::reply(417, HERISSON_EXIT);

        }

    }



    /**
     * Action to handle the sendBackup requests
     *
     * Handled via HTTP Response code
     *
     * @return void
     */
    function downloadbackupAction()
    {

        $signature  = post('signature');
        $url        = post('url');

        $friend = FriendRepository::getOneWhere("url=?", array($url));
        try {
            if ($this->encryptionService->publicDecrypt($signature, $friend->public_key) == $url) {

                $backup = LocalbackupRepository::getOneWhere('friend_id=?', array($friend->id));
                Export::forceDownload($backup->filename, 'herisson.data');

            } else {
                AbstractGrabber::reply(417, HERISSON_EXIT);
            }
        } catch (EncryptionException $e) {
            AbstractGrabber::reply(417, HERISSON_EXIT);

        }

    }


    /**
     * Checks wether this site accept backups, and if there is enough rooms left
     *
     * Handled via HTTP Response code
     *
     * @return void
     */
    private function _checkBackup()
    {
        
        if ($this->options['acceptBackups'] == 0) {
            AbstractGrabber::reply(403, HERISSON_EXIT);
            exit;
        }

        $dirsize = Folder::getFolderSize(HERISSON_BACKUP_DIR);
        if ($dirsize > $this->options['backupFolderSize']) {
            AbstractGrabber::reply(406, HERISSON_EXIT);
            exit;
        }

    }


    /**
     * Action to handle the ask from another site
     *
     * Handled via HTTP Response code
     *
     * TODO: Handle Grabber replies as Exceptions
     *
     * @Route("/front/ask/", name="front.ask")
     *
     * @return void
     */
    function askAction(Request $request)
    {
        $options = $this->optionLoader->load(['acceptFriends']);
        if ($options['acceptFriends'] == 0) {
            throw new ProtocolException(403);
            //Grabber::reply(403, HERISSON_EXIT);
        }
        dump($request);
        $signature = $request->request->get('signature');
        $url = $request->request->get('url');
        $url = 'http://localhost:8001';
        $f = new Friend();
        $f->setUrl($url);
        $f->reloadPublicKey();
        if ($this->encryptionService->publicDecrypt($signature, $f->getPublicKey()) == $f->getUrl()) {
            $f->getInfo();
            if ($options['acceptFriends'] == 2) {
                // Friend automatically accepted, so it's a 202 Accepted for further process response
                AbstractGrabber::reply(202);
                $f->setIsActive(true);
            } else {
                // Friend request need to be manually processed, so it's a 200 Ok response
                AbstractGrabber::reply(200);
                $f->setIsWantsyou(true);
                $f->setIsActive(false);
            }
            $f->save();
        } else {
            AbstractGrabber::reply(417, HERISSON_EXIT);
        }
        exit;
    }



    /**
     * Action to display Herisson site informations
     *
     * This is mandatory for Herisson protocol
     * Outputs JSON
     *
     * @Route("/info", name="front.info")
     * @return void
     */
    function infoAction() : Response
    {
        $visibleOptions = ['sitename', 'adminEmail', 'version', 'protocol-version'];

        $options = $this->optionLoader->load($visibleOptions);

        return $this->render('front/info.html.twig', [
            'controller_name' => 'FrontIndexController',
            'options' => json_encode($options),
        ]);
    }


    /**
     * Action to display Herisson site public key
     *
     * This is mandatory for Herisson protocol
     * Outputs Text
     * @Route("/publicKey", name="front.publicKey")
     * @return void
     */
    function publicKeyAction()
    {
        $visibleOptions = ['publicKey'];

        $options = $this->optionLoader->load($visibleOptions);

        return $this->render('front/publicKey.html.twig', [
            'controller_name' => 'FrontIndexController',
            'options' => $options,
        ]);
    }


    /**
     * Action to send all the bookmarks data to a known friend
     *
     * This methods check the given publickey
     * Outputs JSON
     * @Route("/front/retrieve", name="front.retrieve")
     * @return void
     */
    function retrieveAction()
    {
        if (!sizeof($_POST)) {
            exit;
        }
        $key = post('key');
        $friends = FriendRepository::getWhere("public_key=?", array($key));
        foreach ($friends as $friend) {
            echo $friend->generateBookmarksData($_POST);
            // Exit au cas ou le friend est présent plusieurs fois
            exit;
        }
    }


    /**
     * Action to handle validation of a pending request for friendship.
     *
     * Handled via HTTP Response code
     *
     * @return void
     */
    function validateAction()
    {

        $signature = post('signature');
        $url = post('url');

        $f = FriendRepository::getOneWhere("url=? AND b_youwant=1", array($url));
        try {
            if ($this->encryptionService->publicDecrypt($signature, $f->public_key) == $url) {
                $f->b_youwant=0;
                $f->is_active=1;
                $f->save();
                AbstractGrabber::reply(200);
                echo "1";
                exit;
            } else {
                AbstractGrabber::reply(417, HERISSON_EXIT);
            }
        } catch (EncryptionException $e) {
            AbstractGrabber::reply(417, HERISSON_EXIT);

        }
    }

}
