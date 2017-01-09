<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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
 * A group contains zero or more users, has zero or more permissions and owns
 * zero or more services. Structurally, this is a node of a graph (please, see
 * `Hoa\Graph`) and thus can inherit permissions from other groups. Users and
 * services cannot be inherited. If a group owns a service, this is a shared
 * service because several users can access to it.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Group implements Graph\Node
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
     * Users.
     *
     * @var array
     */
    protected $_users       = [];

    /**
     * Permissions.
     *
     * @var array
     */
    protected $_permissions = [];

    /**
     * Services.
     *
     * @var array
     */
    protected $_services    = [];



    /**
     * Built a new group.
     *
     * @param   mixed   $id       The group ID.
     * @param   string  $label    The group label.
     */
    public function __construct($id, $label = null)
    {
        $this->setId($id);
        $this->setLabel($label);

        return;
    }

    /**
     * Add users.
     *
     * @param   array  $users    Users to add.
     * @return  \Hoa\Acl\Group
     * @throws  \Hoa\Acl\Exception
     */
    public function addUsers(array $users = [])
    {
        foreach ($users as $user) {
            if (!($user instanceof User)) {
                throw new Exception(
                    'User %s must be an instance of Hoa\Acl\User.',
                    0,
                    $user
                );
            }

            $id = $user->getId();

            if (true === $this->userExists($id)) {
                continue;
            }

            $this->_users[$id] = $user;
        }

        return $this;
    }

    /**
     * Delete users.
     *
     * @param   array  $users    User to add.
     * @return  \Hoa\Acl\Group
     * @throws  \Hoa\Acl\Exception
     */
    public function deleteUsers(array $users = [])
    {
        foreach ($users as $user) {
            if (!($user instanceof User)) {
                throw new Exception(
                    'User %s must be an instance of Hoa\Acl\User.',
                    1,
                    $user
                );
            }

            $id = $user->getId();

            if (false === $this->userExists($id)) {
                continue;
            }

            unset($this->_users[$id]);
        }

        return $this;
    }

    /**
     * Check if a user exists or not.
     *
     * @param   muxed  $userId    User ID (or instance).
     * @return  bool
     */
    public function userExists($userId)
    {
        if ($userId instanceof User) {
            $userId = $userId->getId();
        }

        return isset($this->_users[$userId]);
    }

    /**
     * Get a specific user.
     *
     * @param   string  $userId    User ID.
     * @return  \Hoa\Acl\User
     * @throws  \Hoa\Acl\Exception
     */
    public function getUser($userId)
    {
        if (false === $this->userExists($userId)) {
            throw new Exception('User %s does not exist.', 2, $userId);
        }

        return $this->_users[$userId];
    }

    /**
     * Get all users.
     *
     * @return  array
     */
    public function getUsers()
    {
        return $this->_users;
    }

    /**
     * Add permissions in this group.
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
                    3,
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
     * Delete permissions in this group.
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
                    4,
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
     * Check if a permission exists in this group.
     *
     * @param   mixed  $permissionId    Permission ID.
     * @return  bool
     */
    public function permissionExists($permissionId)
    {
        return isset($this->_permissions[$permissionId]);
    }

    /**
     * Get a specific permission of this group.
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
                5,
                [$permissionId, $this->getLabel()]
            );
        }

        return $this->_permissions[$permissionId];
    }

    /**
     * Get permissions of this group.
     *
     * @return  array
     */
    public function getPermissions()
    {
        return $this->_permissions;
    }

    /**
     * Add shared services.
     *
     * @param   array  $services    Services to add.
     * @return  \Hoa\Acl\Group
     * @throws  \Hoa\Acl\Exception
     */
    public function addServices(array $services = [])
    {
        foreach ($services as $service) {
            if (!($service instanceof Service)) {
                throw new Exception(
                    'Service %s must be an instance of Hoa\Acl\Service.',
                    6,
                    $service
                );
            }

            $id = $service->getId();

            if (true === $this->serviceExists($id)) {
                continue;
            }

            $this->_services[$id] = $service;
        }

        return $this;
    }

    /**
     * Delete shared services.
     *
     * @param   array  $services    Service to add.
     * @return  \Hoa\Acl\Group
     * @throws  \Hoa\Acl\Exception
     */
    public function deleteServices(array $services = [])
    {
        foreach ($services as $service) {
            if (!($service instanceof Service)) {
                throw new Exception(
                    'Service %s must be an instance of Hoa\Acl\Service.',
                    7,
                    $service
                );
            }

            $id = $service->getId();

            if (false === $this->serviceExists($id)) {
                continue;
            }

            unset($this->_services[$id]);
        }

        return $this;
    }

    /**
     * Check if a shared service exists or not.
     *
     * @param   muxed  $serviceId    Service ID (or instance).
     * @return  bool
     */
    public function serviceExists($serviceId)
    {
        if ($serviceId instanceof Service) {
            $serviceId = $serviceId->getId();
        }

        return isset($this->_services[$serviceId]);
    }

    /**
     * Get a specific shared service.
     *
     * @param   string  $serviceId    Service ID.
     * @return  \Hoa\Acl\Service
     * @throws  \Hoa\Acl\Exception
     */
    protected function getService($serviceId)
    {
        if (false === $this->serviceExists($serviceId)) {
            throw new Exception('Service %s does not exist.', 8, $serviceId);
        }

        return $this->_services[$serviceId];
    }

    /**
     * Get all shared services.
     *
     * @return  array
     */
    protected function getServices()
    {
        return $this->_services;
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
     * Get group ID.
     *
     * @return  mixed
     */
    public function getId()
    {
        return $this->_id;
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
