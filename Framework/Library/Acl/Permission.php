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
 * @package     Hoa_Acl
 * @subpackage  Hoa_Acl_Permission
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Acl
 */
import('Acl.~');

/**
 * Hoa_Acl_Exception
 */
import('Acl.Exception');

/**
 * Class Hoa_Acl_Permission.
 *
 * Describe a permission profil.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Acl
 * @subpackage  Hoa_Acl_Permission
 */

class Hoa_Acl_Permission {

    /**
     * Permission ID.
     *
     * @var Hoa_Acl_Permission mixed
     */
    protected $permissionId    = null;

    /**
     * Permission label.
     *
     * @var Hoa_Acl_Permission string
     */
    protected $permissionLabel = null;



    /**
     * Built a new permission.
     *
     * @access  public
     * @param   mixed   $id       The permission ID.
     * @param   string  $label    The permission label.
     * @return  void
     */
    public function __construct ( $id, $label = null ) {

        $this->setId($id);
        $this->setLabel($label);
    }

    /**
     * Set permission ID.
     *
     * @access  protected
     * @param   mixed      $id    The permission ID.
     * @return  mixed
     */
    public function setId ( $id ) {

        $old                = $this->permissionId;
        $this->permissionId = $id;

        return $old;
    }

    /**
     * Set permission label.
     *
     * @access  public
     * @param   string  $label    The permission label.
     * @return  string
     */
    public function setLabel ( $label ) {

        $old                   = $this->permissionLabel;
        $this->permissionLabel = $label;

        return $old;
    }

    /**
     * Get permission ID.
     *
     * @access  public
     * @return  mixed
     */
    public function getId ( ) {

        return $this->permissionId;
    }

    /**
     * Get permission label.
     *
     * @access  public
     * @return  mixed
     */
    public function getLabel ( ) {

        return $this->permissionLabel;
    }
}
