<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
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
 * @copyright   Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
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

