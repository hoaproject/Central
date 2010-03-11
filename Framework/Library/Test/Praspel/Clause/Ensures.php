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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Test_Praspel_Clause_Ensures
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Test_Praspel_Exception
 */
import('Test.Praspel.Exception');

/**
 * Hoa_Test_Praspel_Clause_Contract
 */
import('Test.Praspel.Clause.Contract');

/**
 * Class Hoa_Test_Praspel_Clause_Ensures.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Clause_Ensures
 */

class Hoa_Test_Praspel_Clause_Ensures extends Hoa_Test_Praspel_Clause_Contract {

    /**
     * Declare a variable, or get it.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  Hoa_Test_Praspel_Variable
     */
    public function variable ( $name ) {

        if($name == '\result')
            return parent::variable($name);

        if(0 !== preg_match('#\\\old\(\s*\w+\s*\)#i', $name, $matches))
            throw new Hoa_Test_Praspel_Exception(
                'Redefining types of an old variable (%s) in an ensures ' .
                'clause has no sens.',
                0, $name);

        $parent = $this->getParent();

        if(   false === $parent->clauseExists('requires')
           || false === $parent->getClause('requires')->variableExists($name))
           throw new Hoa_Test_Praspel_Exception(
            'Cannot ensure a property on the non-existing variable %s.',
            0, $name);

        return parent::variable($name);
    }
}
