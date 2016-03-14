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

use Hoa\Consistency;
use Hoa\Graph;

/**
 * Class \Hoa\Acl.
 *
 * The ACL main class. It contains all users, groups, and services collections.
 * It also proposes to check if a user is allow or not to do an action according
 * to its groups and services.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
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
     * Restrict delete.
     *
     * @const bool
     */
    const DELETE_RESTRICT = false;

    /**
     * All users.
     *
     * @var array
     */
    protected $_users    = [];

    /**
     * Graph of groups.
     *
     * @var \Hoa\Graph
     */
    protected $_groups   = null;

    /**
     * All services.
     *
     * @var array
     */
    protected $_services = [];



    /**
     * Built an access control list.
     *
     * @param   bool  $loop    Allow or not loop. Please, see the `Hoa\Graph`.
     * @return  void
     */
    public function __construct($loop = Graph::DISALLOW_LOOP)
    {
        $this->_groups = new Graph\AdjacencyList($loop);

        return;
    }

    /**
     * Add a group.
     *
     * @param   \Hoa\Acl\Group  $group      Group to add.
     * @param   array           $parents    Parent groups (will inherit
     *                                      permissions).
     * @return  \Hoa\Acl\Acl
     * @throws  \Hoa\Acl\Exception
     */
    public function addGroup(Group $group, array $parents = [])
    {
        $parentIds = [];

        foreach ($parents as $parent) {
            if (!($parent instanceof Group)) {
                throw new Exception(
                    'Group %s must be an instance of Hoa\Acl\Group.',
                    1,
                    $parent
                );
            }

            $parentIds[] = $parent->getId();
        }

        try {
            $this->getGroups()->addNode($group, $parentIds);
        } catch (Graph\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    /**
     * Delete a group.
     *
     * @param   \Hoa\Acl\Group  $group        Group.
     * @param   bool            $propagate    Propagate the erasure.
     * @return  \Hoa\Acl\Acl
     * @throws  \Hoa\Acl\Exception
     */
    public function deleteGroup(Group $group, $propagate = self::DELETE_RESTRICT)
    {
        $id = $group->getId();

        try {
            $this->getGroups()->deleteNode($id, $propagate);
        } catch (Graph\Exception $e) {
            throw new Exception(
                'Apparently it is not possible to delete the group %s, ' .
                'probably because it has at least one child.',
                42,
                $id,
                $e
            );
        }

        return $this;
    }

    /**
     * Check if a group exists or not.
     *
     * @param   mixed  $groupId    Group ID.
     * @return  bool
     */
    public function groupExists($groupId)
    {
        return $this->getGroups()->nodeExists($groupId);
    }

    /**
     * Get a specific group.
     *
     * @param   string  $groupId    The group ID.
     * @return  \Hoa\Acl\Group
     * @throws  \Hoa\Acl\Exception
     */
    protected function getGroup($groupId)
    {
        if (false === $this->groupExists($groupId)) {
            throw new Exception('Group %s does not exist.', 6, $groupId);
        }

        return $this->getGroups()->getNode($groupId);
    }

    /**
     * Get all groups, i.e. get the underlying graph.
     *
     * @return  \Hoa\Graph
     */
    protected function getGroups()
    {
        return $this->_groups;
    }

    /**
     * Allow a group to make an action according to permissions.
     *
     * @param   mixed  $groupId        Group ID (or instance).
     * @param   array  $permissions    Collection of permissions.
     * @return  bool
     * @throws  \Hoa\Acl\Exception
     */
    public function allow($groupId, array $permissions = [])
    {
        if ($groupId instanceof Group) {
            $groupId = $groupId->getId();
        }

        if (false === $this->groupExists($groupId)) {
            throw new Exception(
                'Group %s does not exist, cannot add permissions.',
                2,
                $groupId
            );
        }

        $this->getGroups()->getNode($groupId)->addPermission($permissions);

        foreach ($this->getGroups()->getChild($groupId) as $subGroupId => $_) {
            $this->allow($subGroupId, $permissions);
        }

        return;
    }

    /**
     * Deny a group to make an action according to permissions.
     *
     * @param   mixed  $groupId        Group ID (or instance).
     * @param   array  $permissions    Collection of permissions.
     * @return  bool
     * @throws  \Hoa\Acl\Exception
     */
    public function deny($groupId, array $permissions = [])
    {
        if ($groupId instanceof Group) {
            $groupId = $groupId->getId();
        }

        if (false === $this->groupExists($groupId)) {
            throw new Exception(
                'Group %s does not exist, cannot delete permissions.',
                3,
                $groupId
            );
        }

        $this->getGroups()->getNode($groupId)->deletePermission($permissions);

        foreach ($this->getGroups()->getChild($groupId) as $subGroupId => $_) {
            $this->deny($subGroupId, $permissions);
        }

        return;
    }

    /**
     * Check if a user is allowed to reach an action according to the permission.
     *
     * @param   mixed               $userId          User ID (or instance).
     * @param   mixed               $permissionId    Permission ID (or instance).
     * @param   mixed               $serviceId       Service ID (or instance).
     * @param   Hoa\Acl\Assertable  $assert          Assert.
     * @return  bool
     * @throws  \Hoa\Acl\Exception
     */
    public function isAllowed(
        $userId,
        $permissionId,
        $serviceId         = null,
        Assertable $assert = null
    ) {
        if ($userId instanceof User) {
            $user   = $userId;
            $userId = $userId->getId();
        } else {
            $user = $this->getUser($userId);
        }

        if ($permissionId instanceof Permission) {
            $permissionId = $permissionId->getId();
        }

        $service = null;

        if (null !== $serviceId) {
            if ($serviceId instanceof Service) {
                $service = $serviceId;
            } else {
                $service = $this->getService($serviceId);
            }

            if (false === $service->userExists($userId)) {
                return false;
            }
        }

        $out  = false;

        foreach ($user->getGroups() as $groupId) {
            $out |= $this->isGroupAllowed($groupId, $permissionId);
        }

        $out = (bool) $out;

        if (null === $assert) {
            return $out;
        }

        return $out && $assert->assert();
    }

    /**
     * Check if a group is allowed to reach an action according to the permission.
     *
     * @param   mixed  $groupId         Group ID (or instance).
     * @param   mixed  $permissionId    Permission ID (or instance).
     * @return  bool
     * @throws  \Hoa\Acl\Exception
     */
    public function isGroupAllowed($groupId, $permissionId)
    {
        if ($groupId instanceof Group) {
            $groupId = $groupId->getId();
        }

        if ($permissionId instanceof Permission) {
            $permissionId = $permissionId->getId();
        }

        if (false === $this->groupExists($groupId)) {
            throw new Exception(
                'Group %s does not exist, cannot check if allowed to do ' .
                'something.',
                4,
                $groupId
            );
        }

        return
            $this
                ->getGroups()
                ->getNode($groupId)
                ->permissionExists($permissionId);
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
Consistency::flexEntity('Hoa\Acl\Acl');
