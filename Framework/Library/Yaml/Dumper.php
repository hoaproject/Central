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
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Yaml
 * @subpackage  Hoa_Yaml_Dumper
 *
 */

/**
 * Class Hoa_Yaml_Dumper.
 *
 * Yaml dumper.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Yaml
 * @subpackage  Hoa_Yaml_Dumper
 */

class Hoa_Yaml_Dumper {

    /**
     * Final source.
     *
     * @var Hoa_Yaml_Dumper string
     */
    protected $out = '';



    /**
	 * __construct
	 * Dump a parsed file.
	 *
	 * @access  public
	 * @param   source  array    YAML source.
	 * @param   idt     int      Indentation.
	 * @return  string
     */
    public function __construct ( $source = array(), $idt = -1 ) {

		$_ = $idt == -1 ? '' : str_repeat(' ', $idt*2);

		foreach($source as $key => $value) {

			if(is_int($key))
				if($idt == -1)
					$this->out .= "\n" . '---' . "\n";
				else
					$this->out .= $_ . '- ';
			else
				$this->out .= $_ . $key . ': ';

			if(is_array($value)) {
				$this->out .= "\n";
				$idt++;
					$this->__construct($source[$key], $idt);
				$idt--;
			}
			else
				$this->out .= $this->quote($value) . "\n";
		}

		return $this->out;
    }

    /**
	 * quote
	 * Quote a string.
	 *
	 * @access  protected
	 * @param   str      string    String to quote.
	 * @return  string
     */
    protected function quote ( $str ) {

		if(strpos($str, '"')) {
			$str = str_replace('"', '\"', $str);
			$str = '"'.$str.'"';
		}
		elseif(strpos($str, "'")) {
			$str = str_replace("'", "\'", $str);
			$str = "'".$str."'";
		}

		return $str;
    }

	/**
	 * get
	 * Get result.
	 *
	 * @access  public
	 * @return  string
	 */
	public function get ( ) {

		return $this->out;
	}
}

