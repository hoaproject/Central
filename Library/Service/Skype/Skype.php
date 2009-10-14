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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Service_Skype
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Service_Skype_Exception
 */
import('Service.Skype.Exception');

/**
 * Class Hoa_Service_Skype.
 *
 * Get skype status from a specific user.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Service_Skype
 */

class Hoa_Service_Skype {

    /**
     * Data address.
     * uri/username.num        : Integer status.
     * uri/username.xml        : XML Document (int status, string lang etc.)
     * uri/username            : Alias of uri/balloon/username.
     * uri/typeOfIcon/username : Type of icon.
     *
     * @var Hoa_Service_Skype string
     */
    protected $uri = 'http://mystatus.skype.com/';

    /**
     * Username.
     *
     * @var Hoa_Service_Skype string
     */
    protected $user = '';

    /**
     * Image directory.
     *
     * @var Hoa_Service_Skype string
     */
    protected $imgDir = '';

    /**
     * Image extension.
     *
     * @var Hoa_Service_Skype string
     */
    protected $imgExt = 'png';

    /**
     * List of icons.
     *
     * @var Hoa_Service_Skype array
     */
    protected $icon = array(
        'smallicon',
        'mediumicon',
        'balloon',
        'smallclassic',
        'bigclassic',
        'dropdown-white',
        'dropdown-trans'
    );

    /**
     * Image HTML tag.
     *
     * @var Hoa_Service_Skype string
     */
    protected $tag = '<img src="%s" width="%d" height="%d" alt="%s" />';

    /**
     * Status.
     *
     * @var Hoa_Service_Skype array
     */
    protected $status = array(
        '0' => 'offline',
        '1' => 'offline',
        '2' => 'online',
        '3' => 'away',
        '4' => 'do_not_available',
        '5' => 'do_not_disturb',
        '7' => 'skype_me',
    );

    /**
     * Langue.
     *
     * @var Hoa_Service_Skype array
     */
    protected $lang = array(
        '0' => 'offline',
        '1' => 'offline',
        '2' => 'online',
        '3' => 'away',
        '4' => 'do not available',
        '5' => 'do not disturb',
        '7' => 'skype me !',
    );



    /**
     * __construct
     * The constructor.
     *
     * @access  public
     * @param   user    string    Username.
     * @return  bool
     */
    public function __construct ( $user = '' ) {

        if(!empty($user))
            $this->user = $user;
    }

    /**
     * getStatus
     * Get Skype user status.
     *
     * @access  public
     * @param   user    string    Username.
     * @param   type    string    Type of result.
     * @param   extra   string    ImgDir, or TypeOfIcon.
     * @return  string
     * @throw   Hoa_Service_Skype_Exception
     */
    public function getStatus ( $user = '', $type = 'int', $extra = '' ) {

        if(empty($user))
            $user  = $this->user;

        if(empty($user))
            throw new Hoa_Service_Skype_Exception(
                'Username could not be empty.', 0);

        if(false === file_get_contents($this->uri . $user . '.num'))
            throw new Hoa_Service_Skype_Exception(
                'Username %s does not exist.', 1, $user);

        if(empty($extra)     &&  in_array($extra, $this->icon))
            $extra = $this->icon['smallicon'];
        elseif(empty($extra) && !in_array($extra, $this->icon))
            $extra = $this->imgDir;


        switch($type) {

            case 'int':
                $out = (int) file_get_contents($this->uri . $user . '.num');
              break;

            case 'txt':
                $out = (int) file_get_contents($this->uri . $user . '.num');
                $out = $this->lang[$out];
              break;

            case 'img':
                if(!in_array($extra, $this->icon))
                    throw new Hoa_Service_Skype_Exception(
                        '"%s" icon type does not exist.', 2, $extra);

                header('content-type: image/png');

                $out = imagecreatefrompng($this->uri . $extra . '/' . $user);
                $out = !$out ? null : imagepng($out);
              break;

            case 'html':
                $out = (int) file_get_contents($this->uri . $user . '.num');
                $out = $this->getHTML($out, $extra);
              break;

            default:
                $out = (int) file_get_contents($this->uri . $user . '.num');
        }

        return $out;
    }

    /**
     * getStatusInt
     * Alias of getStatus.
     *
     * @access  public
     * @param   user    string    Username.
     * @return  string
     */
    public function getStatusInt ( $user = '' ) {

        return $this->getStatus($user, 'int');
    }

    /**
     * getStatusTxt
     * Alias of getStatus.
     *
     * @access  public
     * @param   user    string    Username.
     * @return  string  
     */
    public function getStatusTxt ( $user = '' ) {

        return $this->getStatus($user, 'txt');
    }

    /**
     * getStatusImg
     * Alias of getStatus.
     *
     * @access  public
     * @param   user    string    Username.
     * @param   icon    string    Type of icon.
     * @return  string
     */
    public function getStatusImg ( $user = '', $icon = 'smallicon' ) {

        return $this->getStatus($user, 'img', $icon);
    }

    /**
     * getStatusHTML
     * Alias of getStatus.
     *
     * @access  public
     * @param   user    string    Username.
     * @param   imgDir  string    Image directory.
     * @return  string

     */
    public function getStatusHTML ( $user = '', $imgDir = '' ) {

        return $this->getStatus($user, 'html', $imgDir);
    }

    /**
     * getHTML
     * Get HTML string for getStatusImg.
     *
     * @access  private
     * @param   int      int       Status casted in integer.
     * @param   imgDir   string    Image directory.
     * @param   imgExt   string    Image extension.
     * @return  string
     */
    public function getHTML ( $int = '', $imgDir = '', $imgExt = '') {

        if(empty($imgDir))
            $imgDir = $this->imgDir;

        if(empty($imgExt))
            $imgExt = '.' . $this->imgExt;

        if(empty($imgDir))
            throw new Hoa_Service_Skype_Exception(
                'Image directory could not be empty.', 3);

        if(empty($imgExt))
            throw new Hoa_Service_Skype_Exception(
                'Image extension could not be empty.', 4);

        if(false === file_exists($imgDir . $this->status[$int] . $imgExt))
            throw new Hoa_Service_Skype_Exception(
                'File %s does not exists',
                5, array($imgDir . $this->status[$int] . $imgExt));

        if(false === $imgInfo = @getimagesize($imgDir.$this->status[$int].$imgExt))
            throw new Hoa_Service_Skype_Exception(
                'An error has occured with getimagesize().', 6);

        $args = array(
            $imgDir.$this->status[$int].$imgExt,
            $imgInfo[0],
            $imgInfo[1],
            $this->status[$int]
        );

        return vsprintf($this->tag, $args);
    }

    /**
     * setHTML
     * Set HTML tag.
     *
     * @access  public
     * @param   tag     string    HTML tag (<img />)
     * @return  string
     */
    public function setHTML ( $tag = '' ) {

        return $this->tag = $tag;
    }

    /**
     * setImgDir
     * Set image directory.
     *
     * @access  public
     * @param   imgDir  string    Image directory.
     * @return  string
     */
    public function setImgDir ( $imgDir = '' ) {

        return $this->imgDir = $imgDir;
    }

    /**
     * setImgExt
     * Set image extension.
     *
     * @access  public
     * @param   imgExt  string    Image extension.
     * @return  string
     */
    public function setImgExt ( $imgExt = '' ) {

        return $this->imgExt = $imgExt;
    }

    /**
     * lang
     * Set lang.
     *
     * @access  public
     * @param   lang    array    Lang.
     * @return  array
     */
    public function setLang ( $lang = '' ) {

        return $this->lang = $lang;
    }
}
