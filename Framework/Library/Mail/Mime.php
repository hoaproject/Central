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
 * @subpackage  Hoa_Mail_Mime
 *
 */

/**
 * Class Hoa_Mail_Mime.
 *
 * Write an email with Multipurpose Internet Mail Extensions.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Mime
 */

class Hoa_Mail_Mime {

    /**
     * Mime version.
     *
     * @const string
     */
    const MIME_VERSION     = '1.0';

    /**
     * Encoding in 8bits.
     *
     * @const string
     */
    const ENCODING_8BIT   = '8bit ';

    /**
     * Encoding in base64.
     *
     * @const string
     */
    const ENCODING_BASE64  = 'base64';

    /**
     * Text body container.
     *
     * @var Hoa_Mail_Mime string
     */
    protected $txtBody     = '';

    /**
     * HTML body container.
     *
     * @var Hoa_Mail_Mime string
     */
    protected $htmlBody    = '';

    /**
     * List of headers.
     *
     * @var Hoa_Mail_Mime array
     */
    protected $header      = array(
        'header'           => array(),
        'body'             => array()
    );

    /**
     * List of attachments.
     *
     * @var Hoa_Mail_Mime array
     */
    protected $part        = array();

    /**
     * End-Of-Line sequence.
     *
     * @var Hoa_Mail_Mime string
     */
    protected $crlf        = CRLF;

    /**
     * Boundary (with prefix).
     *
     * @var Hoa_Mail_Mime string
     */
    protected $boundary    = '_boundary=';

    /**
     * Charset (UTF-8 by default).
     *
     * @var Hoa_Mail_Mime string
     */
    protected $charset     = '';



    /**
     * Set sender object, charset, setEOL (CRLF), some header variables, and boundary
     * (for more documentation, see RFC 2045, 2046 and 2047).
     *
     * @access  public
     * @param   string  $charset     Charset.
     * @param   string  $eol         End-Of-Line sequence.
     * @param   int     $priority    X-Priority level.
     * @return  void
     * @throw   Hoa_Mail_Transport_Exception
     */
    public function __construct ( $charset = 'utf-8', $eol = CRLF, $priority = 3 ) {

        if($this instanceof Hoa_Mail) {

            import('Mail.Transport.Sendmail');
            $this->defaultSender = new Hoa_Mail_Transport_Sendmail();
        }

        if(!empty($eol))
            $this->setEOL($eol);

        $this->setCharset($charset);

        $this->header['header']['MIME-Version'] = self::MIME_VERSION;
        $this->header['header']['Content-Type'] = 'text/plain; charset="' .
                                                  $this->getCharset() . '"';
        $this->header['header']['Date']         = date('D, j M Y H:i:s O');
        $this->header['header']['X-Mailer']     = 'PHP/' . PHP_VERSION;
        $this->setXPriority($priority);

        srand((double) microtime() * 1000000);
        $this->boundary .= md5(rand() . microtime());
    }

    /**
     * Set a content to body of mail.
     *
     * @access  public
     * @param   string  $type        Type of content : 'txt' or 'html'.
     * @param   string  $data        Content in plain text.
     * @param   string  $isFile      File path for content.
     * @param   string  $append      Add to $data or not.
     * @param   string  $encoding    Default encoding.
     * @return  string
     * @throw   Hoa_Mail_Exception
     */
    public function setBodyContent ( $type     = 'txt', $data   = '',
                                     $isFile   = false, $append = false,
                                     $encoding = self::ENCODING_8BIT ) {

        if(empty($type) || ($type != 'txt' && $type != 'html'))
            $type = 'txt';

        if(empty($data))
            throw new Hoa_Mail_Exception(
                'Body content could not be empty.', 0);

        $out = $data;

        if(false !== $isFile)
            if(file_exists($isFile)) {

                $fo      = fopen($isFile, 'rb');
                $content = fread($fo, filesize($isFile));
                fclose($fo);

                if(true === $append)
                    $out .= $content;
                else
                    $out  = $content;
            }
            else
                throw new Hoa_Mail_Exception(
                    'File %s is not found.', 1, $isFile);

        if($type == 'txt') {

            $this->txtBody = $out;
            $this->header['body']['Content-Type'] =
                'text/plain; charset="' . $this->getCharset() . '"';
        }
        elseif($type == 'html') {

            $this->htmlBody = $out;
            $this->header['body']['Content-Type'] =
                'text/html; charset="' . $this->getCharset() . '"';
        }

        $this->header['body']['Content-Transfer-Encoding'] = $encoding;

        return $out;
    }

    /**
     * Prepare an exhaustive list of part.
     *
     * @access  public
     * @param   string  $file        Filepath or file content.
     * @param   string  $cType       Content type.
     * @param   string  $fname       Filename.
     * @param   bool    $isfname     Specify if it is a file or not.
     * @param   string  $encoding    Encoding.
     * @return  array
     * @throw   Hoa_Mail_Exception
     */
    public function addAttachment ( $file,
                                    $cType    = 'application/octet-stream',
                                    $fname    = '',
                                    $isFile   = false,
                                    $encoding = self::ENCODING_BASE64 ) {

        $this->header['header']['Content-Type'] =
            'multipart/mixed; boundary="' . $this->boundary . '"';

        $content = $file;

        if(true === $isFile) {

            if(!file_exists($file))
                throw new Hoa_Mail_Exception(
                    'Attached file is not found.', 2);

            $fo      = fopen($file, 'rb');
            $content = fread($fo, filesize($file));
            fclose($fo);

            if(empty($fname))
                $fname = basename($file);
        }

        if(empty($fname))
            throw new Hoa_Mail_Exception(
                'A file name is needed', 3);

        $this->part[] = array(
            'content'                   => $content,
            'Content-Type'              => $cType . '; name="' . $this->headerEncode($fname) . '"',
            'Content-Transfer-Encoding' => $encoding,
            'Content-Disposition'       => 'attachment; filename="' . $fname . '"'
        );

        return $this->part;
    }

    /**
     * Return an array that contains mail parts : header, body, to and subject.
     *
     * @access  public
     * @return  array
     */
    public function get ( ) {

        $out  = array();
        $html = !empty($this->htmlBody);
        $text = !$html && !empty($this->txtBody);
        $part = !empty($this->part);

        $out['header']  = $this->headerToStr();
        $out['body']    = ($part ? $this->crlf . '--' . $this->boundary . "\n" .
                                   $this->headerToStr('body')
                                 : null) .
                          ($text ? $this->txtBody
                                 : $this->htmlBody) .
                          ($part ? "\n\n" . $this->part() . '--' .
                                   $this->boundary . '--'
                                 : null);
        $out['to']      = $this->protect($this->header['header']['To']);
        $out['from']    = $this->protect($this->header['header']['From']);
        $out['subject'] = $this->protect($this->header['header']['Subject']);

        return $out;
    }

    /**
     * Get all headers (header and body).
     *
     * @access  public
     * @return  array
     */
    public function getHeader ( ) {

        return $this->header;
    }

    /**
     * Convert header to string.
     *
     * @access  protected
     * @param   mixed      $type    Type of header to convert.
     *                              Could be an array, or a string.
     * @return  string
     * @throw   Hoa_Mail_Exception
     */
    protected function headerToStr ( $type = 'header' ) {

        $out = '';

        if(is_array($type))
            $header = $type;

        elseif(is_string($type)) {

            if(isset($this->header[$type]))
                $header = $this->header[$type];
            else
                throw new Hoa_Mail_Exception(
                    'This header type does not exist.', 4);
        }

        foreach($header as $key => $value) {

            switch($key) {

                case 'To':
                case 'Reply-To':
                case 'From':
                case 'Subject':
                    $value = $this->headerEncode($value);
                  break;
            }

            $out .= $key . ': ' . $this->protect($value) . $this->crlf;
        }

        return $out . "\n";
    }

    /**
     * Encode hearders according to current charset.
     *
     * @access  protected
     * @param   string     $header    Header value to encode.
     * @return  string
     * @throw   Hoa_Mail_Exception
     */
    protected function headerEncode ( $header = '' ) {

        if(empty($header))
            throw new Hoa_Mail_Exception('Header string is needed.', 5);

		$header = preg_replace('#((\s)(?!<))#', '_', $header);

		if(preg_match('#\s#', $header))
			list($pre, $app) = preg_split('#[\s]#', $header);
		else {

			$pre = $header;
			$app = '';
		}

		if(preg_match('#([\x80-\xFF]+)#', $pre)) {

			$pre = preg_replace('#([\x80-\xFF])#e', '"=".strtoupper(dechex(ord("\\1")))', $pre);
			$pre = '=?' . $this->getCharset() . '?Q?' . $pre . '?=';
		}
		else
			$pre = str_replace('_', ' ', $pre);

		$pre .= ' ';

		return trim($pre . $app);
    }

    /**
     * Convert and encode attachment to string.
     *
     * @access  protected
     * @return  string
     */
    protected function part ( ) {

        $out = '';

        foreach($this->part as $num => $part) {

            $content = array_shift($part);
            $out    .= '--' . $this->boundary . "\n" .
                       $this->headerToStr($part) .
                       chunk_split(base64_encode($content)) . "\n";
        }

        return $out;
    }

    /**
     * Set End-Of-Line sequence.
     *
     * @access  public
     * @param   string  $eol    End-Of-Line sequence.
     * @return  string
     */
    public function setEOL ( $eol = CRLF ) {

        return $this->crlf = $eol;
    }

    /**
     * Set the recipient.
     *
     * @access  public
     * @param   string  $to    Recipient address.
     * @return  bool
     * @throw   Hoa_Mail_Exception
     */
    public function setTo ( $to = '' ) {

        if(empty($to))
            throw new Hoa_Mail_Exception(
                '"To" could not be empty.', 6);

        $this->header['header']['To'] = '<' . $to . '>';

        return true;
    }

    /**
     * Set the consignor.
     *
     * @access  public
     * @param   string  $from    Consignor address.
     * @param   string  $name    Consignor name.
     * @return  bool
     * @throw   Hoa_Mail_Exception
     */
    public function setFrom ( $from = '', $name = '' ) {

        if(empty($from))
            throw new Hoa_Mail_Exception(
                '"From" could not be empty.', 7);

        $this->header['header']['From'] = (!empty($name) ? $name . ' ' : '') .
                                          '<' . $from . '>';

        return true;
    }

    /*
     * Set Reply-To.
     *
     * @access  public
     * @param   string  $to      Reply-To address.
     * @param   string  $name    Reply-To name.
     * @return  bool
     * @throw   Hoa_Mail_Exception
     */
    public function setReplyTo ( $to = '', $name = '' ) {

        if(empty($to))
            throw new Hoa_Mail_Exception(
                '"Reply-To" could not be empty.', 8);

        $this->header['header']['Reply-To'] = (!empty($name) ? $name . ' ' : '') .
                                              '<' . $to . '>';

        return true;
    }

    /**
     * Set the subject.
     *
     * @access  public
     * @param   string  $subject    The subject.
     * @return  bool
     * @throw   Hoa_Mail_Exception
     */
    public function setSubject ( $subject = '' ) {

        if(empty($subject))
            throw new Hoa_Mail_Exception(
                '"Subject" could not be empty.', 9);

        $this->header['header']['Subject'] = $subject;

        return true;
    }

    /**
     * Add a consignor copy.
     *
     * @access  public
     * @param   array   $cc    The consignor copy.
     * @return  bool
     * @throw   Hoa_Mail_Exception
     */
    public function addCc ( $cc ) {

        if(empty($cc))
            throw new Hoa_Mail_Exception(
                '"Cc" could not be empty.', 10);

        foreach((array) $cc as $mail => $owner) {

            if(is_int($mail))
                $mail = $owner;

            $owner = $this->headerEncode($owner);

            if(!empty($this->header['header']['Cc']))
                $this->header['header']['Cc'] .= ',' . "\n" .
                                                 '    ' . $owner .
                                                 ' <' . $mail . '>';
            else
                $this->header['header']['Cc']  = $owner . ' <' . $mail . '>';
        }

        return true;
    }

    /**
     * Add a blind carbon copy.
     *
     * @access  public
     * @param   array   $bcc    The blind carbon copy.
     * @return  bool
     * @throw   Hoa_Mail_Exception
     */
    public function addBcc ( $bcc ) {

        if(empty($bcc))
            throw new Hoa_Mail_Exception(
                '"Bbc" could not be empty.', 11);

        foreach((array) $bcc as $mail => $owner) {

            if(is_int($mail))
                $mail = $owner;

            $owner = $this->headerEncode($owner);

            if(!empty($this->header['header']['Bcc']))
                $this->header['header']['Bcc'] .= ',' . "\n" .
                                                  '     '.$owner .
                                                  ' <' . $mail . '>';
            else
                $this->header['header']['Bcc']  = $owner . ' <' . $mail . '>';
        }

        return true;
    }

    /*
     * Set X-Priority.
     *
     * @access  public
     * @param   int     $priority    X-Priority level (1 <= x <= 5) ; 1 is the more important.
     * @return  bool
     * @throw   Hoa_Mail_Exception
     */
    public function setXPriority ( $priority = 3 ) {

        if($priority < 1 || $priority > 5)
            throw new Hoa_Mail_Exception(
                '"X-Priority" must belong to the interval [1; 5].', 12);

        $this->header['header']['X-Priority'] = $priority;

        return true;
    }

    /**
     * Define charset.
     *
     * @access  public
     * @return  string
     */
    public function setCharset ( $charset = 'utf-8' ) {

        return $this->charset = $charset;
    }

    /**
     * Get charset.
     *
     * @access  public
     * @return  string
     */
    public function getCharset ( ) {

        return $this->charset;
    }

    /**
     * Protect value of header from blind header injections.
     *
     * @access  public
     * @param   string  $string    String to evaluate.
     * @return  string
     * @throw   Hoa_Mail_Exception
     */
    public function protect ( $string = '' ) {

        if(empty($string))
            throw new Hoa_Mail_Exception(
                'String to protect could not be empty.', 13);

        if(strpos($string, $this->crlf))
            $string = str_replace($this->crlf, '', $string);

        return $string;

    }
}
