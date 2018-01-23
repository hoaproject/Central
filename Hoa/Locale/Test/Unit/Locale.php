<?php

declare(strict_types=1);

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

namespace Hoa\Locale\Test\Unit;

use Hoa\Locale as LUT;
use Hoa\Test;

/**
 * Class \Hoa\Locale\Test\Unit\Locale.
 *
 * Test suite of the locale main object.
 *
 * @license    New BSD License
 */
class Locale extends Test\Unit\Suite
{
    public function case_no_default_no_locale(): void
    {
        $this
            ->exception(function (): void {
                new LUT();
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_no_locale(): void
    {
        $this
            ->given(
                LUT::setDefault('fr-FR'),
                $locale = new LUT()
            )
            ->when(
                $localizer = $locale->getLocalizer(),
                $language  = $locale->getLanguage(),
                $region    = $locale->getRegion()
            )
            ->then
                ->object($localizer)
                    ->isInstanceOf(LUT\Localizer\Coerce::class)
                ->variable($localizer->getLocale())
                    ->isNull()
                ->string($language)
                    ->isEqualTo('fr')
                ->string($region)
                    ->isEqualTo('FR');
    }

    public function case_locale_autoBoxing(): void
    {
        $this
            ->given($locale = new LUT('fr-FR'))
            ->when($localizer = $locale->getLocalizer())
            ->then
                ->object($localizer)
                    ->isInstanceOf(LUT\Localizer\Coerce::class);
    }

    public function case_invalid_locale(): void
    {
        $this
            ->exception(function (): void {
                new LUT('fr_FR');
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_type_privateUse(): void
    {
        $this
            ->given(
                $regex = $this->realdom->regex(
                    '/x\-[a-z0-9]{1,8}/'
                )
            )
            ->when(function () use ($regex): void {
                foreach ($this->sampleMany($regex, 1000) as $datum) {
                    $this
                        ->given($locale = new LUT($datum))
                        ->when(
                            $type       = $locale->getType(),
                            $privateUse = $locale->getPrivateUse()
                        )
                        ->then
                            ->integer($type)
                                ->isEqualTo(LUT::TYPE_PRIVATEUSE)
                            ->string('x-' . $privateUse)
                                ->isEqualTo($datum);
                }
            });
    }

    public function case_type_grandfathered(): void
    {
        $this
            ->given(
                $regex = $this->realdom->regex(
                    // Modified regular expression,
                    // to avoid conflict with TYPE_LANGTAG
                    '/[a-wyz](\-[a-z0-9]{2,8}){1,2}/'
                )
            )
            ->when(function () use ($regex): void {
                foreach ($this->sampleMany($regex, 1000) as $datum) {
                    $this
                        ->given($locale = new LUT($datum))
                        ->when(
                            $type          = $locale->getType(),
                            $grandFathered = $locale->getGrandfathered()
                        )
                        ->then
                            ->integer($type)
                                ->isEqualTo(LUT::TYPE_GRANDFATHERED)
                            ->string($grandFathered)
                                ->isEqualTo($datum);
                }
            });
    }

    public function case_type_langtag(): void
    {
        $this
            ->given(
                $regex = $this->realdom->regex(
                    '/' .
                    '[a-z]{2,3}' .
                    '(\-[a-z]{4})?' .
                    '(\-(?:[a-z]{2}|[0-9]{4}))?' .
                    '((?:\-(?:[a-z]{2}|[0-9]{3}))+)?' .
                    '((?:\-(?:[a-wy-z]|\d)\-[a-z0-9]{2,8})+)?' .
                    '(\-x\-[a-z0-9]{1,8})?' .
                    '/'
                )
            )
            ->when(function () use ($regex): void {
                foreach ($this->sampleMany($regex, 1000) as $datum) {
                    $this
                        ->given($locale = new LUT($datum))
                        ->when($type = $locale->getType())
                        ->then
                            ->integer($type)
                                ->isEqualTo(LUT::TYPE_LANGTAG);
                }
            });
    }

    public function case_langtag_exploded(): void
    {
        $this
            ->given($locale = 'zh-Hant-TW-xy-ab-123-f-oo-4-42-x-qux')
            ->when($result = new LUT($locale))
            ->then
                ->string($result->getLanguage())
                    ->isEqualTo('zh')
                ->string($result->getScript())
                    ->isEqualTo('Hant')
                ->string($result->getRegion())
                    ->isEqualTo('TW')
                ->array($result->getVariants())
                    ->isEqualTo(['xy', 'ab', '123'])
                ->array($result->getExtensions())
                    ->isEqualTo(['f' => 'oo', 4 => '42'])
                ->string($result->getPrivateUse())
                    ->isEqualTo('qux');
    }

    public function case_langtag_default(): void
    {
        $this
            ->given($locale = 'fr')
            ->when($result = new LUT($locale))
            ->then
                ->string($result->getLanguage())
                    ->isEqualTo('fr')
                ->variable($result->getScript())
                    ->isNull()
                ->variable($result->getRegion())
                    ->isNull()
                ->array($result->getVariants())
                    ->isEmpty()
                ->array($result->getExtensions())
                    ->isEmpty()
                ->variable($result->getPrivateUse())
                    ->isNull();
    }

    public function case_reset(): void
    {
        $this
            ->given(
                $localizer = new LUT\Localizer\Coerce(
                    'zh-Hant-TW-xy-ab-123-f-oo-4-42-x-qux'
                )
            )
            ->when($locale = new LUT($localizer))
            ->then
                ->string($locale->getLanguage())
                    ->isEqualTo('zh')
                ->string($locale->getScript())
                    ->isEqualTo('Hant')
                ->string($locale->getRegion())
                    ->isEqualTo('TW')
                ->array($locale->getVariants())
                    ->isEqualTo(['xy', 'ab', '123'])
                ->array($locale->getExtensions())
                    ->isEqualTo(['f' => 'oo', 4 => '42'])
                ->string($locale->getPrivateUse())
                    ->isEqualTo('qux')

            ->given($localizer = new LUT\Localizer\Coerce('fr'))
            ->when($locale->setLocalizer($localizer))
            ->then
                ->string($locale->getLanguage())
                    ->isEqualTo('fr')
                ->variable($locale->getScript())
                    ->isNull()
                ->variable($locale->getRegion())
                    ->isNull()
                ->array($locale->getVariants())
                    ->isEmpty()
                ->array($locale->getExtensions())
                    ->isEmpty()
                ->variable($locale->getPrivateUse())
                    ->isNull();
    }
}
