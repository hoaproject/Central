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
 * @category    Data
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Console_Interface_Style
 */
import('Console.Interface.Style');

/**
 * Class SheetStyle.
 *
 * This sheet declares the main style.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class SheetStyle extends Hoa_Console_Interface_Style {

    /**
     * Import the style.
     *
     * @access  public
     * @return  void
     */
    public function import ( ) {

        parent::addStyles(array(

            '_exception' => array(
                parent::COLOR_FOREGROUND_WHITE,
                parent::COLOR_BACKGROUND_RED
            ),

            'h1' => array(
                parent::COLOR_FOREGROUND_YELLOW,
                parent::TEXT_UNDERLINE
            ),

            'h2' => array(
                parent::COLOR_FOREGROUND_GREEN
            ),

            'info' => array(
                parent::COLOR_FOREGROUND_YELLOW
            ),

            'error' => array(
                parent::COLOR_FOREGROUND_WHITE,
                parent::COLOR_BACKGROUND_RED,
                parent::TEXT_BOLD
            ),

            'success' => array(
                parent::COLOR_FOREGROUND_GREEN
            ),

            'nosuccess' => array(
                parent::COLOR_FOREGROUND_RED
            ),

            'command' => array(
                parent::COLOR_FOREGROUND_BLUE
            ),

            'attention' => array(
                parent::COLOR_FOREGROUND_WHITE,
                parent::COLOR_BACKGROUND_RED,
                parent::TEXT_BOLD
            ),
        ));

        return;
    }
}
