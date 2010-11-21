<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 * @package     Hoa_Xyl
 * @subpackage  Hoa_Xyl_Interpreter_Html5
 *
 */

/**
 * Hoa_Xyl_Interpreter
 */
import('Xyl.Interpreter') and load();

/**
 * Hoa_Xyl_Interpreter_Common_*
 */
import('Xyl.Interpreter.Common.*');

/**
 * Hoa_Xyl_Interpreter_Html5_*
 */
import('Xyl.Interpreter.Html5.*');

/**
 * Class Hoa_Xyl_Interpreter_Html5.
 *
 * 
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xyl
 * @subpackage  Hoa_Xyl_Interpreter_Html5
 */

class Hoa_Xyl_Interpreter_Html5 extends Hoa_Xyl_Interpreter { 

    /**
     * Rank: abstract elements to concrete elements.
     *
     * @var Hoa_Xyl_Interpreter_Html5 array
     */
    protected $_rank = array(
        'yield'    => 'Hoa_Xyl_Interpreter_Common_Yield',
        'page'     => 'Hoa_Xyl_Interpreter_Html5_Page',
        'ul'       => 'Hoa_Xyl_Interpreter_Html5_Ul',
        'li'       => 'Hoa_Xyl_Interpreter_Html5_Li',
        'section1' => 'Hoa_Xyl_Interpreter_Html5_Section1',
        'p'        => 'Hoa_Xyl_Interpreter_Html5_P'
    );
}
