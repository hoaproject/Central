<?php 

/**
 * Generated the 2008-10-31T01:36:25.000000Z.
 */

return array (
  'system' => 
  array (
    'group' => 
    array (
      'default' => 'Main',
    ),
    'command' => 
    array (
      'default' => 'Welcome',
    ),
  ),
  'route' => 
  array (
    'directory' => 'Command/',
    'grpcmd' => 
    array (
      'separator' => ':',
    ),
  ),
  'pattern' => 
  array (
    'group' => '(:Group)',
    'command' => 
    array (
      'name' => '(:Command)',
      'file' => '(:Command)',
      'class' => '(:Command)Command',
    ),
  ),
  'prompt' => 
  array (
    'prefix' => NULL,
    'symbol' => '> ',
  ),
  'cli' => 
  array (
    'longonly' => true,
  ),
  'interface' => 
  array (
    'style' => 
    array (
      'directory' => 'Style/',
    ),
  ),
  'command' => 
  array (
    'php' => 'php',
    'browser' => 'open',
  ),
);