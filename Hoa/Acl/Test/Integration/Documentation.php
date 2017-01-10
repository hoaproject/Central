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

namespace Hoa\Acl\Test\Integration;

use Hoa\Acl\Acl as SUT;
use Hoa\Acl as LUT;
use Hoa\Test;

/**
 * Class \Hoa\Acl\Test\Integration\Documentation.
 *
 * Test suite of the documentation.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Documentation extends Test\Integration\Suite implements Test\Decorrelated
{
    public function case_readme()
    {
        $this
            ->given(
                $groupVisitor       = new LUT\Group('group_visitor'),
                $groupBuyer         = new LUT\Group('group_buyer'),
                $groupEditor        = new LUT\Group('group_editor'),
                $groupAdministrator = new LUT\Group('group_administrator'),

                $userAnonymousVisitor = new LUT\User('user_visitor_anonymous'),
                $userLoggedVisitor    = new LUT\User('user_visitor_logged'),
                $userProductEditor    = new LUT\User('user_editor_product'),
                $userBlogEditor       = new LUT\User('user_editor_blog'),

                $permissionRead  = new LUT\Permission('permission_read'),
                $permissionWrite = new LUT\Permission('permission_write'),
                $permissionBuy   = new LUT\Permission('permission_buy'),

                $serviceProduct  = new LUT\Service('service_product'),
                $serviceBlogPage = new LUT\Service('service_blog_page'),

                $acl = new SUT(),

                $groupVisitor->addServices([$serviceProduct, $serviceBlogPage]),
                $groupBuyer->addServices([$serviceProduct, $serviceBlogPage]),
                $userProductEditor->addServices([$serviceProduct]),
                $userBlogEditor->addServices([$serviceBlogPage]),

                $groupVisitor->addUsers([$userAnonymousVisitor]),
                $groupBuyer->addUsers([$userLoggedVisitor]),
                $groupEditor->addUsers([$userProductEditor, $userBlogEditor]),

                $acl->addGroup($groupVisitor),
                $acl->addGroup($groupBuyer, [$groupVisitor]),
                $acl->addGroup($groupEditor),
                $acl->addGroup($groupAdministrator, [$groupEditor]),

                $acl->allow($groupVisitor, [$permissionRead]),
                $acl->allow($groupBuyer, [$permissionBuy]),
                $acl->allow($groupEditor, [$permissionRead, $permissionWrite])
            )
            ->when($result = $acl->isAllowed($userAnonymousVisitor, $permissionRead, $serviceProduct))
            ->then
                ->boolean($result)
                    ->isTrue()

            ->when($result = $acl->isAllowed($userAnonymousVisitor, $permissionBuy, $serviceProduct))
            ->then
                ->boolean($result)
                    ->isFalse()

            ->when($result = $acl->isAllowed($userLoggedVisitor, $permissionRead, $serviceProduct))
            ->then
                ->boolean($result)
                    ->isTrue()

            ->when($result = $acl->isAllowed($userLoggedVisitor, $permissionBuy, $serviceProduct))
            ->then
                ->boolean($result)
                    ->isTrue()

            ->when($result = $acl->isAllowed($userLoggedVisitor, $permissionWrite))
            ->then
                ->boolean($result)
                    ->isFalse()

            ->when($result = $acl->isAllowed($userProductEditor, $permissionBuy))
            ->then
                ->boolean($result)
                    ->isFalse()

            ->when($result = $acl->isAllowed($userProductEditor, $permissionWrite))
            ->then
                ->boolean($result)
                    ->isTrue()

            ->when($result = $acl->isAllowed($userBlogEditor, $permissionWrite))
            ->then
                ->boolean($result)
                    ->isTrue()

            ->when($result = $acl->isAllowed($userProductEditor, $permissionWrite, $serviceBlogPage))
            ->then
                ->boolean($result)
                    ->isFalse()

            ->when($result = $acl->isAllowed($userBlogEditor, $permissionWrite, $serviceBlogPage))
            ->then
                ->boolean($result)
                    ->isTrue()

            ->when($result = $acl->isAllowed('user_editor_blog', 'permission_write', 'service_blog_page'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }
}
