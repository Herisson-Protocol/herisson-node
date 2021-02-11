<?php
/**
 * Bookmark controller 
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */

namespace Herisson\Controller\Admin;

use Herisson\Repository\Bookmark;
use Herisson\Entity\Bookmark;
use Herisson\Repository\Tag;
use Herisson\Pagination;


/**
 * Class: HerissonControllerAdminBookmark
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */
class Bookmark extends \Herisson\Controller\Admin
{

    /**
     * Constructor
     *
     * Sets controller's name
     */
    function __construct()
    {
        $this->name = "bookmark";
        parent::__construct();
    }

    /**
     * Action to add a new bookmark
     *
     * Redirects to editAction()
     *
     * @return void
     */
    function addAction()
    {
        $this->setView('edit');
        $this->editAction();
    }

    /**
     * Action to add delete a bookmark
     *
     * Redirects to indexAction()
     *
     * @return void
     */
    function deleteAction()
    {
        $id = intval(param('id'));
        if ($id>0) {
            $bookmark = BookmarkRepository::get($id);
            $bookmark->delete();
        }

        // Redirects to Bookmarks list
        $this->indexAction();
        $this->setView('index');
    }

    /**
     * Action to download a bookmark URL content
     *
     * Redirects to editAction()
     *
     * @return void
     */
    function downloadAction()
    {
        $id = intval(param('id'));
        if ($id>0) {
            $bookmark = BookmarkRepository::get($id);
            $bookmark->maintenance();

            $this->editAction();
            $this->setView('edit');
        }
    }

    /**
     * Action to edit a bookmark
     *
     * If POST method used, update the given bookmark with the POST parameters,
     * otherwise just display the bookmark properties
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
            $bookmark = BookmarkRepository::get($id);
            $bookmark->title = post('title');
            $bookmark->url = post('url');
            $bookmark->description = post('description');
            $bookmark->is_public = post('is_public');
            $bookmark->save();
            $id = $bookmark->id;
            $bookmark->maintenance();

            $tags = explode(',', post('tags'));
            $bookmark->setTags($tags);
        }

        if ($id == 0) {
            $this->view->existing = new Bookmark();
            $this->view->tags = array();
        } else {
            $this->view->existing = BookmarkRepository::get($id);
            $this->view->tags = $this->view->existing->getTagsArray();
        }
        $this->view->id = $id;
    }

    /**
     * Action to list bookmarks
     *
     * This is the default action
     *
     * @return void
     */
    function indexAction()
    {
        $tag = get('tag');
        if ($tag) {
            $this->view->subtitle = "Results for tag &laquo;&nbsp;".esc_html($tag)."&nbsp;&raquo;");
            $this->view->countAll = sizeof(BookmarkRepository::getTag($tag));
            $this->view->bookmarks = BookmarkRepository::getTag($tag, true);
        } else {
            $this->view->bookmarks = BookmarkRepository::getAll(true);
            $this->view->countAll = sizeof(BookmarkRepository::getAll());
        }
        $this->view->pagination = Pagination::i()->getVars();
    }

    /**
     * Action to display the tags list
     *
     * @return void
     */
    function tagCloudAction()
    {
        $this->view->tags = TagRepository::getAll();
        $this->layout = false;
    }

    /**
     * Action to search a keyword through bookmarks
     *
     * @return void
     */
    function searchAction()
    {
        $search = get('search');
        $this->view->bookmarks = BookmarkRepository::getSearch($search, true);
        $this->view->countAll = sizeof(BookmarkRepository::getSearch($search));
        $this->view->subtitle = "Search results for &laquo;&nbsp;".esc_html($search)."&nbsp;&raquo;");
        $this->view->pagination = Pagination::i()->getVars();
        $this->setView('index');
    }

    /**
     * Action to display a bookmark content
     *
     * @return void
     */
    function viewAction()
    {
        $id = intval(get('id'));
        if (!$id) {
            echo "Error : Missing id\n";
            exit;
        }
        $bookmark = BookmarkRepository::get($id);
        if ($bookmark && $bookmark->content) {
            echo $bookmark->content;
        } else {
            echo sprintf("Error : Missing content for bookmark %s\n", $bookmark->id);
        }
        exit;
    }



}


