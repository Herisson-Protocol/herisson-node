<?php

namespace Herisson\Format;

use Herisson\Export;
use Herisson\Entity\Bookmark;
use Herisson\Format;

/**
 * @see     https://github.com/Rivsen/firefox-json-boomark-read
 * @see     http://docs.services.mozilla.com/sync/objectformats.html
 */
class FirefoxJson extends Format
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->name     = "JSON (Firefox format)";
        $this->type     = "file";
        $this->keyword  = "firefox_json";
        $this->filename = "herisson-bookmarks.json";
    }

    /**
     * Export bookmarks and send it to the user
     *
     * @param array $bookmarks a bookmarks array, made of Bookmarks item
     *
     * @see Bookmark
     *
     * @return void
     */
    public function export($bookmarks)
    {
        Export::forceDownloadContent($this->exportData($bookmarks), $this->filename);
    }


    /**
     * Generate JSON bookmarks data
     *
     * @param array $bookmarks a bookmarks array, made of Bookmarks item
     *
     * @see Bookmark
     *
     * @return void
     */
    public function exportData($bookmarks)
    {
        $root = array(
            'title' => 'Herisson-export-'.date('Y-m-d'),
            'type' => 'text/x-moz-place-container',
            'children' => array(),
        );

        foreach ($bookmarks as $bookmark) { 
            $root['children'][] = array(
                'title' => $bookmark->title,
                'uri'   => $bookmark->url,
                'type'  => 'text/x-moz-place',
                );
        }
        return json_encode($root);
    }

    /**
     * Handle the importation of Firefox JSON bookmarks
     *
     * @return a list of Bookmark
     */
    public function import()
    {
        $this->preImport();
        $items = json_decode($this->getFileContent(), true);

        $bookmarks = array();
        if (isset($items['children'])) {
            $this->_parse($items['children'], $bookmarks);
        }

        return $bookmarks;
    }


    /**
     * Recursively parse items and children items to get text/x-moz-place bookmarks only
     *
     * Feed the $bookmarks passed in referenced
     *
     * @param array $items      json item to parse looking for bookmarks
     * @param array &$bookmarks array to fill with found bookmarks
     *
     * @return void
     */
    private function _parse($items, &$bookmarks)
    {
        foreach ($items as $item) {
            if (isset($item['type'])
                && isset($item['uri'])
                && !preg_match('#^place:#', $item['uri'])
                && $item['type'] == 'text/x-moz-place') {

                $bookmark = new Bookmark();
                $bookmark->url = $item['uri'];
                $bookmark->title = $item['title'];
                $bookmarks[] = $bookmark;

            }
            if (isset($item['children'])) {
                $this->_parse($item['children'], $bookmarks);
            }
        }
    }


}


