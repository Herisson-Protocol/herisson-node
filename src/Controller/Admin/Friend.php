<?php
/**
 * Friend controller 
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */

namespace Herisson\Controller\Admin;


use Herisson\Message;
use Herisson\Repository\Friend;
use Herisson\Entity\Friend;

/**
 * Class: Herisson\Controller\Admin\Friend
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */
class Friend extends \Herisson\Controller\Admin
{

    /**
     * Constructor
     *
     * Sets controller's name
     */
    function __construct()
    {
        $this->name = "friend";
        parent::__construct();
    }

    /**
     * Action to add a new friend
     *
     * Redirect to editAction()
     *
     * @return void
     */
    function addAction()
    {
        $this->setView('edit');
        $this->editAction();
    }

    /**
     * Action to approve a new friend
     *
     * Redirect to editAction()
     *
     * @return void
     */
    function approveAction()
    {
        $id = intval(param('id'));
        if ($id>0) {
            $friend = FriendRepository::get($id);
            if ($friend->validateFriend()) {
                Message::i()->addSucces("Friend has been notified of your approvement");
            } else {
                Message::i()->addError("Something went wrong while adding friendFriend has been notified of your approvement");
            }
        }
        // Redirect to Friends list
        $this->indexAction();
        $this->setView('index');
    }

    /**
     * Action to delete a friend
     *
     * Redirect to indexAction()
     *
     * @return void
     */
    function deleteAction()
    {
        $id = intval(param('id'));
        if ($id>0) {
            $friend = FriendRepository::get($id);
            $friend->delete();
        }

        // TODO delete related backups and localbackups

        // Redirect to Friends list
        $this->indexAction();
        $this->setView('index');
    }

    /**
     * Action to edit a friend
     *
     * If POST method used, update the given friend with the POST parameters,
     * otherwise just display the friend properties
     *
     * @return void
     */
    function editAction()
    {
        $id = intval(param('id'));
        if (!$id) {
            $id = 0;
        }
        if (sizeof($_POST)) {
            $url = post('url');
            $alias = post('alias');

            $new = $id == 0 ? true : false;
            if ($new) {
                $friend = new Friend();
                $friend->is_active = 0;
            } else {
                $friend = FriendRepository::get($id);
            }

            $friend->alias = $alias;
            $friend->url = $url;
            if ($new) {
                $friend->getInfo();
                $friend->askForFriend();
            }
            $friend->save();
            if ($new) { 
                if ($new && $friend->is_active) {
                    Message::i()->addSucces("Friend has been added and automatically validated");
                } else {
                    Message::i()->addSucces("Friend has been added, but needs to be validated by its owner");
                }
                // Return to list after creating new friend.
                $this->indexAction();
                $this->setView('index');
                return;
            } else {
                Message::i()->addSucces("Friend saved");
            }
        }

        if ($id == 0) {
            $this->view->existing = new Friend();
        } else {
            $this->view->existing = FriendRepository::get($id);
        }
        $this->view->id = $id;
    }

    /**
     * Action to list friends
     *
     * This is the default action
     *
     * @return void
     */
    function indexAction()
    {
        $this->view->actives  = FriendRepository::getWhere("is_active=1");
        $this->view->youwant  = FriendRepository::getWhere("b_youwant=1");
        $this->view->wantsyou = FriendRepository::getWhere("b_wantsyou=1");
        $this->view->errors   = FriendRepository::getWhere("b_wantsyou!=1 and b_youwant!=1 and is_active!=1");
    }

    /**
     * Action to import friends
     *
     * Not implemented yet
     *
     * @return void
     */
    function importAction()
    {
        if ( !empty($_POST['login']) && !empty($_POST['password'])) {
        }
    }


}


