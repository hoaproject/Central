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

/**
 * Class \Hoa\Acl\Resource.
 *
 * Describe a resource.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Resource
{
    /**
     * Resource ID.
     *
     * @var mixed
     */
    protected $resourceId    = null;

    /**
     * Resource label.
     *
     * @var string
     */
    protected $resourceLabel = null;

    /**
     * Collections of all users ID.
     *
     * @var array
     */
    protected $users         = [];



    /**
     * Built a new resource.
     *
     * @param   mixed   $id       The resource ID.
     * @param   string  $label    The resource label.
     * @return  void
     */
    public function __construct($id, $label = null)
    {
        $this->setId($id);
        $this->setLabel($label);

        return;
    }

    /**
     * Add user.
     *
     * @param   array   $users    User to add.
     * @return  array
     */
    public function addUser($users = [])
    {
        if (!is_array($users)) {
            $users = [$users];
        }

        foreach ($users as $user) {
            if ($user instanceof User) {
                $user = $user->getId();
            }

            if (true === $this->userExists($user)) {
                continue;
            }

            $this->users[$user] = true;
        }

        return $this->getUsers();
    }

    /**
     * Delete user.
     *
     * @param   array   $users    User to add.
     * @return  array
     */
    public function deleteUser($users = [])
    {
        $users = (array) $users;

        foreach ($users as $user) {
            if ($user instanceof User) {
                $user = $user->getId();
            }

            if (false === $this->userExists($user)) {
                continue;
            }

            unset($this->users[$user]);
        }

        return $this->getUsers();
    }

    /**
     * Check if a user exists.
     *
     * @param   mixed   $userId    The user ID.
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
     * Get all users, i.e. the users collection.
     *
     * @return  array
     */
    public function getUsers()
    {
        return array_keys($this->users);
    }

    /**
     * Set resource ID.
     *
     * @param   mixed      $id    The resource ID.
     * @return  mixed
     */
    protected function setId($id)
    {
        $old              = $this->resourceId;
        $this->resourceId = $id;

        return $old;
    }

    /**
     * Set resource label.
     *
     * @param   string  $label    The resource label.
     * @return  string
     */
    public function setLabel($label)
    {
        $old                 = $this->resourceLabel;
        $this->resourceLabel = $label;

        return $old;
    }

    /**
     * Get resource ID.
     *
     * @return  mixed
     */
    public function getId()
    {
        return $this->resourceId;
    }

    /**
     * Get resource label.
     *
     * @return  mixed
     */
    public function getLabel()
    {
        return $this->resourceLabel;
    }
}
