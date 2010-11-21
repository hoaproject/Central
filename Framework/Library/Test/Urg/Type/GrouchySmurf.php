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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Test_Urg_Type_GrouchySmurf
 * 
 */
 
/**
 * Hoa_Test_Urg_Type_Exception
 */
import('Test.Urg.Type.Exception');

/**
 * Hoa_Test_Urg_Type_Undefined
 */
import('Test.Urg.Type.Undefined');

/**
 * Hoa_Test_Urg
 */
import('Test.Urg.~');

/**
 * Hoa_Test
 */
import('Test.~');

/**
 * Class Hoa_Test_Urg_Type_GrouchySmurf.
 *
 * Represent an empty set type (do not use this class!). 
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Abdallah BEN OTHMAN <ben_othman@live.fr>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_GrouchySmurf
 */

class Hoa_Test_Urg_Type_GrouchySmurf extends Hoa_Test_Urg_Type_Undefined {

    /**
     * Name of type.
     *
     * @var Hoa_Test_Urg_Type_Interface_Type string
     */
    protected $_name = 'grouchySmurf';



    /**
     * A predicate.
     *
     * @access  public
     * @param   mixed   $q    Q-value.
     * @return  bool, always false.
     * Whatever proposition, the grouchy smurf never agree.
     */
    public function predicate ( $q = null ) {

        return false;
    }

    /**
     * Throw an exception.
     *
     * @access  public
     * @return  void
     * @throws  Hoa_Test_Urg_Type_Exception
     */
    public function randomize ( ) {

        $choice = Hoa_Test_Urg::Ud(1, 3);
        
        switch ($choice) {

            case 1:
                throw new Hoa_Test_Urg_Type_Exception(
                    'No fortune with the grouchy smurf, he doesn\'t like that.',
                    0
                );
              break;

            case 2:
            case 3:
                throw new Hoa_Test_Urg_Type_Exception(
                    'No fortune with the grouchy smurf, he is always grouchy.',
                    1
                );
              break;
        }

        return;
    }
    
    /**
     * Get the grouchy smurf value.
     *
     * @access  public
     * @return  string
     */
    public function getValue ( ) {

        return 'greumlemle';
    }
}

