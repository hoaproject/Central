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
 * Class \Hoa\Acl\User.
 *
 * Describe a user.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class User
{
    /**
     * User ID.
     *
     * @var mixed
     */
    protected $_id     = null;

    /**
     * User label.
     *
     * @var string
     */
    protected $_label  = null;

    /**
     * Collections of all groups ID.
     *
     * @var array
     */
    protected $_groups = [];



    /**
     * Built a new user.
     *
     * @param   mixed   $id       The user ID.
     * @param   string  $label    The user label.
     * @return  void
     */
    public function __construct($id, $label = null)
    {
        $this->setId($id);
        $this->setLabel($label);

        return;
    }

    /**
     * Add groups.
     *
     * @param   array  $groups    Group to add.
     * @return  \Hoa\Acl\User
     * @throws  \Hoa\Acl\Exception
     */
    public function addGroups(array $groups = [])
    {
        foreach ($groups as $group) {
            if (!($group instanceof Group)) {
                throw new Exception(
                    'Group %s must be an instance of Hoa\Acl\Group.',
                    0,
                    $group
                );
            }

            $id = $group->getId();

            if (true === $this->groupExists($id)) {
                continue;
            }

            $this->_groups[$id] = $group;
        }

        return $this;
    }

    /**
     * Delete groups.
     *
     * @param   array  $groups    Group to add.
     * @return  \Hoa\Acl\User
     * @throws  \Hoa\Acl\Exception
     */
    public function deleteGroups(array $groups = [])
    {
        foreach ($groups as $group) {
            if (!($group instanceof Group)) {
                throw new Exception(
                    'Group %s must be an instance of Hoa\Acl\Group.',
                    1,
                    $group
                );
            }

            $id = $group->getId();

            if (false === $this->groupExists($id)) {
                continue;
            }

            unset($this->_groups[$id]);
        }

        return $this;
    }

    /**
     * Check if a group exists.
     *
     * @param   mixed  $groupId    Group ID.
     * @return  bool
     */
    public function groupExists($groupId)
    {
        return isset($this->_groups[$groupId]);
    }

    /**
     * Get a specific group.
     *
     * @param   mixed  $groupId    Group ID.
     * @return  \Hoa\Acl\User
     * @throws  \Hoa\Acl\Exception
     */
    public function getGroup($groupId)
    {
        if (false === $this->groupExists($groupId)) {
            throw new Exception(
                'Group %s does not exist in the user %s.',
                1,
                [$groupId, $this->getLabel()]
            );
        }

        return $this->_groups[$groupId];
    }

    /**
     * Get all groups, i.e. the groups collection.
     *
     * @return  array
     */
    public function getGroups()
    {
        return $this->_groups;
    }

    /**
     * Set user ID.
     *
     * @param   mixed  $id    User ID.
     * @return  mixed
     */
    protected function setId($id)
    {
        $old       = $this->_id;
        $this->_id = $id;

        return $old;
    }

    /**
     * Set user label.
     *
     * @param   string  $label    User label.
     * @return  string
     */
    public function setLabel($label)
    {
        $old          = $this->_label;
        $this->_label = $label;

        return $old;
    }

    /**
     * Get user ID.
     *
     * @return  mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get user label.
     *
     * @return  mixed
     */
    public function getLabel()
    {
        return $this->_label;
    }
}
