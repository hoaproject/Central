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

namespace Hoa\Notification {

/**
 * Interface \Hoa\Notification\Exception.
 *
 * Notification interface.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
interface Notification
{
    /**
     * Register an application.
     *
     * @param   string  $application    Application name.
     * @param   string  $password       Password.
     * @param   string  $icon           Icon.
     * @return  \Hoa\Notification
     */
    public function register($application, $password = null, $icon = null);

    /**
     * Send a notification.
     *
     * @param   string  $title      Title.
     * @param   string  $message    Message.
     * @param   string  $icon       Icon.
     * @param   string  $sticky     Whether the notification is sticked or not.
     * @return  \Hoa\Notification
     */
    public function notify($title, $message, $icon = null, $sticky = false);
}

}

namespace {

/**
 * Flex entity.
 */
Hoa\Consistency::flexEntity('Hoa\Notification\Notification');

}
