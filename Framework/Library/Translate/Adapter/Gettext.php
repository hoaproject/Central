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
 * @package     Hoa_Translate
 * @subpackage  Hoa_Translate_Adapter_Gettext
 *
 */

/**
 * Hoa_Translate_Adapter_Abstract
 */
import('Translate.Adapter.Abstract');

/**
 * Hoa_File_Read
 */
import('File.Read');

/**
 * Class Hoa_Translate_Adapter_Gettext.
 *
 * Gettext adapter (for Unix and Windows).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Translate
 * @subpackage  Hoa_Translate_Adapter_Gettext
 */

class Hoa_Translate_Adapter_Gettext extends Hoa_Translate_Adapter_Abstract {

    /**
     * Current filename.
     *
     * @var Hoa_Translate_Adapter_Gettext string
     */
    protected $filename = '';

    /**
     * Big endian byte order if true. Else little endian.
     *
     * @var Hoa_Translate_Adapter_Gettext bool
     */
    private $_bigEndianByte = false;



    /**
     * __construct
     * Set options.
     *
     * @access  public
     * @param   path    string    Path to locale directory.
     * @param   locale  string    Locale value.
     * @param   domain  string    Domain.
     * @return  void
     * @throw   Hoa_Translate_Exception
     */
    public function __construct ( $path = '', $locale = '', $domain = null ) {

        parent::__construct($path, $locale, $domain);;
    }

    /**
     * setDomain
     * Set domain and filename, and unpack data.
     *
     * @access  public
     * @param   domain  string    Domain.
     * @param   header  bool      If file got headers : true, else : false.
     * @return  string
     * @throw   Hoa_Translate_Exception
     */
    public function setDomain ( $domain = '', $header = true ) {

        $filename = $this->path . $this->locale .
                    '/LC_MESSAGES/' . $domain . '.mo';

        if(!file_exists($filename))
            throw new Hoa_Translate_Exception('Filename %s is not found.',
                1, $filename);

        $old              = $this->filename;
        $this->filename   = new Hoa_File_Read($filename);
        $this->_translate = $this->unpackData($header);

        return $old;
    }

    /**
     * unpackData
     * Unpack data from domain.
     * Help could be found here : http://gnu.org/software/gettext/manual/gettext.txt.
     * See chapter "10.3 : The Format of GNU MO Files".
     *
     * @access  protected
     * @return  array
     */
    protected function unpackData ( ) {

        $this->filename->seek(0);

        /**
         * The first two words serve the identification of the file.
         * The magic number will always signal GNU MO files. 
         * The number is stored in the byte order of the generating machine,
         * so the magic number really is two numbers: `0x950412de' and `0xde120495'.
         **/
        $magicNumber = $this->_read(1);

        switch(dechex($magicNumber)) {

            case '950412de':
                $this->_bigEndianByte = false;
              break;

            case 'de120495':
                $this->_bigEndianByte = true;
              break;

            default:
                throw new Hoa_Translate_Exception('%s is not a GNU MO file.', 0);
        }

        /**
         * For explain the following code, we will look this table :
         *
         *         byte
         *               +------------------------------------------+
         *            0  | magic number = 0x950412de                |
         *               |                                          |
         *            4  | file format revision = 0                 |
         *               |                                          |
         *            8  | number of strings                        |  == N
         *               |                                          |
         *           12  | offset of table with original strings    |  == O
         *               |                                          |
         *           16  | offset of table with translation strings |  == T
         *               |                                          |
         *           20  | size of hashing table                    |  == S
         *               |                                          |
         *           24  | offset of hashing table                  |  == H
         *               |                                          |
         *               .                                          .
         *               .    (possibly more entries later)         .
         *               .                                          .
         *               |                                          |
         *            O  | length & offset 0th string  ----------------.
         *        O + 8  | length & offset 1st string  ------------------.
         *                ...                                    ...   | |
         *  O + ((N-1)*8)| length & offset (N-1)th string           |  | |
         *               |                                          |  | |
         *            T  | length & offset 0th translation  ---------------.
         *        T + 8  | length & offset 1st translation  -----------------.
         *                ...                                    ...   | | | |
         *  T + ((N-1)*8)| length & offset (N-1)th translation      |  | | | |
         *               |                                          |  | | | |
         *            H  | start hash table                         |  | | | |
         *                ...                                    ...   | | | |
         *    H + S * 4  | end hash table                           |  | | | |
         *               |                                          |  | | | |
         *               | NUL terminated 0th string  <----------------' | | |
         *               |                                          |    | | |
         *               | NUL terminated 1st string  <------------------' | |
         *               |                                          |      | |
         *                ...                                    ...       | |
         *               |                                          |      | |
         *               | NUL terminated 0th translation  <---------------' |
         *               |                                          |        |
         *               | NUL terminated 1st translation  <-----------------'
         *               |                                          |
         *                ...                                    ...
         *               |                                          |
         *               +------------------------------------------+
         */

        $revision = $this->_read(1);
        $notsh    = array(
            'N'   => $this->_read(1),
            'O'   => $this->_read(1),
            'T'   => $this->_read(1),
            'S'   => $this->_read(1),
            'H'   => $this->_read(1)
        );


        // Prepare original strings array.
        $this->filename->seek($notsh['O']);
        $originalStrOffset    = $this->_read(2 * $notsh['N'], null);

        // Prepare translation strings array.
        $this->filename->seek($notsh['T']);
        $translationStrOffset = $this->_read(2 * $notsh['N'], null);
        $headers              = null;

        for($e = 0, $max = $notsh['N']; $e < $max; $e++) {

            if($originalStrOffset[$e*2+1] == 0) {

                if(!empty($header))
                    continue;

                $this->filename->seek($translationStrOffset[$e * 2 + 2]);
                $headers = $this->filename->read($translationStrOffset[$e * 2 + 1]);
            }

            $this->filename->seek($originalStrOffset[$e * 2 + 2]);
            $key = $this->filename->read($originalStrOffset[$e * 2 + 1]);

            $this->filename->seek($translationStrOffset[$e * 2 + 2]);
            $return[$key] = $this->filename->read($translationStrOffset[$e * 2 + 1]);
        }

        $this->_makeHeaders($headers);

        return $return;
    }

    /**
     * _read
     * Private read MO data.
     *
     * @access  private
     * @param   bytes    int    Bytes.
     * @param   ptr      int    Array pointer.
     * @return  string
     */
    private function _read ( $bytes, $ptr = 1 ) {

        if(false === $this->_bigEndianByte)
            $return = unpack('V' . $bytes, $this->filename->read(4 * $bytes));
        else
            $return = unpack('N' . $bytes, $this->filename->read(4 * $bytes));

        if(isset($return[$ptr]))
            return $return[$ptr];

        return $return;
    }

    /**
     * _makeHeaders
     * Make headers.
     *
     * @access  private
     * @param   headers  string    Headers.
     * @return  void
     */
    private function _makeHeaders ( $headers ) {

        $headers = explode("\n", $headers);
        $return  = array();

        foreach($headers as $type => $value) {

            if(empty($value))
                continue;

            $type  = substr($value, 0, strpos($value, ':'));
            $value = substr($value, strpos($value, ':')+1);

            $return[$type] = trim($value);
        }

        $this->_headers = $return;

        return;
    }
}
