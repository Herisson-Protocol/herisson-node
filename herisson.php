<?php


define('HERISSON_VERSION', '0.1');
define('HERISSON_DB', 1);
define('HERISSON_OPTIONS', 1);
define('HERISSON_REWRITE', 1);
define('HERISSON_TD', 'herisson');
define('HERISSON_BASE_DIR', ABSPATH."/wp-content/plugins/herisson/");
define('HERISSON_WP_BASE_DIR', ABSPATH);
define('HERISSON_INCLUDES_DIR', HERISSON_BASE_DIR.'includes/');
define('HERISSON_VENDOR_DIR', HERISSON_BASE_DIR.'vendor/');
define('HERISSON_TEMPLATES_DIR', HERISSON_BASE_DIR.'templates/');
define('HERISSON_ADMIN_DIR', HERISSON_BASE_DIR.'admin/');
define('HERISSON_LANG_DIR', HERISSON_BASE_DIR.'languages/');
define('HERISSON_DATA_DIR', HERISSON_BASE_DIR.'data/');
define('HERISSON_BACKUP_DIR', HERISSON_BASE_DIR.'backup/');
define('HERISSON_SCREENSHOTS_DIR', 'screenshots/');
define('HERISSON_MENU_SINGLE', 4);
define('HERISSON_MENU_MULTIPLE', 2);

define('HERISSON_EXIT', 1);

define('HERISSON_PLUGIN_URL', plugin_dir_url(__FILE__));



// Include other functionality
require_once HERISSON_INCLUDES_DIR . 'screenshots.php';


// Routing and Controller classes



// Initiate the database connexion
// with the same informations as the Wordpress installation.
define('HERISSON_DOCTRINE_DSN',
    'mysql://' . DB_USER . ':' . DB_PASSWORD . '@' . DB_HOST . '/' . DB_NAME);
$doctrine = new Herisson\Doctrine(HERISSON_DOCTRINE_DSN);
$doctrine->loadlibrary();


$options = get_option('HerissonOptions');
define("HERISSON_LOCAL_URL", get_option('siteurl')."/".$options['basePath']);

add_action('init', array('Herisson', 'init'));

add_action('init', array('Herisson', 'checkVersions'));
add_action('plugins_loaded', array('Herisson', 'checkVersions'));

register_activation_hook('herisson/herisson.php', array('Herisson', 'install'));


add_action('send_headers', array('Herisson', 'router'));

add_action('admin_menu', array('Herisson', 'addPages'));


    

