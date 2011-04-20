<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
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
