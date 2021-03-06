<?php

namespace Herisson\Format;

use Herisson\Entity\Bookmark;
use Herisson\Export;
use Herisson\Format;


class Csv extends Format
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->name      = "CSV (Basic format)";
        $this->type      = "file";
        $this->keyword   = "csv";
        $this->filename  = "herisson-bookmarks.csv";
        $this->delimiter = ';';
        $this->columns   = array(
            'title',
            'url',
        );
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
     * Generate CSV bookmarks file
     *
     * @param array $bookmarks a bookmarks array, made of Bookmarks item
     *
     * @see Bookmark
     *
     * @return void
     */
    public function exportData($bookmarks)
    {
        $filename = tempnam('/tmp/', 'csv');
        $fcsv  = fopen($filename, 'w+');

        //headers
        $line = array();
        foreach ($this->columns as $col) {
            $line[] = $col;
        }
        fputcsv($fcsv, $line, $this->delimiter);

        //bookmark lines
        foreach ($bookmarks as $bookmark) {
            $line = array();
            foreach ($this->columns as $col) {
                $line[] = $bookmark->{$col};
            }
            fputcsv($fcsv, $line, $this->delimiter);
        }
        fclose($fcsv);
        $content = file_get_contents($filename);
        unlink($filename);
        return $content;
    }


    /**
     * Handle the importation of CSV files
     *
     * @return a list of Bookmark
     */
    public function import()
    {
        $this->preImport();

        $fh        = fopen($this->getFilename(), 'r');
        $headers   = fgetcsv($fh, 0, $this->delimiter);
        $bookmarks = array();

        while (($line = fgetcsv($fh, 0, $this->delimiter)) !== false) {
            $bookmark = new Bookmark();
            //print_r($bookmark->toArray());
            foreach ($headers as $fieldNum => $header) {
                if (isset($bookmark->$header) && array_key_exists($fieldNum, $line)) {
                    $bookmark->$header = $line[$fieldNum];
                } else {
                    throw new Exception(__("Unknown column definition « $header » on the first line.", HERISSON_TD));
                }
            }
            $bookmarks[] = $bookmark;
        }
        fclose($fh);
        return $bookmarks;
    }


}


