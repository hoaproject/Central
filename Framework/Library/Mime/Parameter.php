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
 * @package     Hoa_Mime
 * @subpackage  Hoa_Mime_Parameter
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Class Hoa_Mime_Parameter.
 *
 * Manipulaite Mime-type parameter.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Mime
 * @subpackage  Hoa_Mime_Parameter
 */

class Hoa_Mime_Parameter {

    /**
     * Mime-type parameter.
     *
     * @var Hoa_Mime_Parameter array
     */
    protected $parameter = array();



    /**
	 * __construct
	 * Start parse.
	 *
	 * @access  public
	 * @param   parameter  string    Mime-type parameter.
	 * @return  array
     * @throw   Hoa_Mime_Exception
     */
    public function __construct ( $parameter = '' ) {

		if(empty($parameter))
			throw new Hoa_Mime_Exception('Parameter could not be empty.', 0);

		$this->parse($parameter);
    }

    /**
	 * parse
	 * Parse Mime-type parameter.
	 * Return attribute and value into an array.
	 *
	 * @access  public
	 * @param   parameter  string    Mime-type parameter.
	 * @return  array
     * @throw   Hoa_Mime_Exception
     */
    public function parse ( $parameter = '' ) {

		if(empty($parameter))
			throw new Hoa_Mime_Exception('Parameter could not be empty.', 1);

		if(strpos($parameter, ';'))
			$parameter = explode(';', $parameter);
		else
			$parameter = array($parameter);

		foreach($parameter as $p)
			$this->parameter[$this->getAttribute($p)] =
				array('value'   => $this->getValue($p),
				      'comment' => $this->getComment($p));

		return $this->parameter;
    }

	/**
	 * getAttribute
	 * Get Mime-type parameter attribute.
	 *
	 * @access  public
	 * @param   attr    string    Mime-type attribute.
	 * @return  string
     * @throw   Hoa_Mime_Exception
	 */
	public function getAttribute ( $attr = '' ) {

		if(empty($attr))
			throw new Hoa_Mime_Exception('Attribute could not be empty.', 2);

		return strtolower(trim(substr($attr, 0, strpos($attr, '='))));
	}

    /**
	 * getValue
	 * Get Mime-type parameter value.
	 *
	 * @access  public
	 * @param   value    string    Mime-type value.
	 * @return  string
     * @throw   Hoa_Mime_Exception
     */
    public function getValue ( $value = '' ) {

		if(empty($value))
			throw new Hoa_Mime_Exception('Value could not be empty.', 3);

		$value = strstr($value, '=');

		if($this->hasComment($value))
			$out = substr($value, 1, strpos($value, '(')-1);
		else
			$out = substr($value, 1);

		return strtolower(trim($out));
    }

    /**
	 * hasComment
	 * Check if Mime-type has a comment or not.
	 *
	 * @access  public
	 * @param   value   string    Mime-type value.
	 * @return  bool
     * @throw   Hoa_Mime_Exception
     */
    public function hasComment ( $value = '' ) {

		if(empty($value))
			throw new Hoa_Mime_Exception('Mime-type could not be empty.', 4);

		return !(false === strpos($value, '('));
    }

    /**
	 * getComment
	 * Get Mime-type parameter comment.
	 *
	 * @access  public
	 * @param   value   string    Mime-type value.
	 * @return  string
     * @throw   Hoa_Mime_Exception
     */
    public function getComment ( $value = '' ) {

		if(empty($value))
			throw new Hoa_Mime_Exception('Value could not be empty.', 5);


		return $this->hasComment($value)
                   ? trim(substr($value, strpos($value, '(')+1), ') ')
                   : false;
    }

    /**
     * getParameter
     * Get parameters.
     *
     * @access  public
     * @return  array
     */
    public function getParameter ( ) {

        return $this->parameter;
    }
}
