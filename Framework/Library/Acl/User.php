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
 * @subpackage  Hoa_Acl_User
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Acl
 */
import('Acl.~');

/**
 * Hoa_Acl_Group
 */
import('Acl.Group');

/**
 * Hoa_Acl_Exception
 */
import('Acl.Exception');

/**
 * Class Hoa_Acl_User.
 *
 * Describe a user.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Acl
 * @subpackage  Hoa_Acl_User
 */

class Hoa_Acl_User {

    /**
     * User ID.
     *
     * @var Hoa_Acl_User mixed
     */
    protected $userId    = null;

    /**
     * User label.
     *
     * @var Hoa_Acl_User string
     */
    protected $userLabel = null;

    /**
     * Collections of all groups ID.
     *
     * @var Hoa_Acl_User array
     */
    protected $groups    = array();



    /**
     * Built a new user.
     *
     * @access  public
     * @param   mixed   $id       The user ID.
     * @param   string  $label    The user label.
     * @return  void
     */
    public function __construct ( $id, $label = null ) {

        $this->setId($id);
        $this->setLabel($label);
    }

    /**
     * Add group.
     *
     * @access  public
     * @param   array   $groups    Group to add.
     * @return  array
     */
    public function addGroup ( $groups = array() ) {

        if(!is_array($groups))
            $groups = array($groups);

        foreach($groups as $foo => $group) {

            if($group instanceof Hoa_Acl_Group)
                $group = $group->getId();

            if(true === $this->groupExists($group))
                continue;

            $this->groups[$group] = true;
        }

        return $this->getGroups();
    }

    /**
     * Delete group.
     *
     * @access  public
     * @param   array   $groups    Group to add.
     * @return  array
     */
    public function deleteGroup ( $groups = array() ) {

        if(!is_array($groups))
            $groups = array($groups);

        foreach($groups as $foo => $group) {

            if($group instanceof Hoa_Acl_Group)
                $group = $group->getId();

            if(false === $this->groupExists($group))
                continue;

            unset($this->groups[$group]);
        }

        return $this->getGroups();
    }

    /**
     * Check if a group exists.
     *
     * @access  public
     * @param   mixed   $groupId    The group ID.
     * @return  bool
     */
    public function groupExists ( $groupId ) {

        if($groupId instanceof Hoa_Acl_Group)
            $groupId = $groupId->getId();

        return isset($this->groups[$groupId]);
    }

    /**
     * Get all groups, i.e. the groups collection.
     *
     * @access  public
     * @return  array
     */
    public function getGroups ( ) {

        return array_keys($this->groups);
    }

    /**
     * Set user ID.
     *
     * @access  protected
     * @param   mixed      $id    The user ID.
     * @return  mixed
     */
    protected function setId ( $id ) {

        $old          = $this->userId;
        $this->userId = $id;

        return $old;
    }

    /**
     * Set user label.
     *
     * @access  public
     * @param   string  $label    The user label.
     * @return  string
     */
    public function setLabel ( $label ) {

        $old             = $this->userLabel;
        $this->userLabel = $label;

        return $old;
    }

    /**
     * Get user ID.
     *
     * @access  public
     * @return  mixed
     */
    public function getId ( ) {

        return $this->userId;
    }

    /**
     * Get user label.
     *
     * @access  public
     * @return  mixed
     */
    public function getLabel ( ) {

        return $this->userLabel;
    }
}
