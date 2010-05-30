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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Xslt
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Xml_Exception
 */
import('Xml.Exception');

/**
 * Class Hoa_Xslt.
 *
 * Make XSL Transformations.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Xslt
 */

class Hoa_Xslt {

    /**
     * Define XLST processor.
     *
     * @const int
     */
    const XSLT_PROC_SAB    = 1;
    const XSLT_PROC_DOMXML = 2;
    const XSLT_PROC_XPROC  = 4;

    /**
     * Processor options.
     *
     * @const int
     */
    const XSLT_OPS         = XSLT_OPT_SILENT;
    const XSLT_EUS         = XSLT_ERR_UNSUPPORTED_SCHEME;

    /**
     * Sablotron options.
     *
     * @const int
     */
    const XSLT_SABOPT_PPE  = XSLT_SABOPT_PARSE_PUBLIC_ENTITIES;
    const XSLT_SABOPT_DAM  = XSLT_SABOPT_DISABLE_ADDING_META;
    const XSLT_SABOPT_DS   = XSLT_SABOPT_DISABLE_STRIPPING;
    const XSLT_SABOPT_IDNF = XSLT_SABOPT_IGNORE_DOC_NOT_FOUND;
    const XSLT_SABOPT_FTH  = XSLT_SABOPT_FILES_TO_HANDLER;

    /**
     * DomXML options.
     *
     * @const int
     */
    const XSLT_DOMXML_LP   = DOMXML_LOAD_PARSING;
    const XSLT_DOMXML_LV   = DOMXML_LOAD_VALIDATING;
    const XSLT_DOMXML_LR   = DOMXML_LOAD_RECOVERING;
    const XSLT_DOMXML_LDKB = DOMXML_LOAD_DONT_KEEP_BLANKS;
    const XSLT_DOMXML_LSE  = DOMXML_LOAD_SUBSTITUTE_ENTITIES;
    const XSLT_DOMXML_LCA  = DOMXML_LOAD_COMPLETE_ATTRS;


    /**
     * Base.
     *
     * @var Hoa_Xslt string
     */
    protected $base = '';

    /**
     * Encoding.
     *
     * @var Hoa_Xslt string
     */
    protected $encoding = 'utf-8';

    /**
     * Look if a processor is actived _with_ a specific function.
     *
     * @var Hoa_Xslt array
     */
    protected $with = array(
        XSLT_PROC_SAB    => 'xslt_create',
        XSLT_PROC_DOMXML => 'domxml_open_file',
        XSLT_PROC_XPROC  => 'exec'
    );



    /**
     * __construct
     * Set base and encoding.
     *
     * @access  public
     * @param   base      string    Base for XSLT processor.
     * @param   encoding  string    Encoding type.
     * @return  void
     */
    public function __construct ( $base = '', $encoding = 'utf-8' ) {

        $this->base     = empty($base) ? getcwd() . DS : $base;
        $this->encoding = $encoding;
    }

    /**
     * process
     * Make XSL Transformations.
     *
     * @access  public
     * @param   xml     string    XML container.
     * @param   xsl     string    XSL container.
     * @param   proc    int       Processor.
     * @param   output  string    Result container.
     * @param   dir     string    Directory for base.
     * @param   params  array     XSL parameters.
     * @param   opts    array     XSL processor options.
     * @param   silent  bool      Switch off error.
     * @return  string
     * @throw   Hoa_Xml_Exception
     */
    public function process ( $xml, $xsl, $proc = self::XSLT_PROC_SAB,
                              $output = null, $dir = '', $params = array(),
                              $opts = null, $silent = false ) {

        if($this->isActived($proc)) {

            switch($proc) {

                case self::XSLT_PROC_SAB:
                    return $this->sablotron($xml, $xsl, $output, $dir, $params, $opts);
                  break;
                case self::XSLT_PROC_DOMXML:
                    return $this->domxml($xml, $xsl, $output, $dir, $params, $opts, $silent);
                  break;
                case self::XSLT_PROC_XPROC:
                    return $this->xsltproc($xml, $xsl, $output, $params);
                  break;
                default:
                    throw new Hoa_Xml_Exception('XSLT processor is not found.', 0);
            }
        }
        else
            throw new Hoa_Xml_Exception('XSLT processor is not actived.', 1);
    }

    /**
     * sablotron
     * Make XSL Transformations with Sablotron.
     *
     * @access  public
     * @param   xml     string    XML container.
     * @param   xsl     string    XSL container.
     * @param   output  string    Result container.
     * @param   dir     string    Directory for base.
     * @param   param   array     XSL parameters.
     * @param   opts    array     XSL processor options.
     * @return  string
     * @throw   Hoa_Xml_Exception
     */
    public function sablotron ( $xml, $xsl, $output = null, $dir = '',
                                $param = array(), $opts = array()) {

        if(empty($xml))
            throw new Hoa_Xml_Exception('XML could not be empty.', 2);
        if(empty($xsl))
            throw new Hoa_Xml_Exception('XSL could not be empty.', 3);

        $notAllowXml = (bool) version_compare(xslt_backend_version(), '0.95', '>');
        /** @todo
         *    take care about format of $arg and $param if it is in XML or not.
         */

        $arg = array();

        if(preg_match('#<\?xml#', $xml)) {
            $arg['/_xml'] = $xml;
            $xml          = 'arg:/_xml';
        }
        if(preg_match('#<xsl:#', $xsl)) {
            $arg['/_xsl'] = $xsl;
            $xsl          = 'arg:/_xsl';
        }

        $base = OS_WIN ? 'file://' . $this->base . $dir : $this->base . $dir;

        $opt = '';
        foreach((array) $opts as $i => $o) $opt .= $o.' | ';
        $opt = !empty($opt) ? substr($opt, 0, -3) : $opt;


        $xh = xslt_create();
        xslt_set_error_handler($xh, array($this, 'sablotronError'));
        xslt_setopt($xh, $opt);
        xslt_set_base($xh, $base);
        xslt_set_encoding($xh, $this->encoding);
        $res = xslt_process($xh, $xml, $xsl, $output, $arg, $param);
        xslt_free($xh);

        if(!$res)
            throw new Hoa_Xml_Exception('Cannot proceed to XSL Transformations.', 4);

        return $res;
    }

    /**
     * sablotronError
     * Sablotron handler error.
     *
     * @access  private
     * @param   handler  string    HXSL processor.
     * @param   errno    int       Error number.
     * @param   level    int       Error level.
     * @param   info     array     Error infos.
     * @return  string
     * @throw   Hoa_Xml_Exception
     */
    public function sablotronError ( $handler, $errno, $level, $info ) {

        $arg = array($info['module'], $info['msgtype'], $level,
                     $info['msg'], $info['URI'], $info['line']);

        throw new Hoa_Xml_Exception('%s %s (level %d) : %s in : %s at line %d.',
                                    '_' . $errno, $arg);
    }

    /**
     * domxml
     * Make XSL Transformations with DomXML.
     *
     * @access  public
     * @param   xml     string    XML container.
     * @param   xsl     string    XSL container.
     * @param   output  string    Result container.
     * @param   dir     string    Directory for base.
     * @param   param   array     XSL parameters.
     * @param   opts    array     XSL processor options.
     * @param   silent  bool      Switch off error.
     * @return  string
     * @throw   Hoa_Xml_Exception
     */
    public function domxml ( $xml, $xsl, $output = null, $dir = '',
                             $params = array(), $opt = self::XSLT_DOMXML_LP,
                             $silent = false) {

        if(empty($xml))
            throw new Hoa_Xml_Exception('XML could not be empty.', 5);
        if(empty($xsl))
            throw new Hoa_Xml_Exception('XSL could not be empty.', 6);
        if(preg_match('#<\?xml#', $xml))
            throw new Hoa_Xml_Exception('XML must be a file.', 7);
        if(preg_match('#<xsl:#', $xsl))
            throw new Hoa_Xml_Exception('XSL must be a file.', 8);

        $base = $this->base . $dir;

        $xmldoc  = @domxml_open_file($base . $xml, $opt, $feedback);

        if(!$silent && !empty($feedback)) {
            $error = '';
            foreach($feedback as $i => $err)
                $error .= '  - at line ' . $err['line']    .
                          ', col ' . $err['line']          .
                          ' : ' . $err['errormessage']     .
                          ' in ' . urldecode($err['file']) . "\n";
            throw new Hoa_Xml_Exception('DomXML error :' . "\n" . $error, 9);
        }

        $xsldoc  = domxml_xslt_stylesheet_file($base.$xsl);
        $res     = $xsldoc->process($xmldoc, $params);

        if(empty($output)) $res = $xsldoc->result_dump_mem($res);
        else               $res = $xsldoc->result_dump_file($res, $base . $output);

        return $res;
    }

    /**
     * xsltproc
     * Make XSL Transformations with XsltProc.
     *
     * @access  public
     * @param   xml     string    XML container.
     * @param   xsl     string    XSL container.
     * @param   output  string    Result container.
     * @param   param   array     XSL parameters.
     * @return  string
     * @throw   Hoa_Xml_Exception
     */
    public function xsltproc ( $xml, $xsl, $output = null, $params = array() ) {

        if(empty($xml))
            throw new Hoa_Xml_Exception('XML could not be empty.', 10);
        if(empty($xsl))
            throw new Hoa_Xml_Exception('XSL could not be empty.', 11);
        if(preg_match('#<\?xml#', $xml))
            throw new Hoa_Xml_Exception('XML must be a file.', 12);
        if(preg_match('#<xsl:#', $xsl))
            throw new Hoa_Xml_Exception('XSL must be a file.', 13);

        $opt = '';
        if(!empty($output)) {
            $opt    .= '-o ';
            $output .= ' ';
        }

        $param = '';
        foreach($params as $k => $p)
            $param .= escapeshellarg($k . ' "\'' . $p . '\'"') . ' ';
        if(!empty($param))
            $opt  .= '--param ';

        /**
         * cmd :
         * /usr/bin/xsltproc -o --param prmA "'str1'" prmB "'str2'" file.xml xslt.xsl out.html 2>&1
         */
        $cmd = '/usr/bin/xsltproc ' . $opt . $param . $xml . ' ' . $xsl . ' ' . $output . '2>&1';
        exec($cmd, $res);

        return implode("\n", $res);
    }

    /**
     * isActived
     * Look if choosen processor is actived or not.
     *
     * @access  public
     * @param   proc    int       Processor.
     * @param   with    string    With this function.
     * @return  bool
     * @throw   Hoa_Xml_Exception
     */
    public function isActived ( $proc = '', $with = '' ) {

        if(empty($proc))
            throw new Hoa_Xml_Exception('proc could not be empty.', 14);

        $with = empty($with) ? $this->with : array($proc => $with);

        return array_key_exists($proc, $with) ? function_exists($with[$proc]) : false;
    }

    /**
     * setWith
     * Set with variable.
     *
     * @access  public
     * @param   proc    int       Processor.
     * @param   with    string    With this function.
     * @return  array
     * @throw   Hoa_Xml_Exception
     */
    public function setWith ( $proc = '', $with = '' ) {

        if(empty($proc))
            throw new Hoa_Xml_Exception('proc could not be empty.', 15);

        return $this->with[$proc] = $with;
    }

    /**
     * setBase
     * Set base.
     *
     * @access  public
     * @param   base    string    Base.
     * @return  string
     */
    public function setBase ( $base = '' ) {

        return $this->base = $base;
    }

    /**
     * setEncoding
     * Set encoding.
     *
     * @access  public
     * @param   encoding  string    Encoding.
     * @return  string
     */
    public function setEncoding ( $encoding = 'utf-8' ) {

        return $this->encoding = $encoding;
    }
}
