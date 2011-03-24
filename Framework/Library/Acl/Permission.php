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
 * Class \Hoa\Acl\Permission.
 *
 * Describe a permission profil.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class Permission {

    /**
     * Permission ID.
     *
     * @var \Hoa\Acl\Permission mixed
     */
    protected $permissionId    = null;

    /**
     * Permission label.
     *
     * @var \Hoa\Acl\Permission string
     */
    protected $permissionLabel = null;



    /**
     * Built a new permission.
     *
     * @access  public
     * @param   mixed   $id       The permission ID.
     * @param   string  $label    The permission label.
     * @return  void
     */
    public function __construct ( $id, $label = null ) {

        $this->setId($id);
        $this->setLabel($label);

        return;
    }

    /**
     * Set permission ID.
     *
     * @access  protected
     * @param   mixed      $id    The permission ID.
     * @return  mixed
     */
    public function setId ( $id ) {

        $old                = $this->permissionId;
        $this->permissionId = $id;

        return $old;
    }

    /**
     * Set permission label.
     *
     * @access  public
     * @param   string  $label    The permission label.
     * @return  string
     */
    public function setLabel ( $label ) {

        $old                   = $this->permissionLabel;
        $this->permissionLabel = $label;

        return $old;
    }

    /**
     * Get permission ID.
     *
     * @access  public
     * @return  mixed
     */
    public function getId ( ) {

        return $this->permissionId;
    }

    /**
     * Get permission label.
     *
     * @access  public
     * @return  mixed
     */
    public function getLabel ( ) {

        return $this->permissionLabel;
    }
}

}
