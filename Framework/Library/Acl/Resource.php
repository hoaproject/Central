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
 * @subpackage  Hoa_Acl_Resource
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
 * Hoa_Acl_User
 */
import('Acl.User');

/**
 * Hoa_Acl_Exception
 */
import('Acl.Exception');

/**
 * Class Hoa_Acl_Resource.
 *
 * Describe a resource.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Acl
 * @subpackage  Hoa_Acl_Resource
 */

class Hoa_Acl_Resource {

    /**
     * Resource ID.
     *
     * @var Hoa_Acl_Resource mixed
     */
    protected $resourceId    = null;

    /**
     * Resource label.
     *
     * @var Hoa_Acl_Resource string
     */
    protected $resourceLabel = null;

    /**
     * Collections of all users ID.
     *
     * @var Hoa_Acl_Resource array
     */
    protected $users         = array();



    /**
     * Built a new resource.
     *
     * @access  public
     * @param   mixed   $id       The resource ID.
     * @param   string  $label    The resource label.
     * @return  void
     */
    public function __construct ( $id, $label = null ) {

        $this->setId($id);
        $this->setLabel($label);
    }

    /**
     * Add user.
     *
     * @access  public
     * @param   array   $users    User to add.
     * @return  array
     */
    public function addUser ( $users = array() ) {

        if(!is_array($users))
            $users = array($users);

        foreach($users as $foo => $user) {

            if($user instanceof Hoa_Acl_User)
                $user = $user->getId();

            if(true === $this->userExists($user))
                continue;

            $this->users[$user] = true;
        }

        return $this->getUsers();
    }

    /**
     * Delete user.
     *
     * @access  public
     * @param   array   $users    User to add.
     * @return  array
     * @throw   Hoa_Acl_Exception
     */
    public function deleteUser ( $users = array() ) {

        $users = (array) $users;

        foreach($users as $foo => $user) {

            if($user instanceof Hoa_Acl_User)
                $user = $user->getId();

            if(false === $this->userExists($user))
                continue;

            unset($this->users[$user]);
        }

        return $this->getUsers();
    }

    /**
     * Check if a user exists.
     *
     * @access  public
     * @param   mixed   $userId    The user ID.
     * @return  bool
     */
    public function userExists ( $userId ) {

        if($userId instanceof Hoa_Acl_User)
            $userId = $userId->getId();

        return isset($this->users[$userId]);
    }

    /**
     * Get all users, i.e. the users collection.
     *
     * @access  public
     * @return  array
     */
    public function getUsers ( ) {

        return array_keys($this->users);
    }

    /**
     * Set resource ID.
     *
     * @access  protected
     * @param   mixed      $id    The resource ID.
     * @return  mixed
     */
    protected function setId ( $id ) {

        $old              = $this->resourceId;
        $this->resourceId = $id;

        return $old;
    }

    /**
     * Set resource label.
     *
     * @access  public
     * @param   string  $label    The resource label.
     * @return  string
     */
    public function setLabel ( $label ) {

        $old                 = $this->resourceLabel;
        $this->resourceLabel = $label;

        return $old;
    }

    /**
     * Get resource ID.
     *
     * @access  public
     * @return  mixed
     */
    public function getId ( ) {

        return $this->resourceId;
    }

    /**
     * Get resource label.
     *
     * @access  public
     * @return  mixed
     */
    public function getLabel ( ) {

        return $this->resourceLabel;
    }
}
