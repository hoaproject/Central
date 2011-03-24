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
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Rfc882
 *
 */

/**
 * Hoa_Mail
 */
import('Mail.~');

/**
 * Class Hoa_Mail_Rfc882.
 *
 * Validate an email address according to RFC 882.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Rfc882
 */

class Hoa_Mail_Rfc882 extends Hoa_Mail {

    /**
     * Validation level.
     *
     * @const int
     */
    const VALIDATE_SYNTAX = 0;
    const VALIDATE_MX     = 1;
    const VALIDATE_SERVER = 2;
    Const VALIDATE_VERIFY = 3;

    /**
     * Syntaxe pattern for mail address.
     *
     * @var Hoa_Mail_Rfc882 string
     */
    protected $pattern = '#^([a-z0-9_\-\.]+)@([a-z0-9_\-\.]+)\.([a-z]{2,6})$#i';

    /**
     * Address to treat.
     *
     * @var Hoa_Mail_Rfc882 string
     */
    protected $address = '';

    /**
     * Domain to treat.
     *
     * @var Hoa_Mail_Rfc882 string
     */
    protected $domain = '';

    /**
     * MX address.
     *
     * @var Hoa_Mail_Rfc882 string
     */
    private $mx = '';

    /**
     * Weight for MX entries.
     *
     * @var Hoa_Mail_Rfc882 int
     */
    private $weight = '';

    /**
     * Socket time out.
     *
     * @var Hoa_Mail_Rfc882 int
     */
    protected $timeout = 30;

    /**
     * Valid level.
     *
     * @var Hoa_Mail_Rfc882 array
     */
    protected $level = array(
            0 => 'syntax',
            1 => 'mx',
            2 => 'server',
            3 => 'verify'
    );

    /**
     * The result of the validation.
     *
     * @var Hoa_Mail_Rfc882 bool
     */
    protected $result = false;



    /**
     * __construct
     * Define address, domain, and timeout,
     * and run method according to valid level.
     *
     * @access  public
     * @param   address  string    Email address.
     * @param   level    int       Valid level.
     * @param   timeout  int       Timeout (in second).
     * @return  void
     * @throw   Hoa_Mail_Exception
     */
    public function __construct ( $address = '', $level = self::VALIDATE_MX,
                                  $timeout = 30 ) {

        if(empty($address))
            throw new Hoa_Mail_Exception('Address could not be empty.', 0);

        $this->address = $address;
        $this->domain  = substr($this->address, strpos($this->address, '@') + 1);
        $this->timeout = $timeout;
        $return        = false;

        if($level >= self::VALIDATE_SYNTAX)
            $return = $this->isWellFormed();

        if(empty($this->errno) && $level >= self::VALIDATE_MX)
            $return = $this->isMxRecord();

        if(empty($this->errno) && $level >= self::VALIDATE_SERVER)
            $return = $this->isSMTP();

        if(empty($this->errno) && $level == self::VALIDATE_VERIFY)
            $return = $this->isVerified();

        $this->setResultTo($return);

        return;
    }


    /**
     * isWellFormed
     * Check if syntax is correct.
     *
     * @access  protected
     * @return  bool
     */
    protected function isWellFormed ( ) {

        return (bool) preg_match($this->pattern, $this->address);
    }


    /**
     * isMxRecord
     * Check if it's an MX record.
     *
     * @access  protected
     * @return  bool
     * @throw   Hoa_Mail_Exception
     */
    protected function isMxRecord ( ) {

        // Unix
        if(function_exists('getmxrr')) {

            if(!getmxrr($this->domain, $records, $weight))
                throw new Hoa_Mail_Exception('getmxrr(); return false.', 1);

            for($i = 0, $max = count($records); $i < $max; $i++)
                $mxs[$weight[$i]] = $records[$i];

            ksort($mxs, SORT_NUMERIC);
            reset($mxs);

            $w  = key($mxs);
            $mx = current($mxs);
        }

        // not Unix
        else {

            $esad = escapeshellarg($this->domain);
            $ns   = `nslookup -type=MX $esad 2>nul`;

            if(preg_match_all("#^(.*)\tMX preference = (\d+), mail exchanger = (.*)$#im",
                              $ns, $lines, PREG_SET_ORDER) === false)
                throw new Hoa_Mail_Exception('Cannot preg_match_all `nslookup`', 2);

            foreach($lines as $line) {

                if($line[1] != $this->domain)
                    continue;

                $name   = $line[3];
                $weight = intval($line[2]);

                if(!isset($w)) {
                    $w  = $weight;
                    $mx = $name;
                }
                elseif($weight < $w) {
                    $w  = $weight;
                    $mx = $name;
                }
            }
        }

        if(!isset($mx))
            throw new Hoa_Mail_Exception('Have not catched MX', 3);
        else {
            $this->mx     = $mx;
            $this->weight = $w;

            return true;
        }
    }


    /**
     * isSMTP
     * Check if SMTP server exists and is valid.
     *
     * @access  protected
     * @return  bool
     * @throw   Hoa_Mail_Exception
     */
    protected function isSMTP ( ) {

        $errno  = '';
        $errstr = '';

        $fsop = fsockopen('tcp://' . $this->mx, 25, $errno, $errstr, $this->timeout);

        if($this->timeout)
            stream_set_timeout($fsop, $this->timeout);

        if(!empty($errno) && !empty($errstr))
            throw new Hoa_Mail_Exception($errstr, $errno);

        fclose($fsop);

        return true;
    }


    /**
     * isVerified
     * Check if address exists on SMTP server, and his status.
     *
     * @access  protected
     * @return  bool
     * @throw   Hoa_Mail_Protocol_Exception
     * @throw   Hoa_Socket_Exception
     */
    protected function isVerified ( ) {

        try {

            import('Mail.Protocol.Smtp');

            $smtp = new Hoa_Mail_Protocol_Smtp('tcp://' . $this->mx, 25, $this->timeout);
            $smtp->ehlo('HOA Mail');
            $smtp->vrfy($this->address);
            $smtp->quit();
            $smtp->disconnect();
        }
        catch ( Hoa_Mail_Exception $e ) {

            throw $e;

            return false;
        }
        catch ( Hoa_Mail_Protocol_Exception $e ) {

            throw new Hoa_Mail_Exception($e->getMessage(), $e->getCode());

            return false;
        }
        catch ( Hoa_Socket_Exception $e ) {

            throw new Hoa_Mail_Exception($e->getMessage(), $e->getCode());

            return false;
        }

        return true;
    }

    /**
     * Set the result of the validation.
     *
     * @access  protected
     * @param   bool       $result    The result.
     * @return  bool
     */
    protected function setResultTo ( $result ) {

        $old          = $this->result;
        $this->result = $result;

        return $old;
    }

    /**
     * Get the result of the validation.
     *
     * @access  public
     * @return  bool
     */
    public function getResult ( ) {

        return $this->result;
    }
}
