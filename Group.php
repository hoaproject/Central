<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2016, Hoa community. All rights reserved.
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

namespace Hoa\Acl;

use Hoa\Graph;

/**
 * Class \Hoa\Acl\Group.
 *
 * Describe a group. A group is based on a graph to set up the multi-inheritance
 * of the group.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Group implements Graph\IGraph\Node
{
    /**
     * Group ID.
     *
     * @var mixed
     */
    protected $_id          = null;

    /**
     * Group label.
     *
     * @var string
     */
    protected $_label       = null;

    /**
     * Collections of all permissions.
     *
     * @var array
     */
    protected $_permissions = [];



    /**
     * Built a new group.
     *
     * @param   mixed   $id       The group ID.
     * @param   string  $label    The group label.
     * @return  void
     */
    public function __construct($id, $label = null)
    {
        $this->setId($id);
        $this->setLabel($label);

        return;
    }

    /**
     * Add permissions.
     *
     * @param   array  $permissions    Permissions to add.
     * @return  \Hoa\Acl\Group
     * @throws  \Hoa\Acl\Exception
     */
    public function addPermissions(array $permissions = [])
    {
        foreach ($permissions as $permission) {
            if (!($permission instanceof Permission)) {
                throw new Exception(
                    'Permission %s must be an instance of Hoa\Acl\Permission.',
                    0,
                    $permission
                );
            }

            $id = $permission->getId();

            if (true === $this->permissionExists($id)) {
                continue;
            }

            $this->_permissions[$id] = $permission;
        }

        return $this;
    }

    /**
     * Delete permissions.
     *
     * @param   array  $permissions    Permissions to add.
     * @return  \Hoa\Acl\Group
     * @throws  \Hoa\Acl\Exception
     */
    public function deletePermissions(array $permissions = [])
    {
        foreach ($permissions as $permission) {
            if (!($permission instanceof Permission)) {
                throw new Exception(
                    'Permission %s must be an instance of Hoa\Acl\Permission.',
                    1,
                    $permission
                );
            }

            $id = $permission->getId();

            if (false === $this->permissionExists($id)) {
                continue;
            }

            unset($this->_permissions[$id]);
        }

        return $this;
    }

    /**
     * Check if a permission exists.
     *
     * @param   mixed  $permissionId    Permission ID.
     * @return  bool
     */
    public function permissionExists($permissionId)
    {
        return isset($this->_permissions[$permissionId]);
    }

    /**
     * Get a specific permission.
     *
     * @param   mixed  $permissionId    Permission ID.
     * @return  \Hoa\Acl\Permission
     * @throws  \Hoa\Acl\Exception
     */
    public function getPermission($permissionId)
    {
        if (false === $this->permissionExists($permissionId)) {
            throw new Exception(
                'Permission %s does not exist in the group %s.',
                2,
                [$permissionId, $this->getLabel()]
            );
        }

        return $this->_permissions[$permissionId];
    }

    /**
     * Get all permissions, i.e. the permissions collection.
     *
     * @return  array
     */
    public function getPermissions()
    {
        return $this->_permissions;
    }

    /**
     * Set group ID.
     *
     * @param   mixed  $id    Group ID.
     * @return  mixed
     */
    protected function setId($id)
    {
        $old       = $this->_id;
        $this->_id = $id;

        return $old;
    }

    /**
     * Set group label.
     *
     * @param   string  $label    Group label.
     * @return  string
     */
    public function setLabel($label)
    {
        $old          = $this->_label;
        $this->_label = $label;

        return $old;
    }

    /**
     * Get group ID.
     *
     * @return  mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get group label.
     *
     * @return  mixed
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Get node ID, i.e. group ID (see `Hoa\Graph\IGraph\Node`).
     *
     * @return  mixed
     */
    public function getNodeId()
    {
        return $this->getId();
    }
}
