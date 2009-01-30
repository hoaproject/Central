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
 * @subpackage  Hoa_Test_Oracle_Eyes
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
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Class Hoa_Test_Oracle_Eyes.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Oracle_Eyes
 */

class Hoa_Test_Oracle_Eyes {

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
     * Look.
     *
     * @access  public
     * @return  void
     */
    public function open ( ) {

        $incubator = $this->getRequest()->getParameter('test.incubator');
        $oracle    = $this->getRequest()->getParameter('test.ordeal.oracle');
        $files     = $this->getRequest()->getParameter('convict.result');

        foreach($files as $i => $file) {

            $parser = Hoa_Pom::parse($incubator . $file, Hoa_Pom::TOKENIZE_FILE);

            foreach($parser->getElements() as $i => $element)
                if($element instanceof Hoa_Pom_Token_Class)
                    foreach($element->getMethods() as $i => $method) {

                        echo $method->getName()->getString() . "\n";
                        //var_dump($method->getComment()->getComment());
                        print_r($method->getComment()->getParsedTags());
                    }
        }
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
