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

namespace Hoa\Locale\Localizer;

use Hoa\Http as HHttp;

/**
 * Class \Hoa\Locale\Localizer\Http.
 *
 * Deduce locale from a HTTP request.
 *
 * Overview: http://www.w3.org/International/articles/language-tags/.
 * Specifications: RFC2822 and RFC3282.
 */
class Http implements Localizer
{
    /**
     * Value of the `Accept-Language` header.
     */
    protected $_value = null;



    /**
     * Constructor.
     */
    public function __construct(string $headerValue = null)
    {
        $value = $headerValue ?: HHttp\Runtime::getHeader('accept-language');

        // Remove CFWS.
        $this->_value = preg_replace('#\([^\)]+\)|\s#', '', $value);

        return;
    }

    /**
     * Get locale.
     * Please, see RFC3282 3. The Accept-Language header and
     * RFC2822 3.2.3. Folding white space and comments.
     */
    public function getLocale(): ?string
    {
        foreach (explode(',', $this->_value) as $language) {
            $match = preg_match(
                '#^(?<language>[^;]+)(;q=(?<q>0(?:\.\d{0,3})|1(?:\.0{0,3})))?$#',
                $language,
                $matches
            );

            if (0 !== $match) {
                break;
            }
        }

        if (empty($matches)) {
            return null;
        }

        return $matches['language'];
    }
}
