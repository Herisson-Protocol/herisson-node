<?php

namespace Herisson\Controller\Admin;

use Herisson\Repository\Screenshot;
use Herisson\Encryption;
use Herisson\Shell;
use Herisson\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class OptionController extends AbstractController
{

    /**
     * Constructor
     *
     * Sets controller's name
     */
    function __construct()
    {
        $this->name = "option";
        parent::__construct();
        $this->allowedoptions = array(
            'acceptFriends',
            'acceptBackups',
            'adminEmail',
            'backupFolderSize',
            'basePath',
            'bookmarksPerPage',
            'checkHttpImport',
            'convertPath',
            'debugMode',
            'screenshotTool',
            'search',
            'sitename',
            'spiderOptionFavicon',
            'spiderOptionFullPage',
            'spiderOptionScreenshot',
            'spiderOptionTextOnly',
        );
    }

    /**
     * Creates the options admin page and manages the update of options.
     * 
     * This is the default Action
     *
     * @return void
     */
    function indexAction()
    {

        if (post('action') == 'index') {
            $options = get_option('HerissonOptions');
            $new_options = array();
            foreach ($this->allowedoptions as $option) {
                $new_options[$option] = post($option);
            }
            $complete_options = array_merge($options, $new_options);
            if (!array_key_exists('privateKey', $complete_options)) {
                $encryption = Encryption::i()->generateKeyPairs();
                $complete_options['publicKey'] = $encryption->public;
                $complete_options['privateKey'] = $encryption->private;
                Message::i()->addError("<b>Warning</b> : public/private keys have been regenerated");
            }
            update_option('HerissonOptions', $complete_options);
        }

        // Check binaries paths
        $binaryTools = array(
            'convert',
            'wget',
            'du',
            'mv',
            'uname',
        );
        sort($binaryTools);
        $this->view->binaries = array();
        foreach ($binaryTools as $binary) {
            $this->view->binaries[$binary] = Shell::getPath($binary);
        }

        $this->view->platform = Shell::shellExec('uname', '-a');

        $this->view->screenshots = ScreenshotRepository::getAll();
        $this->view->options = get_option('HerissonOptions');

    }

}


