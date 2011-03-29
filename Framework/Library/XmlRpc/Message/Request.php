<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 */

namespace {

from('Hoa')

/**
 * \Hoa\XmlRpc\Exception
 */
-> import('XmlRpc.Exception')

/**
 * \Hoa\XmlRpc\Message\Message
 */
-> import('XmlRpc.Message.~')

/**
 * \Hoa\XmlRpc\Message\Valued
 */
-> import('XmlRpc.Message.Valued');

}

namespace Hoa\XmlRpc\Message {

/**
 * Class \Hoa\XmlRpc\Message\Message\Request.
 *
 * Represent a request message, with values (through inheritance).
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Request extends Valued implements Message {

    /**
     * Method to call.
     *
     * @var \Hoa\XmlRpc\Message\Request
     */
    protected $_method = null;



    /**
     * Construct a new request.
     *
     * @access  public
     * @param   string  $method    Method to call.
     * @return  void
     */
    public function __construct ( $method ) {

        $this->_method = $method;
        parent::__construct(parent::IS_SCALAR, null);

        return;
    }

    /**
     * Get method to call.
     *
     * @access  public
     * @return  string
     */
    public function getMethod ( ) {

        return $this->_method;
    }

    /**
     * Transform the message into a XML string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $out = '<?xml version="1.0" encoding="utf-8"?' . '>' . "\n" .
               '<methodCall>' . "\n" .
               '  <methodName>' . $this->getMethod() . '</methodName>' . "\n";

        $values = $this->getValues();

        if(!empty($values)) {

            $out .= '  <params>' . "\n";

            foreach($this->getValues() as $value)
                $out .= '    <param>' . "\n" . '      <value>' .
                        str_replace(
                            "\n",
                            "\n      ",
                            $this->getValueAsString(
                                $value[self::VALUE],
                                $value[self::TYPE]
                            )
                        ).
                        '</value>' . "\n" . '    </param>' . "\n";

            $out .= '  </params>' . "\n";
        }

        return $out . '</methodCall>';
    }
}

}
