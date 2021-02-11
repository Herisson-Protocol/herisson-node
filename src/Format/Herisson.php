<?php

namespace Herisson\Format;

use Herisson\Export;
use Herisson\Entity\Bookmark;
use Herisson\Format;


class Herisson extends Format
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->name     = "Herisson (Complete format)";
        $this->type     = "file";
        $this->keyword  = "herisson";
        $this->filename = "herisson-bookmarks.json";
    }

    /**
     * Export bookmarks and send it to the user
     *
     * @param array $bookmarks a bookmarks array, made of Bookmarks item
     *
     * @return void
     */
    public function export($bookmarks)
    {
        Export::forceDownloadContent($this->exportData($bookmarks), $this->filename);
    }

    /**
     * Generate JSON bookmarks file
     *
     * @param array $bookmarks a bookmarks array, made of Bookmarks item
     *
     * @see Bookmark
     *
     * @return void
     */
    public function exportData($bookmarks)
    {
        $list = array();
        foreach ($bookmarks as $bookmark) {
            $list[] = $bookmark->toArray();
        }
        return json_encode($list);
    }


    /**
     * Handle the importation of JSON Herisson bookmarks
     *
     * Redirects to importList() to help the user decide which bookmarks to import
     *
     * @return void
     */
    function import()
    {
        $this->preImport();

        $bookmarks = json_decode($this->getFileContent(), 1);

        return $bookmarks;

    }



}


