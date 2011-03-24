<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * @category    Framework
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Schema
 *
 */

/**
 * Hoa_Database_Schema_Exception
 */
import('Database.Schema.Exception');

/**
 * Hoa_Database
 */
import('Database.~');

/**
 * Class Hoa_Database_Schema.
 *
 * Parse the schema and write the model.
 *
 * @author      Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan Enderlin.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Schema
 */

class Hoa_Database_Schema {

    /**
     * The XML object.
     *
     * @var Hoa_Database_Schema SimpleXMLElement
     */
    protected $xml = null;



    /**
     * Builth the XML.
     *
     * @access  public
     * @param   string  $file    XML file that contains the schema.
     * @return  void
     * @throw   Hoa_Database_Schema_Exception
     */
    public function __construct ( $file ) {

        $this->buildXml($file);
    }

    /**
     * Build the XML object (based on SimpleXML, must be loaded).
     *
     * @access  public
     * @param   string  $file    File that contains the schema (without
     *                           extension).
     * @return  void
     * @throw   Hoa_Database_Schema_Exception
     */
    protected function buildXml ( $file ) {

        $directory = Hoa_Database::getInstance()
                         ->getParameter('schema.directory', null, false);
        $file      = Hoa_Database::getInstance()
                         ->getParameter('schema.filename', $file);
        $file      = $directory . DS . $file;

        if(!file_exists($file))
            throw new Hoa_Database_Schema_Exception(
                'The file %s is not found.', 0, $file);

        if(!function_exists('simplexml_load_file'))
            throw new Hoa_Database_Schema_Exception(
                'SimpleXML is not loaded on this server.', 1);

        $this->xml = simplexml_load_file($file);
    }

    /**
     * Get the XML object.
     *
     * @access  protected
     * @return  SimpleXMLElement
     */
    protected function get ( ) {

        return $this->xml;
    }

    /**
     * Start to create folders, write files etc.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Database_Schema_Exception
     */
    public function process ( ) {

        $instance        = Hoa_Database::getInstance();
        $basePath        = $instance->getParameter('base.directory',       null, false);
        $baseFile        = $instance->getParameter('base.filename',        null, false);
        $baseName        = $instance->getParameter('base.classname',       null, false);
        $tableFile       = $instance->getParameter('table.filename',       null, false);
        $tableName       = $instance->getParameter('table.classname',      null, false);
        $tableCollection = $instance->getParameter('collection.classname', null, false);

        foreach($this->get() as $foo => $base) {

            $bname = $base['name'] . '';
            $path  = $instance->transform($basePath, $bname);

            if(is_dir($path))
                throw new Hoa_Database_Schema_Exception(
                    'The directory %s already exists. Remove it before process.',
                    2, $path);

            mkdir($path);

            $t = array();

            foreach($base as $foo => $table) {

                $tname = $table['name'] . '';
                $t[]   = $tname;
                $f     = array();

                foreach($table as $foo => $field) {

                    $fname = $field['name'] . '';

                    foreach($field->attributes() as $key => $value)
                        switch($key) {

                            case 'null':
                                $f[$fname]['isNull']        = $value . '';
                              break;

                            case 'increment':
                                $f[$fname]['autoIncrement'] = $value . '';
                              break;

                            case 'charset':
                                $f[$fname]['charSet']       = $value . '';
                              break;

                            case 'charcollate':
                                $f[$fname]['charCollate']   = $value . '';
                              break;

                            default:
                                $f[$fname][$key] = $value . '';
                        }
                }

                $table = '<?php' . "\n\n" .
                         'class ' . $instance->transform($tableName, $tname) .
                         ' extends Hoa_Database_Model_Table {' . "\n\n" .
                         '    protected $_base    = \'' . $bname. '\';' . "\n" .
                         '    protected $_name    = \'' . $tname. '\';' . "\n";

                foreach($f as $name => $fields) {

                    $table .= '    public    $' . str_pad($name, 8) . ' = array(' . "\n";
                    $temp   = array();

                    foreach($fields as $key => $value) {

                        switch($value) {

                            case 'true':
                            case 'false':
                              break;

                            default:
                                $value = '\'' . $value . '\'';
                        }

                        $temp[] = '        \'' . str_pad($key . '\'', 14) . ' => ' . $value;
                    }

                    $table .= implode(",\n", $temp) . "\n" .
                              '    );' . "\n\n";
                }

                $table = substr($table, 0, -1) . '}' . "\n\n" .
                         'class ' . $instance->transform($tableCollection, $tname) .
                         ' extends Hoa_Database_Model_Collection { }';

                file_put_contents($instance->transform($basePath, $bname) . DS .
                                  $instance->transform($tableFile, $tname), $table);

            }
        }

        $class = '<?php' . "\n\n" .
                 'class ' . $instance->transform($baseName, $bname) .
                 ' extends Hoa_Database_Model_Base {' . "\n\n" .
                 '    protected $_name = \'' . $bname . '\';' . "\n" .
                 '    public' . "\n" . '        $' . implode(",\n" . '        $', $t) . ';' . "\n" .
                 '}';

        file_put_contents($instance->transform($basePath, $bname) . DS .
                          $instance->transform($baseFile, $bname), $class);

        return true;
    }
}
