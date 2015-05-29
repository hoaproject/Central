<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Hoa community. All rights reserved.
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

use Hoa\Core;
use Hoa\Graph;

/**
 * Class \Hoa\Acl.
 *
 * The ACL main class. It contains all users, groups, and services collections.
 * It also proposes to check if a user is allow or not to do an action according
 * to its groups and services.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Acl
{
    /**
     * Propagate delete.
     *
     * @const bool
     */
    const DELETE_CASCADE  = true;

    /**
     * Restricte delete.
     *
     * @const bool
     */
    const DELETE_RESTRICT = false;

    /**
     * Instance of \Hoa\Acl, make a singleton.
     *
     * @var \Hoa\Acl
     */
    private static $_instance = null;

    /**
     * Array of all users.
     *
     * @var array
     */
    protected $users          = [];

    /**
     * Graph of groups.
     *
     * @var \Hoa\Acl \Hoa\Graph
     */
    protected $groups         = null;

    /**
     * Array of all services.
     *
     * @var array
     */
    protected $services       = [];



    /**
     * Built an access control list.
     *
     * @param   bool     $loop    Allow or not loop. Please, see the \Hoa\Graph
     *                            class.
     * @return  void
     */
    private function __construct($loop = Graph::DISALLOW_LOOP)
    {
        $this->groups = Graph::getInstance(
            Graph::TYPE_ADJACENCYLIST,
            $loop
        );

        return;
    }

    /**
     * Get the instance of \Hoa\Acl, make a singleton.
     *
     * @param   bool     $loop    Allow or not loop. Please, see the \Hoa\Graph
     *                            class.
     * @return  object
     */
    public static function getInstance($loop = Graph::DISALLOW_LOOP)
    {
        if (null === static::$_instance) {
            static::$_instance = new static($loop);
        }

        return static::$_instance;
    }

    /**
     * Add a user.
     *
     * @param   \Hoa\Acl\User  $user    User to add.
     * @return  void
     * @throws  \Hoa\Acl\Exception
     */
    public function addUser(User $user)
    {
        if ($this->userExists($user->getId())) {
            throw new Exception(
                'User %s is already registried.',
                0,
                $user->getId()
            );
        }

        $this->users[$user->getId()] = $user;

        return;
    }

    /**
     * Delete a user.
     *
     * @param   mixed   $user    User to delete.
     * @return  void
     */
    public function deleteUser($user)
    {
        if ($user instanceof User) {
            $user = $user->getId();
        }

        unset($this->users[$user]);

        return;
    }

    /**
     * Add a group.
     *
     * @param   \Hoa\Acl\Group  $group      Group to add.
     * @param   mixed           $inherit    Group inherit permission from (should
     *                                      be the group ID or the group
     *                                      instance).
     * @return  void
     * @throws  \Hoa\Acl\Exception
     */
    public function addGroup(Group $group, $inherit = [])
    {
        if (!is_array($inherit)) {
            $inherit = [$inherit];
        }

        foreach ($inherit as &$in) {
            if ($in instanceof Group) {
                $in = $in->getId();
            }
        }

        try {
            $this->getGroups()->addNode($group, $inherit);
        } catch (Graph\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return;
    }

    /**
     * Delete a group.
     *
     * @param   mixed   $groupId       The group ID.
     * @param   bool    $propagate     Propagate the erasure.
     * @return  void
     * @throws  \Hoa\Acl\Exception
     */
    public function deleteGroup($groupId, $propagate = self::DELETE_RESTRICT)
    {
        if ($groupId instanceof Group) {
            $groupId = $groupId->getId();
        }

        try {
            $this->getGroups()->deleteNode($groupId, $propagate);
        } catch (Graph\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        foreach ($this->getUsers() as $userId => $user) {
            $user->deleteGroup($groupId);
        }

        return;
    }

    /**
     * Add a service.
     *
     * @param   \Hoa\Acl\Service  $service    Service to add.
     * @return  void
     * @throws  \Hoa\Acl\Exception
     */
    public function addService(Service $service)
    {
        if ($this->serviceExists($service->getId())) {
            throw new Exception(
                'Service %s is already registried.',
                1,
                $service->getId()
            );
        }

        $this->services[$service->getId()] = $service;

        return;
    }

    /**
     * Delete a service.
     *
     * @param   mixed   $service    Service to delete.
     * @return  void
     */
    public function deleteService($service)
    {
        if ($service instanceof Service) {
            $service = $service->getId();
        }

        unset($this->services[$service]);

        return;
    }

    /**
     * Allow a group to make an action according to permissions.
     *
     * @param   mixed   $groupId        The group ID.
     * @param   array   $permissions    Collection of permissions.
     * @return  bool
     * @throws  \Hoa\Acl\Exception
     */
    public function allow($groupId, $permissions = [])
    {
        if (false === $this->groupExists($groupId)) {
            throw new Exception(
                'Group %s does not exist.',
                2,
                $groupId
            );
        }

        $this->getGroups()->getNode($groupId)->addPermission($permissions);

        foreach ($this->getGroups()->getChild($groupId) as $subGroupId => $group) {
            $this->allow($subGroupId, $permissions);
        }

        return;
    }

    /**
     * Deny a group to make an action according to permissions.
     *
     * @param   mixed   $groupId        The group ID.
     * @param   array   $permissions    Collection of permissions.
     * @return  bool
     * @throws  \Hoa\Acl\Exception
     */
    public function deny($groupId, $permissions = [])
    {
        if ($groupId instanceof Group) {
            $groupId = $groupId->getId();
        }

        if (false === $this->groupExists($groupId)) {
            throw new Exception(
                'Group %s does not exist.',
                3,
                $groupId
            );
        }

        $this->getGroups()->getNode($groupId)->deletePermission($permissions);

        foreach ($this->getGroups()->getChild($groupId) as $subGroupId => $group) {
            $this->deny($subGroupId, $permissions);
        }

        return;
    }

    /**
     * Check if a user is allowed to reach a action according to the permission.
     *
     * @param   mixed   $user          User to check (should be the user ID or
     *                                 the user instance).
     * @param   mixed   $permission    List of permission (should be permission
     *                                 ID, permission instance).
     * @return  bool
     * @throws  \Hoa\Acl\Exception
     */
    public function isAllowed(
        $user,
        $permission,
        $service            = null,
        IAcl\Assert $assert = null
    ) {
        if ($user instanceof User) {
            $user = $user->getId();
        }

        if ($permission instanceof Permission) {
            $permission = $permission->getId();
        }

        if (is_array($permission)) {
            throw new Exception(
                'Should check one permission, not a list of permissions.',
                4
            );
        }

        if (null !== $service &&
            !($service instanceof Service)) {
            $service = $this->getService($service);
        }

        $user = $this->getUser($user);
        $out  = false;

        if (null  !== $service &&
            false === $service->userExists($user->getId())) {
            return false;
        }

        foreach ($user->getGroups() as $groupId) {
            $out |= $this->isGroupAllowed($groupId, $permission);
        }

        $out = (bool) $out;

        if (null === $assert) {
            return $out;
        }

        return $out && $assert->assert();
    }

    /**
     * Check if a group is allowed to reach a action according to the permission.
     *
     * @param   mixed   $group         Group to check (should be the group ID or
     *                                 the group instance).
     * @param   mixed   $permission    List of permission (should be permission
     *                                 ID, permission instance).
     * @return  bool
     * @throws  \Hoa\Acl\Exception
     */
    public function isGroupAllowed($group, $permission)
    {
        if ($group instanceof Group) {
            $group = $group->getId();
        }

        if ($permission instanceof Permission) {
            $permission = $permission->getId();
        }

        if (is_array($permission)) {
            throw new Exception(
                'Should check one permission, not a list of permissions.',
                5
            );
        }

        if (false === $this->groupExists($group)) {
            throw new Exception(
                'Group %s does not exist.',
                6,
                $group
            );
        }

        return
            $this
                ->getGroups()
                ->getNode($group)
                ->permissionExists($permission);
    }

    /**
     * Check if a user exists or not.
     *
     * @param   string  $userId    The user ID.
     * @return  bool
     */
    public function userExists($userId)
    {
        if ($userId instanceof User) {
            $userId = $userId->getId();
        }

        return isset($this->users[$userId]);
    }

    /**
     * Check if a group exists or not.
     *
     * @param   string  $groupId    The group ID.
     * @return  bool
     */
    public function groupExists($groupId)
    {
        if ($groupId instanceof Group) {
            $groupId = $groupId->getId();
        }

        return $this->getGroups()->nodeExists($groupId);
    }

    /**
     * Check if a service exists or not.
     *
     * @param   string  $serviceId    The service ID.
     * @return  bool
     */
    public function serviceExists($serviceId)
    {
        if ($serviceId instanceof Service) {
            $serviceId = $serviceId->getId();
        }

        return isset($this->services[$serviceId]);
    }

    /**
     * Get a specific user.
     *
     * @param   string  $userId    The user ID.
     * @return  \Hoa\Acl\User
     * @throws  \Hoa\Acl\Exception
     */
    public function getUser($userId)
    {
        if (false === $this->userExists($userId)) {
            throw new Exception('User %s does not exist.', 7, $userId);
        }

        return $this->users[$userId];
    }

    /**
     * Get all users.
     *
     * @return  array
     */
    protected function getUsers()
    {
        return $this->users;
    }

    /**
     * Get a specific group.
     *
     * @param   string  $groupId    The group ID.
     * @return  \Hoa\Acl\Group
     * @throws  \Hoa\Acl\Exception
     */
    public function getGroup($groupId)
    {
        if (false === $this->groupExists($groupId)) {
            throw new Exception('Group %s does not exist.', 8, $groupId);
        }

        return $this->getGroups()->getNode($groupId);
    }

    /**
     * Get all groups, i.e. get the groups graph.
     *
     * @return  \Hoa\Graph
     */
    protected function getGroups()
    {
        return $this->groups;
    }

    /**
     * Get a specific service.
     *
     * @param   string  $serviceId    The service ID.
     * @return  \Hoa\Acl\Service
     * @throws  \Hoa\Acl\Exception
     */
    public function getService($serviceId)
    {
        if (false === $this->serviceExists($serviceId)) {
            throw new Exception('Service %s does not exist.', 9, $serviceId);
        }

        return $this->services[$serviceId];
    }

    /**
     * Get all services.
     *
     * @return  array
     */
    protected function getServices()
    {
        return $this->getServices;
    }

    /**
     * Transform the groups to DOT language.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getGroups()->__toString();
    }
}

/**
 * Flex entity.
 */
Core\Consistency::flexEntity('Hoa\Acl\Acl');
