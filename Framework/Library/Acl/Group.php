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
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Acl\Exception
 */
-> import('Acl.Exception')

/**
 * \Hoa\Graph\IGraph\Node
 */
-> import('Graph.I~.Node');

}

namespace Hoa\Acl {

/**
 * Class \Hoa\Acl\Group.
 *
 * Describe a group. A group is based on a graph (coding by adjacency list) to
 * set up the multi-inheritance of the group.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Group implements \Hoa\Graph\IGraph\Node {

    /**
     * Group ID.
     *
     * @var \Hoa\Acl\Group mixed
     */
    protected $groupId     = null;

    /**
     * Group label.
     *
     * @var \Hoa\Acl\Group string
     */
    protected $groupLabel  = null;

    /**
     * Collections of all permissions.
     *
     * @var \Hoa\Acl\Group array
     */
    protected $permissions = array();



    /**
     * Built a new group.
     *
     * @access  public
     * @param   mixed   $id       The group ID.
     * @param   string  $label    The group label.
     * @return  void
     */
    public function __construct ( $id, $label = null ) {

        $this->setId($id);
        $this->setLabel($label);

        return;
    }

    /**
     * Add permission.
     *
     * @access  public
     * @param   array   $permissions    Permission to add.
     * @return  array
     * @throw   \Hoa\Acl\Exception
     */
    public function addPermission ( $permissions = array() ) {

        if(!is_array($permissions))
            $permissions = array($permissions);

        foreach($permissions as $foo => $permission) {

            if(!($permission instanceof Permission))
                throw new Exception(
                    'Permission %s must be an instance of \Hoa\Acl\Permission',
                    0, $permission);

            if(true === $this->permissionExists($permission->getId()))
                continue;

            $this->permissions[$permission->getId()] = $permission;
        }

        return $this->getPermissions();
    }

    /**
     * Delete permission.
     *
     * @access  public
     * @param   array   $permissions    Permission to add.
     * @return  array
     * @throw   \Hoa\Acl\Exception
     */
    public function deletePermission ( $permissions = array() ) {

        if(!is_array($permissions))
            $permissions = array($permissions);

        foreach($permissions as $foo => $permission) {

            if($permission instanceof Permission)
                $permission = $permission->getId();

            if(false === $this->permissionExists($permission))
                continue;

            unset($this->permissions[$permission]);
        }

        return $this->getPermissions();
    }

    /**
     * Check if a permission exists.
     *
     * @access  public
     * @param   mixed   $permissionId    The permission ID.
     * @return  bool
     */
    public function permissionExists ( $permissionId ) {

        return isset($this->permissions[$permissionId]);
    }

    /**
     * Get all permissions, i.e. the permissions collection.
     *
     * @access  public
     * @return  array
     */
    public function getPermissions ( ) {

        return $this->permissions;
    }

    /**
     * Set group ID.
     *
     * @access  protected
     * @param   mixed      $id    The group ID.
     * @return  mixed
     */
    protected function setId ( $id ) {

        $old           = $this->groupId;
        $this->groupId = $id;

        return $old;
    }

    /**
     * Set group label.
     *
     * @access  public
     * @param   string  $label    The group label.
     * @return  string
     */
    public function setLabel ( $label ) {

        $old              = $this->groupLabel;
        $this->groupLabel = $label;

        return $old;
    }

    /**
     * Get group ID.
     *
     * @access  public
     * @return  mixed
     */
    public function getId ( ) {

        return $this->groupId;
    }

    /**
     * Get group label.
     *
     * @access  public
     * @return  mixed
     */
    public function getLabel ( ) {

        return $this->groupLabel;
    }

    /**
     * Get node ID, i.e. group ID as well (see
     * \Hoa\Graph\IGraph\Node).
     *
     * @access  public
     * @return  mixed
     */
    public function getNodeId ( ) {

        return $this->getId();
    }
}

}
