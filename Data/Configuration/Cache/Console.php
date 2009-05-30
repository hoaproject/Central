<?php 

/**
 * Generated the 2009-05-30T20:02:13.000000Z.
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