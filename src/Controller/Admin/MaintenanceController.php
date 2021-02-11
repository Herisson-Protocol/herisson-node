<?php


namespace Herisson\Controller\Admin;

use Herisson\Repository\BookmarkRepository;
use Herisson\Entity\Bookmark;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MaintenanceController extends AbstractController
{



    /**
     * Display import and maintenance options page
     *
     * This is the default Action
     *
     * @return void
     */
    function indexAction()
    {
        if (post('maintenance')) {
            $condition = "
                LENGTH(favicon_url)=?   or favicon_url is null or
                LENGTH(favicon_image)=? or favicon_image is null or
                LENGTH(content)=?       or content is null or
                LENGTH(content_image)=? or content_image is null";

            $bookmarks_errors   = BookmarkRepository::getWhere($condition, array(0, 0, 0, 0));
            foreach ($bookmarks_errors as $b) {
                $b->maintenance(false);
                //$b->captureFromUrl();
                $b->save();
            }
        }


        // TODO Check for correct backups

        $bookmarks         = BookmarkRepository::getAll();
        $this->view->total = sizeof($bookmarks);
        $favicon           = BookmarkRepository::getWhere("LENGTH(favicon_image)=?   or favicon_image is null", array(0));
        $html_content      = BookmarkRepository::getWhere("LENGTH(content)=?         or content is null", array(0));
        $full_content      = BookmarkRepository::getWhere("LENGTH(content)=?         or content is null", array(0));
        $screenshot        = BookmarkRepository::getWhere("LENGTH(content_image)=?   or content_image is null", array(0));
        $this->view->stats = array(
            'favicon'           => sizeof($favicon),
            'html_content'      => sizeof($html_content),
            'full_content'      => sizeof($full_content),
            'screenshot'        => sizeof($screenshot),
        );

    }


}


