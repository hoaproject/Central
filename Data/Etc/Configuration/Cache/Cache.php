<?php

/**
 * Generated the 2009-11-02T20:48:48.000000Z
 */

return array (
  'frontend' => 
  array (
    'lifetime' => 3600,
    'serialize_content' => true,
    'make_id_with' => 
    array (
      'get' => true,
      'post' => true,
      'cookie' => true,
      'session' => true,
      'files' => true,
    ),
  ),
  'backend' => 
  array (
    'cache_directory' => '/tmp/',
    'compress' => 
    array (
      'active' => true,
      'level' => true,
    ),
    'database' => 
    array (
      'host' => '127.0.0.0',
      'port' => 11211,
      'persistent' => true,
    ),
  ),
);