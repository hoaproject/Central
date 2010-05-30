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
 * @package     Hoa_Version
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Class Hoa_Version.
 *
 * Get version of the framework.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Version
 */

class Hoa_Version {

    /**
     * Hoa Framework version.
     *
     * @const string
     */
    const VERSION           = '0.5.5b';

    /**
     * Hoa Framework previous version.
     *
     * @const string
     */
    const PREVIOUS_VERSION  = '0.5.1b';

    /**
     * Hoa Framework revision number.
     *
     * @const int
     */
    const REVISION          = 984;

    /**
     * Hoa Framework previous revision number.
     *
     * @const int
     */
    const PREVIOUS_REVISION = 600;



    /**
     * Get current Hoa Framework version.
     *
     * @access  public
     * @return  string
     */
    public static function getVersion ( ) {

        return self::VERSION;
    }

    /**
     * Get previous Hoa Framework version.
     *
     * @access  public
     * @return  string
     */
    public static function getPreviousVersion ( ) {

        return self::PREVIOUS_VERSION;
    }

    /**
     * Get current Hoa Framework revision.
     *
     * @access  public
     * @return  int
     */
    public static function getRevision ( ) {

        return self::REVISION;
    }

    /**
     * Get previous Hoa Framework revision.
     *
     * @access  public
     * @return  int
     */
    public static function getPreviousRevision ( ) {

        return self::PREVIOUS_REVISION;
    }

    /**
     * Compare a version with the current Hoa Framework version.
     *
     * @access  public
     * @param   string  $version     Version to compare.
     * @param   string  $operator    Comparison operator.
     * @return  int
     */
    public static function compareVersion ( $version, $operator = '>' ) {

        return version_compare($version, self::getVersion(), $operator);
    }

    /**
     * Compare a revision with the current Hoa Framework revision.
     *
     * @access  public
     * @param   string  $revision    Revision to compare.
     * @param   string  $operator    Comparison operator.
     * @return  int
     */
    public static function compareRevision ( $revision, $operator = '>' ) {

        return version_compare($revision, self::getRevision(), $operator);
    }

    /**
     * Get the version signature.
     *
     * @access  public
     * @return  string
     */
    public static function getSignature ( ) {

        return 'Hoa Framework ' . self::getVersion() .
               ' (' . self::getRevision() . ').' . "\n" .
               'Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.' . "\n" .
               'License GNU GPL <http://gnu.org/licenses/gpl.txt> ' .
               'or /LICENSE.';
    }
}
