<?php

/**
 * Hoa
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
 * \Hoa\Acl\User
 */
-> import('Acl.User')

/**
 * \Hoa\Acl\Group
 */
-> import('Acl.Group')

/**
 * \Hoa\Acl\Permission
 */
-> import('Acl.Permission')

/**
 * \Hoa\Acl\Resource
 */
-> import('Acl.Resource')

/**
 * \Hoa\Graph
 */
-> import('Graph.~');

}

namespace Hoa\Acl {

/**
 * Class \Hoa\Alc.
 *
 * The ACL main class. It contains all users, groups, and resources collections.
 * It also proposes to check if a user is allow or not to do an action according
 * to its groups, and resources.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Acl {

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
     * @var \Hoa\Acl object
     */
    private static $_instance = null;

    /**
     * Array of all users.
     *
     * @var \Hoa\Acl array
     */
    protected $users          = array();

    /**
     * Graph of groups.
     *
     * @var \Hoa\Acl \Hoa\Graph
     */
    protected $groups         = null;

    /**
     * Array of all resources.
     *
     * @var \Hoa\Acl array
     */
    protected $resources      = array();



    /**
     * Built an access control list.
     *
     * @access  private
     * @param   bool     $loop    Allow or not loop. Please, see the \Hoa\Graph
     *                            class.
     * @return  void
     */
    private function __construct ( $loop = \Hoa\Graph::DISALLOW_LOOP ) {

        $this->groups = \Hoa\Graph::getInstance(
            \Hoa\Graph::TYPE_ADJACENCYLIST,
            $loop
        );
    }

    /**
     * Get the instance of \Hoa\Acl, make a singleton.
     *
     * @access  public
     * @param   bool     $loop    Allow or not loop. Please, see the \Hoa\Graph
     *                            class.
     * @return  object
     */
    public static function getInstance ( $loop = \Hoa\Graph::DISALLOW_LOOP ) {

        if(null === self::$_instance)
            self::$_instance = new self($loop);

        return self::$_instance;
    }

    /**
     * Add a user.
     *
     * @access  public
     * @param   \Hoa\Acl\User  $user    User to add.
     * @return  void
     * @throw   \Hoa\Acl\Exception
     */
    public function addUser ( User $user ) {

        if($this->userExists($user->getId()))
            throw new Exception(
                'User %s is already registried.', 0, $user->getId());

        $this->users[$user->getId()] = $user;

        return;
    }

    /**
     * Delete a user.
     *
     * @access  public
     * @param   mixed   $user    User to delete.
     * @return  void
     */
    public function deleteUser ( $user ) {

        if($user instanceof User)
            $user = $user->getId();

        unset($this->users[$user]);

        return;
    }

    /**
     * Add a group.
     *
     * @access  public
     * @param   \Hoa\Acl\Group  $group      Group to add.
     * @param   mixed           $inherit    Group inherit permission from (should
     *                                      be the group ID or the group
     *                                      instance).
     * @return  void
     * @throw   \Hoa\Acl\Exception
     */
    public function addGroup ( Group $group, $inherit = array() ) {

        if(!is_array($inherit))
            $inherit = array($inherit);

        foreach($inherit as $foo => &$in)
            if($in instanceof Group)
                $in = $in->getId();

        try {

            $this->getGroups()->addNode($group, $inherit);
        }
        catch ( \Hoa\Graph\Exception $e ) {

            throw new Exception($e->getFormattedMessage(), $e->getCode());
        }

        return;
    }

    /**
     * Delete a group.
     *
     * @access  public
     * @param   mixed   $groupId       The group ID.
     * @param   bool    $propagate     Propagate the erasure.
     * @return  void
     * @throw   \Hoa\Acl\Exception
     */
    public function deleteGroup ( $groupId, $propagate = self::DELETE_RESTRICT ) {

        if($groupId instanceof Group)
            $groupId = $groupId->getId();

        try {

            $this->getGroups()->deleteNode($groupId, $propagate);
        }
        catch ( \Hoa\Graph\Exception $e ) {

            throw new Exception($e->getFormattedMessage(), $e->getCode());
        }

        foreach($this->getUsers() as $userId => $user)
            $user->deleteGroup($groupId);

        return;
    }

    /**
     * Add a resource.
     *
     * @access  public
     * @param   \Hoa\Acl\Resource  $resource    Resource to add.
     * @return  void
     * @throw   \Hoa\Acl\Exception
     */
    public function addResource ( Resource $resource ) {

        if($this->resourceExists($resource->getId()))
            throw new Exception(
                'Resource %s is already registried.', 1, $resource->getId());

        $this->resources[$resource->getId()] = $resource;

        return;
    }

    /**
     * Delete a resource.
     *
     * @access  public
     * @param   mixed   $resource    Resource to delete.
     * @return  void
     */
    public function deleteResource ( $resource ) {

        if($resource instanceof Resource)
            $resource = $resource->getId();

        unset($this->resources[$resource]);

        return;
    }

    /**
     * Allow a group to make an action according to permissions.
     *
     * @access  public
     * @param   mixed   $groupId        The group ID.
     * @param   array   $permissions    Collection of permissions.
     * @return  bool
     * @throw   \Hoa\Acl\Exception
     */
    public function allow ( $groupId, $permissions = array() ) {

        if(false === $this->groupExists($groupId))
            throw new Exception(
                'Group %s does not exist.', 2, $groupId);

        $this->getGroups()->getNode($groupId)->addPermission($permissions);

        foreach($this->getGroups()->getChild($groupId) as $subGroupId => $group)
            $this->allow($subGroupId, $permissions);

        return;
    }

    /**
     * Deny a group to make an action according to permissions.
     *
     * @access  public
     * @param   mixed   $groupId        The group ID.
     * @param   array   $permissions    Collection of permissions.
     * @return  bool
     * @throw   \Hoa\Acl\Exception
     */
    public function deny ( $groupId, $permissions = array() ) {

        if($groupId instanceof Group)
            $groupId = $groupId->getId();

        if(false === $this->groupExists($groupId))
            throw new Exception(
                'Group %s does not exist.', 3, $groupId);

        $this->getGroups()->getNode($groupId)->deletePermission($permissions);

        foreach($this->getGroups()->getChild($groupId) as $subGroupId => $group)
            $this->deny($subGroupId, $permissions);

        return;
    }

    /**
     * Check if a user is allowed to reach a action according to the permission.
     *
     * @access  public
     * @param   mixed   $user          User to check (should be the user ID or
     *                                 the user instance).
     * @param   mixed   $permission    List of permission (should be permission
     *                                 ID, permission instance).
     * @return  bool
     * @throw   \Hoa\Acl\Exception
     */
    public function isAllowed ( $user, $permission, $resource = null,
                                IAcl\Assert $assert = null ) {

        if($user instanceof User)
            $user       = $user->getId();

        if($permission instanceof Permission)
            $permission = $permission->getId();

        if(is_array($permission))
            throw new Exception(
                'Should check one permission, not a list of permissions.', 4);

        if(   null !== $resource
           && !($resource instanceof Resource))
            $resource = $this->getResource($resource);

        $user = $this->getUser($user);
        $out  = false;

        if(    null !== $resource
           && false === $resource->userExists($user->getId()))
            return false;

        foreach($user->getGroups() as $foo => $groupId)
            $out |= $this->isGroupAllowed($groupId, $permission);

        $out = (bool) $out;

        if(null === $assert)
            return $out;

        return $out && $assert->assert();
    }

    /**
     * Check if a group is allowed to reach a action according to the permission.
     *
     * @access  public
     * @param   mixed   $group         Group to check (should be the group ID or
     *                                 the group instance).
     * @param   mixed   $permission    List of permission (should be permission
     *                                 ID, permission instance).
     * @return  bool
     * @throw   \Hoa\Acl\Exception
     */
    public function isGroupAllowed ( $group, $permission ) {

        if($group instanceof Group)
            $group      = $group->getId();

        if($permission instanceof Permission)
            $permission = $permission->getId();

        if(is_array($permission))
            throw new \Exception(
                'Should check one permission, not a list of permissions.', 5);

        if(false === $this->groupExists($group))
            throw new Exception(
                'Group %s does not exist.', 6, $group);

        return $this->getGroups()
                    ->getNode($group)
                    ->permissionExists($permission);
    }

    /**
     * Check if a user exists or not.
     *
     * @access  public
     * @param   string  $userId    The user ID.
     * @return  bool
     */
    public function userExists ( $userId ) {

        if($userId instanceof User)
            $userId = $userId->getId();

        return isset($this->users[$userId]);
    }

    /**
     * Check if a group exists or not.
     *
     * @access  public
     * @param   string  $groupId    The group ID.
     * @return  bool
     */
    public function groupExists ( $groupId ) {

        if($groupId instanceof Group)
            $groupId = $groupId->getId();

        return $this->getGroups()->nodeExists($groupId);
    }

    /**
     * Check if a resource exists or not.
     *
     * @access  public
     * @param   string  $resourceId    The resource ID.
     * @return  bool
     */
    public function resourceExists ( $resourceId ) {

        if($resourceId instanceof Resource)
            $resourceId = $resourceId->getId();

        return isset($this->resources[$resourceId]);
    }

    /**
     * Get a specific user.
     *
     * @access  public
     * @param   string  $userId    The user ID.
     * @return  \Hoa\Acl\User
     * @throw   \Hoa\Acl\Exception
     */
    public function getUser ( $userId ) {

        if(false === $this->userExists($userId))
            throw new Exception(
                'User %s does not exist.', 7, $userId);

        return $this->users[$userId];
    }

    /**
     * Get all users.
     *
     * @access  protected
     * @return  array
     */
    protected function getUsers ( ) {

        return $this->users;
    }

    /**
     * Get a specific group.
     *
     * @access  public
     * @param   string  $groupId    The group ID.
     * @return  \Hoa\Acl\Group
     * @throw   \Hoa\Acl\Exception
     */
    public function getGroup ( $groupId ) {

        if(false === $this->groupExists($groupId))
            throw new Exception(
                'Group %s does not exist.', 8, $groupId);

        return $this->getGroups()->getNode($groupId);
    }

    /**
     * Get all groups, i.e. get the groups graph.
     *
     * @access  protected
     * @return  \Hoa\Graph
     */
    protected function getGroups ( ) {

        return $this->groups;
    }

    /**
     * Get a specific resource.
     *
     * @access  public
     * @param   string  $resourceId    The resource ID.
     * @return  \Hoa\Acl\Resource
     * @throw   \Hoa\Acl\Exception
     */
    public function getResource ( $resourceId ) {

        if(false === $this->resourceExists($resourceId))
            throw new Exception(
                'Resource %s does not exist.', 9, $resourceId);

        return $this->resources[$resourceId];
    }

    /**
     * Get all resources.
     *
     * @access  protected
     * @return  array
     */
    protected function getResources ( ) {

        return $this->getResources;
    }

    /**
     * Transform the groups to DOT language.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->getGroups()->__toString();
    }
}

}
