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
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Acl_Exception
 */
import('Acl.Exception');

/**
 * Hoa_Acl_User
 */
import('Acl.User');

/**
 * Hoa_Acl_Group
 */
import('Acl.Group');

/**
 * Hoa_Acl_Permission
 */
import('Acl.Permission');

/**
 * Hoa_Acl_Resource
 */
import('Acl.Resource');

/**
 * Hoa_Graph
 */
import('Graph.~');

/**
 * Class Hoa_Alc.
 *
 * The ACL main class. It contains all users, groups, and resources collections.
 * It also proposes to check if a user is allow or not to do an action according
 * to its groups, and resources.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Acl
 */

class Hoa_Acl {

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
     * Instance of Hoa_Acl, make a singleton.
     *
     * @var Hoa_Acl object
     */
    private static $_instance = null;

    /**
     * Array of all users.
     *
     * @var Hoa_Acl array
     */
    protected $users          = array();

    /**
     * Graph of groups.
     *
     * @var Hoa_Acl Hoa_Graph
     */
    protected $groups         = null;

    /**
     * Array of all resources.
     *
     * @var Hoa_Acl array
     */
    protected $resources      = array();



    /**
     * Built an access control list.
     *
     * @access  private
     * @param   bool     $loop    Allow or not loop. Please, see the Hoa_Graph
     *                            class.
     * @return  void
     */
    private function __construct ( $loop = Hoa_Graph::DISALLOW_LOOP ) {

        $this->groups = Hoa_Graph::getInstance(
            Hoa_Graph::TYPE_ADJACENCYLIST,
            $loop
        );
    }

    /**
     * Get the instance of Hoa_Acl, make a singleton.
     *
     * @access  public
     * @param   bool     $loop    Allow or not loop. Please, see the Hoa_Graph
     *                            class.
     * @return  object
     */
    public static function getInstance ( $loop = Hoa_Graph::DISALLOW_LOOP ) {

        if(null === self::$_instance)
            self::$_instance = new self($loop);

        return self::$_instance;
    }

    /**
     * Add a user.
     *
     * @access  public
     * @param   Hoa_Acl_User  $user    User to add.
     * @return  void
     * @throw   Hoa_Acl_Exception
     */
    public function addUser ( Hoa_Acl_User $user ) {

        if($this->userExists($user->getId()))
            throw new Hoa_Acl_Exception(
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

        if($user instanceof Hoa_Acl_User)
            $user = $user->getId();

        unset($this->users[$user]);
    }

    /**
     * Add a group.
     *
     * @access  public
     * @param   Hoa_Acl_Group  $group      Group to add.
     * @param   mixed          $inherit    Group inherit permission from (should
     *                                     be the group ID or the group
     *                                     instance).
     * @return  void
     * @throw   Hoa_Acl_Exception
     */
    public function addGroup ( Hoa_Acl_Group $group, $inherit = array() ) {

        if(!is_array($inherit))
            $inherit = array($inherit);

        foreach($inherit as $foo => &$in)
            if($in instanceof Hoa_Acl_Group)
                $in = $in->getId();

        try {

            $this->getGroups()->addNode($group, $inherit);
        }
        catch ( Hoa_Graph_Exception $e ) {

            throw new Hoa_Acl_Exception($e->getFormattedMessage(), $e->getCode());
        }
    }

    /**
     * Delete a group.
     *
     * @access  public
     * @param   mixed   $groupId       The group ID.
     * @param   bool    $propagate     Propagate the erasure.
     * @return  void
     * @throw   Hoa_Acl_Exception
     */
    public function deleteGroup ( $groupId, $propagate = self::DELETE_RESTRICT ) {

        if($groupId instanceof Hoa_Acl_Group)
            $groupId = $groupId->getId();

        try {

            $this->getGroups()->deleteNode($groupId, $propagate);
        }
        catch ( Hoa_Graph_Exception $e ) {

            throw new Hoa_Acl_Exception($e->getFormattedMessage(), $e->getCode());
        }

        foreach($this->getUsers() as $userId => $user)
            $user->deleteGroup($groupId);
    }

    /**
     * Add a resource.
     *
     * @access  public
     * @param   Hoa_Acl_Resource  $resource    Resource to add.
     * @return  void
     * @throw   Hoa_Acl_Exception
     */
    public function addResource ( Hoa_Acl_Resource $resource ) {

        if($this->resourceExists($resource->getId()))
            throw new Hoa_Acl_Exception(
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

        if($resource instanceof Hoa_Acl_Resource)
            $resource = $resource->getId();

        unset($this->resources[$resource]);
    }

    /**
     * Allow a group to make an action according to permissions.
     *
     * @access  public
     * @param   mixed   $groupId        The group ID.
     * @param   array   $permissions    Collection of permissions.
     * @return  bool
     * @throw   Hoa_Acl_Exception
     */
    public function allow ( $groupId, $permissions = array() ) {

        if(false === $this->groupExists($groupId))
            throw new Hoa_Acl_Exception(
                'Group %s does not exist.', 2, $groupId);

        $this->getGroups()->getNode($groupId)->addPermission($permissions);

        foreach($this->getGroups()->getChild($groupId) as $subGroupId => $group)
            $this->allow($subGroupId, $permissions);
    }

    /**
     * Deny a group to make an action according to permissions.
     *
     * @access  public
     * @param   mixed   $groupId        The group ID.
     * @param   array   $permissions    Collection of permissions.
     * @return  bool
     * @throw   Hoa_Acl_Exception
     */
    public function deny ( $groupId, $permissions = array() ) {

        if($groupId instanceof Hoa_Acl_Group)
            $groupId = $groupId->getId();

        if(false === $this->groupExists($groupId))
            throw new Hoa_Acl_Exception(
                'Group %s does not exist.', 3, $groupId);

        $this->getGroups()->getNode($groupId)->deletePermission($permissions);

        foreach($this->getGroups()->getChild($groupId) as $subGroupId => $group)
            $this->deny($subGroupId, $permissions);
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
     * @throw   Hoa_Acl_Exception
     */
    public function isAllowed ( $user, $permission, $resource = null,
                                Hoa_Acl_Assert_Interface $assert = null ) {

        if($user instanceof Hoa_Acl_User)
            $user       = $user->getId();

        if($permission instanceof Hoa_Acl_Permission)
            $permission = $permission->getId();

        if(is_array($permission))
            throw new Hoa_Acl_Exception(
                'Should check one permission, not a list of permissions.', 4);

        if(null !== $resource && !($resource instanceof Hoa_Acl_Resource))
            $resource = $this->getResource($resource);

        $user = $this->getUser($user);
        $out  = false;

        if(null !== $resource && false === $resource->userExists($user->getId()))
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
     * @throw   Hoa_Acl_Exception
     */
    public function isGroupAllowed ( $group, $permission ) {

        if($group instanceof Hoa_Acl_Group)
            $group      = $group->getId();

        if($permission instanceof Hoa_Acl_Permission)
            $permission = $permission->getId();

        if(is_array($permission))
            throw new Hoa_Acl_Exception(
                'Should check one permission, not a list of permissions.', 5);

        if(false === $this->groupExists($group))
            throw new Hoa_Acl_Exception(
                'Group %s does not exist.', 6, $group);

        return $this->getGroups()->getNode($group)
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

        if($userId instanceof Hoa_Acl_User)
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

        if($groupId instanceof Hoa_Acl_Group)
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

        if($resourceId instanceof Hoa_Acl_Resource)
            $resourceId = $resourceId->getId();

        return isset($this->resources[$resourceId]);
    }

    /**
     * Get a specific user.
     *
     * @access  public
     * @param   string  $userId    The user ID.
     * @return  Hoa_Acl_User
     * @throw   Hoa_Acl_Exception
     */
    public function getUser ( $userId ) {

        if(false === $this->userExists($userId))
            throw new Hoa_Acl_Exception(
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
     * @return  Hoa_Acl_Group
     * @throw   Hoa_Acl_Exception
     */
    public function getGroup ( $groupId ) {

        if(false === $this->groupExists($groupId))
            throw new Hoa_Acl_Exception(
                'Group %s does not exist.', 8, $groupId);

        return $this->getGroups()->getNode($groupId);
    }

    /**
     * Get all groups, i.e. get the groups graph.
     *
     * @access  protected
     * @return  Hoa_Graph
     */
    protected function getGroups ( ) {

        return $this->groups;
    }

    /**
     * Get a specific resource.
     *
     * @access  public
     * @param   string  $resourceId    The resource ID.
     * @return  Hoa_Acl_Resource
     * @throw   Hoa_Acl_Exception
     */
    public function getResource ( $resourceId ) {

        if(false === $this->resourceExists($resourceId))
            throw new Hoa_Acl_Exception(
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
