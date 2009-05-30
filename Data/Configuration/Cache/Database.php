<?php 

/**
 * Generated the 2009-05-30T20:02:13.000000Z.
 */

return array (
  'base' => 
  array (
    'classname' => '(:Base)Base',
    'filename' => '_(:Base).php',
    'directory' => 'Data/Database/Model/(:Base)/',
  ),
  'table' => 
  array (
    'classname' => '(:Table)Table',
    'filename' => '(:Table).php',
    'pkname' => 'Pk(:Table)',
    'fkname' => 'Fk(:Field)',
  ),
  'collection' => 
  array (
    'classname' => '(:Table)Collection',
  ),
  'cache' => 
  array (
    'enable' => true,
    'filename' => 
    array (
      'table' => '(:Table).cache',
      'query' => '(:Table)Query.cache',
    ),
    'directory' => 'Data/Database/Cache/(:Base)/',
  ),
  'constraint' => 
  array (
    'methodname' => 'user(:Field)Constraint',
  ),
  'schema' => 
  array (
    'filename' => '(:Schema).xml',
    'directory' => 'Data/Database/Schema/',
  ),
  'connection' => NULL,
);