<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
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
 * @package     Hoa_Php
 * @subpackage  Hoa_Php_Io_Error
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Php_Io_Exception
 */
import('Php.Io.Exception');

/**
 * Hoa_Php_Io_Out
 */
import('Php.Io.Out');

/**
 * Whether it is not defined.
 */
_define('STDERR', fopen('php://stderr', 'wb'));

/**
 * Class Hoa_Php_Io_Error.
 *
 * Manage the php://stderr stream.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Php
 * @subpackage  Hoa_Php_Io_Error
 */

class Hoa_Php_Io_Error extends Hoa_Php_Io_Out {

    /**
     * Open a stream to php://stderr.
     * Actually, it is a king of singleton because the stream resource is
     * defined in the STDERR constant.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        parent::__construct('php://stderr', null);
    }

    /**
     * Open the stream and return the associated resource.
     *
     * @access  protected
     * @param   string              $streamName    Stream name (e.g. path or URL).
     * @param   Hoa_Stream_Context  $context       Context.
     * @return  resource
     */
    protected function &open ( $streamName, Hoa_Stream_Context $context = null ) {

        $out = STDERR;

        return $out;
    }

    /**
     * Close the current stream.
     * Do not want to close the STDIN stream.
     *
     * @access  public
     * @return  bool
     */
    public function close ( ) {

        return true;
    }
}
