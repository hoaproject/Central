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
 * \Hoa\Dns\Exception
 */
-> import('Dns.Exception');

}

namespace Hoa\Dns {

/**
 * Class \Hoa\Dns.
 *
 * Provide a tiny and very simple DNS server.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Dns implements \Hoa\Core\Event\Listenable {

    /**
     * Listeners.
     *
     * @var \Hoa\Core\Event\Listener object
     */
    protected $_on           = null;

    /**
     * Socket.
     *
     * @var \Hoa\Socket object
     */
    protected $_socket       = null;

    /**
     * Type values for resources and queries.
     *
     * @var \Hoa\Dns\Light array
     */
    protected static $_types = array(
        'invalid'     =>     0, // Cookie.
        'a'           =>     1, // Host address.
        'ns'          =>     2, // Authorative server.
        'md'          =>     3, // Mail destinaion.
        'mf'          =>     4, // Mail forwarder.
        'cname'       =>     5, // Canonical name.
        'soa'         =>     6, // Start of authority zone.
        'mb'          =>     7, // Mailbox domain name.
        'mg'          =>     8, // Mail group member.
        'mr'          =>     9, // Mail rename name.
        'null'        =>    10, // Null resource record.
        'wks'         =>    11, // Well known service.
        'ptr'         =>    12, // Domain name pointer.
        'hinfo'       =>    13, // Host information.
        'minfo'       =>    14, // Mailbox information.
        'mx'          =>    15, // Mail routing information.
        'txt'         =>    16, // Text strings.
        'rp'          =>    17, // Responsible person.
        'afsdb'       =>    18, // AFS cell database.
        'x25'         =>    19, // X_25 calling address.
        'isdn'        =>    20, // ISDN calling address.
        'rt'          =>    21, // Router.
        'nsap'        =>    22, // NSAP address.
        'ns_nsap_ptr' =>    23, // Reverse NSAP lookup (deprecated)
        'sig'         =>    24, // Security signature.
        'key'         =>    25, // Security key.
        'px'          =>    26, // X.400 mail mapping.
        'gpos'        =>    27, // Geographical position (withdrawn).
        'aaaa'        =>    28, // Ip6 Address.
        'loc'         =>    29, // Location Information.
        'nxt'         =>    30, // Next domain (security)
        'eid'         =>    31, // Endpoint identifier.
        'nimloc'      =>    32, // Nimrod Locator.
        'srv'         =>    33, // Server Selection.
        'atma'        =>    34, // ATM Address
        'naptr'       =>    35, // Naming Authority PoinTeR
        'kx'          =>    36, // Key Exchange
        'cert'        =>    37, // Certification Record
        'a6'          =>    38, // IPv6 Address (deprecated, use ns_t.aaaa)
        'dname'       =>    39, // Non-terminal DNAME (for IPv6)
        'sink'        =>    40, // Kitchen sink (experimental)
        'opt'         =>    41, // EDNS0 option (meta-RR)
        'apl'         =>    42, // Address prefix list (RFC3123)
        'ds'          =>    43, // Delegation Signer
        'sshfp'       =>    44, // SSH Fingerprint
        'ipseckey'    =>    45, // IPSEC Key
        'rrsig'       =>    46, // RRSet Signature
        'nsec'        =>    47, // Negative Security
        'dnskey'      =>    48, // DNS Key
        'dhcid'       =>    49, // Dynamic host configuartion identifier
        'nsec3'       =>    50, // Negative security type 3
        'nsec3param'  =>    51, // Negative security type 3 parameters
        'hip'         =>    55, // Host Identity Protocol
        'spf'         =>    99, // Sender Policy Framework
        'tkey'        =>   249, // Transaction key
        'tsig'        =>   250, // Transaction signature.
        'ixfr'        =>   251, // Incremental zone transfer.
        'axfr'        =>   252, // Transfer zone of authority.
        'mailb'       =>   253, // Transfer mailbox records.
        'maila'       =>   254, // Transfer mail agent records.
        'any'         =>   255, // Wildcard match.
        'zxfr'        =>   256, // BIND-specific, nonstandard.
        'dlv'         => 32769, // DNSSEC look-aside validation.
        'max'         => 65536
    );



    /**
     * Construct the DNS server.
     *
     * @access  public
     * @param   \Hoa\Socket\Server  $server    Server.
     * @return  void
     */
    public function __construct ( \Hoa\Socket\Server $server ) {

        if('udp' != $server->getSocket()->getTransport())
            throw new Exception(
                'Server must listen on UDP transport; given %s.',
                0, strtoupper($server->getSocket()->getTransport()));

        set_time_limit(0);

        $this->_server = $server;
        $this->_on     = new \Hoa\Core\Event\Listener($this, array('query'));

        return;
    }

    /**
     * Attach a callable to this listenable object.
     *
     * @access  public
     * @param   string  $listenerId    Listener ID.
     * @param   mixed   $call          First callable part.
     * @param   mixed   $able          Second callable part (if needed).
     * @return  \Hoa\Worker\Backend\Shared
     * @throw   \Hoa\Core\Exception
     */
    public function on ( $listenerId, $call, $able = '' ) {

        return $this->_on->attach($listenerId, $call, $able);
    }

    /**
     * Run the server.
     *
     * @access  public
     * @return  void
     */
    public function run ( ) {

        $this->_server->considerRemoteAddress(true);
        $this->_server->connectAndWait();

        while(true) {

            $buffer = $this->_server->read(1024);

            if(empty($buffer))
                continue;

            $domain = null;
            $handle = substr($buffer, 12);

            for($i = 0, $m = strlen($handle); $i < $m; ++$i) {

                if(0 === $length = ord($handle[$i]))
                    break;

                $domain .= substr($handle, $i + 1, $length) . '.';
                $i      += $length;
            }

            $i     += 2;
            $type   = array_search(
                (int) (string) ord($handle[$i]),
                self::$_types
            );
            $domain = substr($domain, 0, -1);
            $ips    = $this->_on->fire('query', new \Hoa\Core\Event\Bucket(array(
                'domain' => $domain,
                'type'   => $type
            )));
            $ip     = null;

            foreach(explode('.', $ips[0]) as $foo)
                $ip .= chr($foo);

            $this->_server->writeAll(
                $buffer[0] . $buffer[1] . chr(129)   . chr(128)   .
                $buffer[4] . $buffer[5] . $buffer[4] . $buffer[5] .
                chr(0)     . chr(0)     . chr(0)     . chr(0)     .
                $handle    . chr(192)   . chr(12)    . chr(0)     .
                chr(1)     . chr(0)     . chr(1)     . chr(0)     .
                chr(0)     . chr(0)     . chr(60)    . chr(0)     .
                chr(4)     . $ip
            );
        }

        $this->_server->disconnect();

        return;
    }
}

}
