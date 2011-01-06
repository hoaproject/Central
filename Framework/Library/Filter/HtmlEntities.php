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
 * @package     Hoa_Filter
 * @subpackage  Hoa_Filter_HtmlEntities
 *
 */

/**
 * Hoa_Filter_Abstract
 */
import('Filter.Abstract');

/**
 * Class Hoa_Filter_HtmlEntities.
 *
 * Apply a html entities filter.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Filter
 * @subpackage  Hoa_Filter_HtmlEntities
 */

class Hoa_Filter_HtmlEntities extends Hoa_Filter_Abstract {

    /**
     * Needed arguments.
     *
     * @var Hoa_Filter_Abstract array
     */
    protected $arguments = array(
        'quoteStyle'   => 'specify the quote style, see the PHP constants : ENT_COMPAT, ENT_QUOTES, ENT_NOQUOTES.',
        'charset'      => 'specify charset.',
        'doubleEncode' => 'specify if PHP make a double encode (true) or not (false).'
    );



    /**
     * Apply a filter.
     *
     * @access  public
     * @param   string  $string    String needed a filter.
     * @return  string
     */
    public function filter ( $string = null ) {

        if(PHP_VERSION_ID >= 50203)
            return htmlentities(
                       (string) $string,
                       $this->getFilterArgument('quoteStyle'),
                       $this->getFilterArgument('charset'),
                       $this->getFilterArgument('doubleEncode')
                   );
        else
            return htmlentities(
                       (string) $string,
                       $this->getFilterArgument('quoteStyle'),
                       $this->getFilterArgument('charset')
                   );
    }
}
