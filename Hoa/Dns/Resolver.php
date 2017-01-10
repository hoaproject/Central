<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Dns;

use Hoa\Event;
use Hoa\Socket;

/**
 * Class \Hoa\Dns\Resolver.
 *
 * Provide a DNS resolution server.
 * Please, see RFC6195, RFC1035 and RFC1034 for an overview.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Resolver implements Event\Listenable
{
    use Event\Listens;

    /**
     * Socket.
     *
     * @var \Hoa\Socket
     */
    protected $_server         = null;

    /**
     * Type values for resources and queries.
     * Please, see
     * http://iana.org/assignments/dns-parameters/dns-parameters.xml.
     *
     * @var array
     */
    protected static $_types   = [
        'invalid'     => 0,     // Invalid.
        'a'           => 1,     // Host address.
        'ns'          => 2,     // Authorative name server.
        'md'          => 3,     // Mail destination (obsolete, use MX).
        'mf'          => 4,     // Mail forwarder (obsolete, use MX).
        'cname'       => 5,     // Canonical name for an alias.
        'soa'         => 6,     // Start of a zone of authority.
        'mb'          => 7,     // Mailbox domain name.
        'mg'          => 8,     // Mail group member.
        'mr'          => 9,     // Mail rename name.
        'null'        => 10,    // Null resource record.
        'wks'         => 11,    // Well known service description.
        'ptr'         => 12,    // Domain name pointer.
        'hinfo'       => 13,    // Host information.
        'minfo'       => 14,    // Mailbox or mail list information.
        'mx'          => 15,    // Mail exchange.
        'txt'         => 16,    // Text strings.
        'rp'          => 17,    // Responsible person.
        'afsdb'       => 18,    // AFS cell database.
        'x25'         => 19,    // X_25 calling address.
        'isdn'        => 20,    // ISDN calling address.
        'rt'          => 21,    // Route through resource record.
        'nsap'        => 22,    // NSAP address.
        'ns_nsap_ptr' => 23,    // Reverse NSAP lookup (deprecated).
        'sig'         => 24,    // Security signature.
        'key'         => 25,    // Security key resource record.
        'px'          => 26,    // X.400 mail mapping.
        'gpos'        => 27,    // Geographical position (withdrawn).
        'aaaa'        => 28,    // IPv6 Address.
        'loc'         => 29,    // Location Information.
        'nxt'         => 30,    // Next domain.
        'eid'         => 31,    // Endpoint identifier.
        'nimloc'      => 32,    // Nimrod Locator.
        'srv'         => 33,    // Server Selection.
        'atma'        => 34,    // ATM Address.
        'naptr'       => 35,    // Naming Authority pointer.
        'kx'          => 36,    // Key Exchange.
        'cert'        => 37,    // Certification Record.
        'a6'          => 38,    // IPv6 Address (obsolete, use aaaa).
        'dname'       => 39,    // Non-terminal DNAME (for IPv6).
        'sink'        => 40,    // Kitchen sink.
        'opt'         => 41,    // EDNS0 option (meta-RR).
        'apl'         => 42,    // Address prefix list.
        'ds'          => 43,    // Delegation Signer
        'sshfp'       => 44,    // SSH Fingerprint
        'ipseckey'    => 45,    // IPSEC Key
        'rrsig'       => 46,    // RRSet Signature
        'nsec'        => 47,    // Negative Security
        'dnskey'      => 48,    // DNS Key
        'dhcid'       => 49,    // Dynamic host configuration identifier
        'nsec3'       => 50,    // Negative security type 3
        'nsec3param'  => 51,    // Negative security type 3 parameters
        'hip'         => 55,    // Host Identity Protocol
        'spf'         => 99,    // Sender Policy Framework
        'tkey'        => 249,   // Transaction key
        'tsig'        => 250,   // Transaction signature.
        'ixfr'        => 251,   // Incremental zone transfer.
        'axfr'        => 252,   // Transfer zone of authority.
        'mailb'       => 253,   // Transfer mailbox records.
        'maila'       => 254,   // Transfer mail agent records.
        'any'         => 255,   // Wildcard match.
        'zxfr'        => 256,   // BIND-specific, nonstandard.
        'dlv'         => 32769, // DNSSEC look-aside validation.
        'max'         => 65536
    ];

    /**
     * Class values for resources and queries.
     *
     * @var array
     */
    protected static $_classes = [
        'in'    => 1,   // Internet.
        'cs'    => 2,   // CSNET (obsolete).
        'ch'    => 3,   // Chaos.
        'hs'    => 4,   // Hesiod.
        'qnone' => 254, // QClass none.
        'qany'  => 255  // QClass any.
    ];



    /**
     * Construct the DNS server.
     *
     * @param   \Hoa\Socket\Server  $server    Server.
     */
    public function __construct(Socket\Server $server)
    {
        if ('udp' != $server->getSocket()->getTransport()) {
            throw new Exception(
                'Server must listen on UDP transport; given %s.',
                0,
                strtoupper($server->getSocket()->getTransport())
            );
        }

        set_time_limit(0);

        $this->_server = $server;
        $this->setListener(new Event\Listener($this, ['query']));

        return;
    }

    /**
     * Run the server.
     *
     * @return  void
     */
    public function run()
    {
        $this->_server->considerRemoteAddress(true);
        $this->_server->connectAndWait();

        while (true) {
            $buffer = $this->_server->read(1024);

            if (empty($buffer)) {
                continue;
            }

            // Skip header.
            $handle = substr($buffer, 12);
            $domain = null;

            // QNAME.
            for ($i = 0, $m = strlen($handle); $i < $m; ++$i) {
                if (0 === $length = ord($handle[$i])) {
                    break;
                }

                if (null !== $domain) {
                    $domain .= '.';
                }

                $domain .= substr($handle, $i + 1, $length);
                $i      += $length;
            }

            // QTYPE.
            $i      += 2;
            $qtype   = (int) (string) ord($handle[$i]) +
                       (int) (string) ord($handle[$i + 1]);
            $type    = array_search($qtype, static::$_types) ?: $qtype;

            // QCLASS.
            $i      += 2;
            $qclass  = (int) (string) ord($handle[$i]);
            $class   = array_search($qclass, static::$_classes) ?: $qclass;

            $ips     = $this->getListener()->fire('query', new Event\Bucket([
                'domain' => $domain,
                'type'   => $type,
                'class'  => $class
            ]));
            $ip      = null;

            if (false === $ips[0]) {
                $this->_server->writeAll(
                    // Header.

                    // ID.
                    $buffer[0] .
                    $buffer[1] .
                    pack(
                        'C',
                        1 << 7 // QR,     1 = response.
                               // OpCode, 0 = QUERY.
                               // AA,     0
                               // TC,     0
                      | 1      // RD
                    ) .
                    pack(
                        'C',
                        0      // RA, Z, AD, CD.
                      | 3      // NXDOMAIN.
                    ) .
                    // QDCOUNT.
                    pack('n', 0) .
                    // ANCOUNT.
                    pack('n', 0) .
                    // NSCOUNT.
                    pack('n', 0) .
                    // ARCOUNT.
                    pack('n', 0)
                );

                continue;
            }

            foreach (explode('.', $ips[0]) as $foo) {
                $ip .= pack('C', $foo);
            }

            $this->_server->writeAll(
                // Header.

                // ID.
                $buffer[0] .
                $buffer[1] .
                pack(
                    'C',
                    1 << 7 // QR,     1 = response.
                           // OpCode, 0 = QUERY.
                           // AA,     0
                           // TC,     0
                  | 1      // RD
                ) .
                pack(
                    'C',
                    0      // RA, Z, AD, CD.
                ) .
                // QDCOUNT.
                $buffer[4] .
                $buffer[5] .
                // ANCOUNT.
                pack('n', 1) .
                // NSCOUNT.
                pack('n', 0) .
                // ARCOUNT.
                pack('n', 0) .

                // Question.

                $handle .
                pack('CC', 192, 12) .

                // Answer.

                // TYPE.
                pack('n', $qtype) .
                // CLASS.
                pack('n', $qclass) .
                // TTL.
                pack('N', 60) .
                // RDLENGTH.
                pack('n', 4) .
                // RDATA.
                $ip
            );
        }

        $this->_server->disconnect();

        return;
    }
}
