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
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Selector_Random
 *
 */

/**
 * Hoa_Test_Selector
 */
import('Test.Selector.~') and load();

/**
 * Hoa_Test_Sampler_Random
 */
import('Test.Sampler.Random') and load();

/**
 * Class Hoa_Test_Selector_Random.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Selector_Random
 */

class Hoa_Test_Selector_Random extends Hoa_Test_Selector {

    public function __construct ( Array $variables ) {

        $sampler              = new Hoa_Test_Sampler_Random();
        $this->_selections[0] = array();

        foreach($variables as $variable) {

            $domains = $variable->getDomains();
            $i       = $sampler->getInteger(0, count($domains) - 1);

            foreach($domains as $domain)
                if(0 === $i--)
                    break;

            $this->_selections[0][$variable->getName()] = $domain;
        }

        return;
    }
}
