<?php
 
/**
 * We redefine the include path.
 */
set_include_path('./'            . PATH_SEPARATOR .
                 './Application' . PATH_SEPARATOR .
                 './Framework'   . PATH_SEPARATOR .
                 get_include_path());
 
/**
 * We call the main framework file.
 */
require_once 'Framework.php';
 
/**
 * We import the front controller.
 */
import('Controller.Front');
 
/**
 * We get an instance of the front controller,
 * and start to dispatch.
 */
Hoa_Controller_Front::getInstance()->dispatch();
