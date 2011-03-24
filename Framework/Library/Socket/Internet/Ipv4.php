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
 * \Hoa\Socket\Exception
 */
-> import('Socket.Exception')

/**
 * \Hoa\Socket\Internet
 */
-> import('Socket.Internet.~');

}

namespace Hoa\Socket\Internet {

/**
 * Class \Hoa\Socket\Internet\Ipv4.
 *
 * Handle IPv4.
 *
 * @author      Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan Enderlin.
 * @license     New BSD License
 */

class Ipv4 extends Internet {

    /**
     * Set address.
     *
     * @access  public
     * @param   string  $address    Address.
     * @return  string
     * @throw   \Hoa\Socket\Exception
     */
    public function setAddress ( $address ) {

        if(!(   substr_count($address, '.')  == 3
             && ip2long($address)           !== false))
            throw new \Hoa\Socket\Exception(
                'Address %s is not a valid IPv4 address.', 0, $address);

        $old            = $this->_address;
        $this->_address = $address;

        return $old;
    }
}

}
