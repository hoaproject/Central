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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Service_ToutMessenger
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Service_ToutMessenger_Exception
 */
import('Service.ToutMessenger.Exception');

/**
 * Hoa_Xml
 */
import('Xml.~');

/**
 * Class Hoa_Service_ToutMessenger.
 *
 * Use ToutMessenger services.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Service_ToutMessenger
 */

class Hoa_Service_ToutMessenger {

    /**
     * URI to ToutMessenger website.
     *
     * @var Hoa_Service_ToutMessenger string
     */
    protected $uri = 'http://www.toutmessenger.info/';

    /**
     * Xml object.
     *
     * @var Hoa_Xml object
     */
    protected $xml = null;

    /**
     * Xml content.
     *
     * @var Hoa_Service_ToutMessenger array
     */
    protected $xmlContent = array();



    /**
     * __construct
     * Initializes parameters.
     *
     * @access  public
     * @param   user    string    Username.
     * @return  void
     * @throw   Hoa_Service_ToutMessenger_Exception
     */
    public function __construct ( $user = '' ) {

        if(empty($user))
            throw new Hoa_Service_ToutMessenger_Exception(
                'User could not be empty.', 0);

        $this->xml        = new Hoa_Xml();
        $this->xmlContent = $this->xml->parse(
                                file_get_contents($this->uri . $user . '.xml'),
                                null
                            );
        $this->xmlContent = $this->xmlContent['Wlm'];
    }

    /**
     * getStatus
     * Get user status.
     *
     * @access  public
     * @return  string
     */
    public function getStatus ( ) {

        return is_array($this->xmlContent['Status'])
            ? implode('', $this->xmlContent['Status'])
            : $this->xmlContent['Status'];
    }

    /**
     * getNick
     * Get user nick.
     *
     * @access  public
     * @return  string
     */
    public function getNick ( ) {

        return is_array($this->xmlContent['Nick'])
            ? implode('', $this->xmlContent['Nick'])
            : $this->xmlContent['Nick'];
    }

    /**
     * getPsm
     * Get user psm (personal message).
     *
     * @access  public
     * @return  string 
     */
    public function getPsm ( ) {

        return is_array($this->xmlContent['Psm'])
            ? implode('', $this->xmlContent['Psm'])
            : $this->xmlContent['Psm'];
    }

    /**
     * getDisplayPicture
     * Get user display picture.
     *
     * @access  public
     * @return  string
     */
    public function getDisplayPicture ( ) {

        return is_array($this->xmlContent['DisplayPicture'])
            ? implode('', $this->xmlContent['DisplayPicture'])
            : $this->xmlContent['DisplayPicture'];
    }
}
