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

/**
 * Class \Hoa\Acl\User.
 *
 * A user is a role —an actor— that can own zero or more services and can belong
 * to zero or more groups.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class User
{
    /**
     * User ID.
     *
     * @var mixed
     */
    protected $_id       = null;

    /**
     * User label.
     *
     * @var string
     */
    protected $_label    = null;

    /**
     * Services.
     *
     * @var array
     */
    protected $_services = [];



    /**
     * Built a new user.
     *
     * @param   mixed   $id       The user ID.
     * @param   string  $label    The user label.
     */
    public function __construct($id, $label = null)
    {
        $this->setId($id);
        $this->setLabel($label);

        return;
    }

    /**
     * Add services.
     *
     * @param   array  $services    Services to add.
     * @return  \Hoa\Acl\User
     * @throws  \Hoa\Acl\Exception
     */
    public function addServices(array $services = [])
    {
        foreach ($services as $service) {
            if (!($service instanceof Service)) {
                throw new Exception(
                    'Service %s must be an instance of Hoa\Acl\Service.',
                    0,
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
     * Delete services.
     *
     * @param   array  $services    Service to add.
     * @return  \Hoa\Acl\User
     * @throws  \Hoa\Acl\Exception
     */
    public function deleteServices(array $services = [])
    {
        foreach ($services as $service) {
            if (!($service instanceof Service)) {
                throw new Exception(
                    'Service %s must be an instance of Hoa\Acl\Service.',
                    1,
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
     * Check if a service exists or not.
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
     * Get a specific service.
     *
     * @param   string  $serviceId    Service ID.
     * @return  \Hoa\Acl\Service
     * @throws  \Hoa\Acl\Exception
     */
    protected function getService($serviceId)
    {
        if (false === $this->serviceExists($serviceId)) {
            throw new Exception('Service %s does not exist.', 2, $serviceId);
        }

        return $this->_services[$serviceId];
    }

    /**
     * Get all services.
     *
     * @return  array
     */
    protected function getServices()
    {
        return $this->_services;
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
     * Get user ID.
     *
     * @return  mixed
     */
    public function getId()
    {
        return $this->_id;
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
     * Get user label.
     *
     * @return  mixed
     */
    public function getLabel()
    {
        return $this->_label;
    }
}
