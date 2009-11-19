<?php

/**
 * Generated the 2009-11-19T01:15:55.000000Z
 */

return array (
  'keywords' => 
  array (
    'controller' => 'index',
    'action' => 'index',
    'view' => 'hend',
    'view.layout' => 'front',
  ),
  'parameters' => 
  array (
    'data.array' => 
    array (
    ),
    'data.array.personal' => NULL,
    'route.type' => 'Get',
    'route.parameter.default.module' => '(:controller:)',
    'route.parameter.default.action' => '(:action:)',
    'controller.class' => '(:controller:U:)Controller',
    'controller.file' => '(:controller:U:).php',
    'controller.directory' => 'hoa://Application/Controller/',
    'action.class' => '(:action:U:)Controller',
    'action.method' => '(:action:U:)Action',
    'action.file' => '(:action:U:).php',
    'action.directory' => '(:%controller.directory:)(:%controller.file:r:)/',
    'model.share.directory' => 'hoa://Application/Model/',
    'model.directory' => '(:%model.share.directory:)(:%controller.file:r:)/',
    'view.theme' => '(:view:U:)Theme',
    'view.directory' => 'hoa://Application/View/(:%view.theme:)/',
    'view.layout.file' => '(:view.layout:U:).phtml',
    'view.layout.enable' => true,
    'view.action' => '(:controller:U:)/(:action:U:).phtml',
  ),
);