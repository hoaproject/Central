<?php 

/**
 * Generated the 2008-10-31T01:36:25.000000Z.
 */

return array (
  'route' => 
  array (
    'type' => 'Get',
    'parameter' => 
    array (
      'default' => 
      array (
        'module' => 'index',
        'action' => 'index',
      ),
    ),
    'controller' => 
    array (
      'key' => 'module',
    ),
    'action' => 
    array (
      'key' => 'action',
    ),
    'directory' => 'Application/Controller/',
  ),
  'view' => 
  array (
    'theme' => 'MyTheme',
    'directory' => 'Application/View/(:Theme)/',
    'layout' => 'Front',
    'enable' => 
    array (
      'layout' => true,
    ),
    'helper' => 
    array (
      'directory' => 'Application/View/Helper/',
    ),
  ),
  'model' => 
  array (
    'directory' => 'Application/Model/(:Controller)/',
  ),
  'pattern' => 
  array (
    'controller' => 
    array (
      'class' => '(:Controller)Controller',
      'file' => '(:Controller)Controller.php',
      'directory' => '(:Controller)Controller/',
    ),
    'action' => 
    array (
      'class' => '(:Action)Controller',
      'method' => '(:Action)Action',
      'file' => '(:Action)Controller.php',
    ),
    'view' => 
    array (
      'layout' => '(:Layout)Layout.phtml',
      'file' => '(:Action)Action.phtml',
      'directory' => '(:Controller)View/',
    ),
    'model' => 
    array (
      'directory' => '(:Controller)Model/',
    ),
  ),
);