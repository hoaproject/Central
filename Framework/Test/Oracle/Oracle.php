<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Framework
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Oracle
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Test_Oracle_Exception
 */
import('Test.Oracle.Exception');

/**
 * Hoa_Test_Oracle_Eyes
 */
import('Test.Oracle.Eyes');

/**
 * Class Hoa_Test_Oracle.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Oracle
 */

class Hoa_Test_Oracle {

    /**
     * The request object.
     *
     * @var Hoa_Test_Request object
     */
    protected $_request = null;



    /**
     * Set the request object.
     *
     * @access  public
     * @param   Hoa_Test_Request  $request    The request object.
     * @return  Hoa_Test_Request
     */
    public function setRequest ( Hoa_Test_Request $request ) {

        $old            = $this->_request;
        $this->_request = $request;

        return $old;
    }

    /**
     *
     */
    public function predict ( ) {

        $this->prepareIncubator();
        $this->prepareEyes();
    }

    /**
     * Prepare the incubator.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Test_Oracle_Exception
     */
    protected function prepareIncubator ( ) {

        $convict   = $this->getRequest()->getParameter('convict.directory');
        $recursive = $this->getRequest()->getParameter('convict.recursive');
        $incubator = $this->getRequest()->getParameter('test.incubator');

        if(null === $convict)
            throw new Hoa_Test_Oracle_Exception(
                'A file or a directory must be specified to run tests.', 0);

        $files = array();

        if(is_dir($convict)) {

            $iterator = new RecursiveDirectoryIterator($convict);

            if(true === $recursive)
                $iterator = new RecursiveIteratorIterator(
                                $iterator,
                                RecursiveIteratorIterator::SELF_FIRST
                            );

            $strlen = strlen($convict);

            foreach($iterator as $name => $splFileInfo) {

                if(substr($name, -4) != '.php')
                    continue;

                $files[] = substr($name, $strlen);
            }
        }
        elseif(is_file($convict))
            $files[] = basename($convict);
        else
            throw new Hoa_Test_Oracle_Exception(
                '%s is not a valid file or directory.', 1, $convict);

        if(is_dir($incubator)) {

            foreach(new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($incubator),
                        RecursiveIteratorIterator::CHILD_FIRST
                    ) as $name => $splFileInfo) {

                if(is_dir($name))
                    rmdir($name);
                elseif(is_file($name))
                    unlink($name);
            }

            rmdir($incubator);
        }

        if(false === @mkdir($incubator, 0777, true))
            throw new Hoa_Test_Oracle_Exception(
                'Cannot create the incubator in %s.', 2, $incubator);

        foreach($files as $i => $file) {

            if(!is_dir(dirname($incubator . $file)))
                mkdir(dirname($incubator . $file), 0777, true);

            if(false === @copy($convict . $file, $incubator . $file))
                throw new Hoa_Test_Oracle_Exception(
                    'Cannot copy %s in %s.', 3,
                    array($convict . $file, $incubator . $file));
        }

        $this->getRequest()->setParameter('convict.result', $files);

        return;
    }

    /**
     * Prepare eyes of oracle.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Test_Oracle_Exception
     */
    protected function prepareEyes ( ) {

        $eyes = new Hoa_Test_Oracle_Eyes();
        $eyes->setRequest($this->getRequest());
        $eyes->open();
    }

    /**
     * Get the request object.
     *
     * @access  public
     * @return  Hoa_Test_Request
     */
    public function getRequest ( ) {

        return $this->_request;
    }
}
