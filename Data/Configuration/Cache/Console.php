<?php 

/**
 * Generated the 2009-09-01T18:34:33.000000Z.
 */

return array (
);<?php 

/**
 * Generated the 2009-09-01T18:34:33.000000Z.
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