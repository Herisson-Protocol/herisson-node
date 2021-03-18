<?php


namespace Herisson\Service\System;


use Herisson\Entity\Bookmark;
use Herisson\Shell;

class SaverFilesystem implements SaverInterface
{

    public function save(Bookmark $bookmark): bool
    {
        /*
        $options = get_option('HerissonOptions');
        if (! $options['spiderOptionFullPage']) {
            return false;
        }
        */
        $directory = $bookmark->getDir();
        if ($bookmark->hasFullContent()) {
            return false;
        }
        $shell = new Shell();
        if ($this->createDir($directory)) {
            $shell->shellExec("wget",
                "-q --no-parent --timestamping --convert-links --page-requisites --no-directories --no-host-directories ".
                "-erobots=off -P $directory ".'"'.$bookmark->getUrl().'"');
            $this->calculateDirSize($directory);
            if ($bookmark->getUrlPath()) {
                $file = $bookmark->getUrlPath();
                $shell->shellExec("mv", "\"$directory/$file\" \"".$bookmark->getFullContentFile()."\"");
                return true;
                /*
                if ($verbose) {
                    Message::i()->addSucces(sprintf('<b>Downloading bookmark : <a href="%s">%s</a></b>',
                        "/wp-admin/admin.php?page=herisson_bookmarks&action=edit&id=".$this->id, $this->title));
                }
                */
            }
        }
        return false;
    }



    /**
     * Create the bookmark dir of the bookmark files
     *
     * @return boolean true if the directory was succesfully created, false otherwise
     */
    public function createDir($dir) : bool
    {
        if (!file_exists($dir)) {
            // Create dir recursively
            mkdir($dir, 0775, true);
            return true;
        } else if (file_exists($dir) && !is_dir($dir)) {
            //Message::i()->addError("Can't create directory $dir. A file already exists");
            return false;
        } else if (!is_writeable($dir)) {
            //Message::i()->addError("Directory $dir exists, but is not writable.");
            return false;
        }
        return true;
    }


    public function calculateDirSize($dir) : string
    {
        $shell = new Shell();
        $res = $shell->shellExec("du", " -b $dir");
        error_log($res);
        return $res;
    }

    public function getDataSize(Bookmark $bookmark): int
    {
        return $this->calculateDirSize($bookmark->getDir());
    }
}