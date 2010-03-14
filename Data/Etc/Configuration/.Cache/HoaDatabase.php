<?php

/**
 * Generated the 2010-03-14T17:03:52.000000Z
 */

return array (
  'keywords' => 
  array (
    'base' => NULL,
    'table' => NULL,
    'field' => NULL,
    'schema' => NULL,
  ),
  'parameters' => 
  array (
    'base.class' => '(:base:U:)Base',
    'base.file' => '_(:base:U:).php',
    'base.directory' => 'hoa://Data/Etc/Database/Model/(:base:U:)/',
    'table.class' => '(:table:U:)Table',
    'table.file' => '(:table:U:).php',
    'table.primaryKey' => 'Pk(:table:U:)',
    'table.foreignKey' => 'Fk(:field:U:)',
    'collection.class' => '(:table:U:)Collection',
    'cache.enable' => true,
    'cache.file.table' => '(:table:U:)Table.cache',
    'cache.file.query' => '(:table:U:)Query.cache',
    'cache.directory' => 'hoa://Data/Var/Private/Database/Cache/(:base:U:)/',
    'constraint.method' => 'user(:field:U:)Constraint',
    'schema.file' => '(:schema:U:).xml',
    'schema.directory' => 'Data/Database/Schema/',
    'connection.list.default.dal' => 'Pdo',
    'connection.list.default.dsn' => 'mysql:host=localhost;dbname=foobar',
    'connection.list.default.username' => 'root',
    'connection.list.default.password' => '',
    'connection.list.default.options' => true,
    'connection.autoload' => NULL,
  ),
);