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
 * @package     Hoa_Xml
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Xml_Exception
 */
import('Xml.Exception');

/**
 * Hoa_Xml_Parser
 */
import('Xml.Parser');

/**
 * Hoa_Xml_Dumper
 */
import('Xml.Dumper');

/**
 * Class Hoa_Xml.
 *
 * Manage Xml document.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xml
 */

class Hoa_Xml {

	/**
	 * parse
	 * Parse a Xml document into a nested array.
	 *
	 * @access  public
	 * @param   src       string    Source
	 * @param   typeof    string    Source type : NULL, FILE, CURL.
	 * @param   encoding  string    Encoding type.
	 * @return  array
	 */
	public function parse ( $src, $typeof = 'FILE', $encoding = 'UTF-8' ) {

		$parser = new Hoa_Xml_Parser($src, $typeof, $encoding);

		return $parser->getResult();
	}

	/**
	 * dump
	 * Dump a Xml document from an array.
	 *
	 * @access  public
	 * @param   parsed         array     Source.
	 * @param   handlerTag     string    Global/First tag.
	 * @param   encoding       string    Encoding type.
	 * @param   handlerAttr    array     Multi-tag attributs.
	 * @return  string
	 */
	public function dump ( $parsed, $handlerTag = 'global', $hdrftr = true,
                           $encoding = 'utf-8', $handlerAttr = null ) {

		$dumper = new Xml_Dumper($parsed, $handlerAttr);

		return $dumper->get($handlerTag, $encoding, $hdrftr);
	}
}
