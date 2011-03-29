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
 * \Hoa\XmlRpc\Message\Valued
 */
-> import('XmlRpc.Message.Valued')

/**
 * \Hoa\XmlRpc\Message\Message
 */
-> import('XmlRpc.Message.~')

/**
 * \Hoa\StringBuffer\Read
 */
-> import('StringBuffer.Read')

/**
 * \Hoa\Xml\Read
 */
-> import('Xml.Read');

}

namespace Hoa\XmlRpc\Message {

/**
 * Class \Hoa\XmlRpc\Message\Message\Request.
 *
 * Represent a response message, with values (through inheritance).
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Response extends Valued implements Message {

    /**
     * Response.
     *
     * @var \Hoa\Xml\Read object
     */
    protected $_response = null;



    /**
     * Construct a new response.
     */
    public function __construct ( $response ) {

        $buffer = new \Hoa\StringBuffer\Read();
        $buffer->initializeWith($response);

        $this->_response = new \Hoa\Xml\Read($buffer);
        $this->_computeResponse(
            $this->_response->xpath('/methodResponse/params/param/value/*'),
            $this
        );

        return;
    }

    /**
     * Compute response into values bucket.
     *
     * @access  protected
     * @param   array                       $values    Values XML collection.
     * @param   \Hoa\XmlRpc\Message\Valued  $self      Current valued object.
     * @return  void
     */
    protected function _computeResponse ( $values, $self ) {

        if(!is_array($values))
            $values = array($values);

        foreach($values as $value) {

            switch(strtolower($value->getName())) {

                case 'array':
                    $self = $self->withArray();

                    foreach($value->data as $data)
                        $this->_computeResponse($data->xpath('./value/*'), $self);

                    $self = $self->endArray();
                  break;

                case 'base64':
                    $self->withBase64($value->readAll());
                  break;

                case 'boolean':
                    $self->withBoolean((boolean) (int) $value->readAll());
                  break;

                case 'datetime.iso8601':
                    $self->withDateTime(strtotime($value->readAll()));
                  break;

                case 'double':
                    $self->withFloat((float) $value->readAll());
                  break;

                case 'i4':
                case 'integer':
                    $self->withInteger((int) $value->readAll());
                  break;

                case 'string':
                    $self->withString($value->readAll());
                  break;

                case 'struct':
                    $self = $self->withStructure();

                    foreach($value->member as $member) {

                        $self->withName($member->name->readAll());
                        $this->_computeResponse($member->xpath('./value/*'), $self);
                    }

                    $self = $self->endStructure();
                  break;

                case 'nil':
                    $self->withNull();
                  break;
            }
        }

        return;
    }
}

}
