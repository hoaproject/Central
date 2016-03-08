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

/**
 * Class \Hoa\Acl\Service.
 *
 * Describe a service.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Service
{
    /**
     * Service ID.
     *
     * @var mixed
     */
    protected $_id    = null;

    /**
     * Service label.
     *
     * @var string
     */
    protected $_label = null;

    /**
     * Collections of all users ID.
     *
     * @var array
     */
    protected $_users = [];



    /**
     * Built a new service.
     *
     * @param   mixed   $id       Service ID.
     * @param   string  $label    Service label.
     * @return  void
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
     * @return  \Hoa\Acl\Service
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

            $this->_users[$id] = true;
        }

        return $this;
    }

    /**
     * Delete users.
     *
     * @param   array  $users    User to add.
     * @return  \Hoa\Acl\Service
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

            unset($this->_users[$user]);
        }

        return $this;
    }

    /**
     * Check if a user exists.
     *
     * @param   mixed  $userId    The user ID.
     * @return  bool
     */
    public function userExists($userId)
    {
        return isset($this->_users[$userId]);
    }

    /**
     * Get a specific user.
     *
     * @param   mixed  $userId    User ID.
     * @return  \Hoa\Acl\User
     * @throws  \Hoa\Acl\Exception
     */
    public function getUser($userId)
    {
        if (false === $this->userExists($userId)) {
            throw new Exception(
                'User %s does not exist in the service %s.',
                1,
                [$userId, $this->getLabel()]
            );
        }

        return $this->_users[$userId];
    }

    /**
     * Get all users, i.e. the users collection.
     *
     * @return  array
     */
    public function getUsers()
    {
        return $this->_users;
    }

    /**
     * Set service ID.
     *
     * @param   mixed  $id    Service ID.
     * @return  mixed
     */
    protected function setId($id)
    {
        $old       = $this->_id;
        $this->_id = $id;

        return $old;
    }

    /**
     * Set service label.
     *
     * @param   string  $label    Service label.
     * @return  string
     */
    public function setLabel($label)
    {
        $old          = $this->_label;
        $this->_label = $label;

        return $old;
    }

    /**
     * Get service ID.
     *
     * @return  mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get service label.
     *
     * @return  mixed
     */
    public function getLabel()
    {
        return $this->_label;
    }
}
