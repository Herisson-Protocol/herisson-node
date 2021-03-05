<?php
/**
 * Herisson\Controller\Admin\BackupTest
 *
 * PHP Version 5.3
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */

namespace Herisson\Controller\Admin;

use Herisson\ControllerTest;

require_once __DIR__."/../../../Env.php";


/**
 * Class: Herisson\Controller\Admin\BackupTest
 * 
 * Test Herisson\Controller classes
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      PHPUnit_Framework_TestCase
 */
class BackupTest extends ControllerTest
{


    /**
     * Configuration
     *
     * Create sample data, and Encryptor object
     *
     * @return void
     */
    protected function setUp()
    {
        $this->controller = new Backup();
    }

    
    /**
     * Test index Action
     * 
     * @return void
     */
    public function testIndexAction()
    {
        $this->callAction('index');
    }


}

