<?php
/**
 * Test environment
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */


require_once __DIR__."/../../../../wp-config.php";





$options = get_option('HerissonOptions');
define("HERISSON_URL", get_option('siteurl')."/".$options['basePath']);


