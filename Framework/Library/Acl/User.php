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

namespace Hoa\Acl {

/**
 * Class \Hoa\Acl\User.
 *
 * Describe a user.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class User {

    /**
     * User ID.
     *
     * @var \Hoa\Acl\User mixed
     */
    protected $userId    = null;

    /**
     * User label.
     *
     * @var \Hoa\Acl\User string
     */
    protected $userLabel = null;

    /**
     * Collections of all groups ID.
     *
     * @var \Hoa\Acl\User array
     */
    protected $groups    = array();



    /**
     * Built a new user.
     *
     * @access  public
     * @param   mixed   $id       The user ID.
     * @param   string  $label    The user label.
     * @return  void
     */
    public function __construct ( $id, $label = null ) {

        $this->setId($id);
        $this->setLabel($label);

        return;
    }

    /**
     * Add group.
     *
     * @access  public
     * @param   array   $groups    Group to add.
     * @return  array
     */
    public function addGroup ( $groups = array() ) {

        if(!is_array($groups))
            $groups = array($groups);

        foreach($groups as $foo => $group) {

            if($group instanceof Group)
                $group = $group->getId();

            if(true === $this->groupExists($group))
                continue;

            $this->groups[$group] = true;
        }

        return $this->getGroups();
    }

    /**
     * Delete group.
     *
     * @access  public
     * @param   array   $groups    Group to add.
     * @return  array
     */
    public function deleteGroup ( $groups = array() ) {

        if(!is_array($groups))
            $groups = array($groups);

        foreach($groups as $foo => $group) {

            if($group instanceof Group)
                $group = $group->getId();

            if(false === $this->groupExists($group))
                continue;

            unset($this->groups[$group]);
        }

        return $this->getGroups();
    }

    /**
     * Check if a group exists.
     *
     * @access  public
     * @param   mixed   $groupId    The group ID.
     * @return  bool
     */
    public function groupExists ( $groupId ) {

        if($groupId instanceof Group)
            $groupId = $groupId->getId();

        return isset($this->groups[$groupId]);
    }

    /**
     * Get all groups, i.e. the groups collection.
     *
     * @access  public
     * @return  array
     */
    public function getGroups ( ) {

        return array_keys($this->groups);
    }

    /**
     * Set user ID.
     *
     * @access  protected
     * @param   mixed      $id    The user ID.
     * @return  mixed
     */
    protected function setId ( $id ) {

        $old          = $this->userId;
        $this->userId = $id;

        return $old;
    }

    /**
     * Set user label.
     *
     * @access  public
     * @param   string  $label    The user label.
     * @return  string
     */
    public function setLabel ( $label ) {

        $old             = $this->userLabel;
        $this->userLabel = $label;

        return $old;
    }

    /**
     * Get user ID.
     *
     * @access  public
     * @return  mixed
     */
    public function getId ( ) {

        return $this->userId;
    }

    /**
     * Get user label.
     *
     * @access  public
     * @return  mixed
     */
    public function getLabel ( ) {

        return $this->userLabel;
    }
}

}
