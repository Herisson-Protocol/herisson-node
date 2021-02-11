<?php
/**
 * Backup controller 
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */

namespace Herisson\Controller\Admin;

use Doctrine_Query;
use Herisson\Doctrine;
use Herisson\Export;
use Herisson\Message;
use Herisson\Entity\Backup;
use Herisson\Repository\Backup;
use Herisson\Repository\Bookmark;
use Herisson\Repository\Friend;


/**
 * Class: HerissonControllerAdminBackup
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */
class Backup extends \Herisson\Controller\Admin
{

    /**
     * Constructor
     *
     * Sets controller's name
     */
    function __construct()
    {
        $this->name = "backup";
        parent::__construct();
    }


    /**
     * Add a new backup
     *
     * Start a backup of all bookmarks to a remote friend
     *
     * @return void
     */
    function addAction()
    {
        $friend = FriendRepository::get(post('friend_id'));
        if (! $friend->id) {
            Message::i()->addError("Friend could not be found");
            $this->indexAction();
            $this->setView('index');
            return;
        }

        $acceptsBackups = $friend->acceptsBackups();
        switch ($acceptsBackups) {
        case 0:
            Message::i()->addError("Friend doesn't accept backups");
            $this->indexAction();
            $this->setView('index');
            return;
        case 1:
            Message::i()->addSucces("Friend accepts backups");
            break;
        case 2:
            Message::i()->addError("Friend backup directory is full");
            $this->indexAction();
            $this->setView('index');
            return;
        }

        $bookmarks = BookmarkRepository::getAll();
        include_once HERISSON_BASE_DIR."/Herisson/Format/Herisson.php";
        $format = new \Herisson\Format\Herisson();
        $herissonBookmarks = $format->exportData($bookmarks);
        //print_r($herissonBookmarks);
        $res = $friend->sendBackup($herissonBookmarks);
        //echo $res;
        if ($res) {
            // TODO : Delete backups from that friend before adding a new one
            $backup            = new Backup();
            $backup->friend_id = $friend->id;
            $backup->size      = strlen($herissonBookmarks);
            $backup->nb        = sizeof($bookmarks);
            $backup->creation  = date('Y-m-d H:i:s');
            $backup->save();
        }


        // Redirects to Backups list
        $this->indexAction();
        $this->setView('index');
    }


    /**
     * Download a backup from friend
     *
     * @return void
     */
    private function _retrieve()
    {

        $friend = FriendRepository::get(get('id'));
        if (! $friend->id) {
            Message::i()->addError("Friend could not be found");
            $this->indexAction();
            $this->setView('index');
            return;
        }

        return $friend->downloadBackup();

    }


    /**
     * Download a backup from a friend
     *
     * @return void
     */
    function downloadAction()
    {
        $data         = $this->_retrieve();
        $this->layout = false;
        // FIXME This fails
        Export::forceDownloadContent($data, 'herisson.tar.gz');

    }


    /**
     * Download and import a backup from a friend
     *
     * @return void
     */
    function importAction()
    {
        $data       = $this->_retrieve();
        $bookmarks  = json_decode($data, 1);
        $controller = new \Herisson\Controller\Admin\Import();
        $controller->importList($bookmarks);
        $controller->route();
    }


    /**
     * Action to list existing backups
     *
     * This is the default action
     *
     * @return void
     */
    function indexAction()
    {
        $this->view->backups = \Doctrine_Query::create()
            ->from('Herisson\Entity\Backup b')
            ->execute();

        $this->view->localbackups = \Doctrine_Query::create()
            ->from('Herisson\Entity\Localbackup b')
            ->execute();

        $this->view->nbBookmarks   = BookmarkRepository::countAll();
        $this->view->sizeBookmarks = BookmarkRepository::getTableSize();
        
        $friends = \Doctrine_Query::create()
            ->from('Herisson\Entity\Friend f')
            ->orderby('name')
            ->execute();
        $this->view->friends = array();
        foreach ($friends as $friend) {
            $this->view->friends[$friend->id] = $friend;
        }
    }

}


