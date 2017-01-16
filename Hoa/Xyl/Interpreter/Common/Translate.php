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

namespace Hoa\Xyl\Interpreter\Common;

use Hoa\Stream;
use Hoa\Xyl;

/**
 * Class \Hoa\Xyl\Interpreter\Common\Translate.
 *
 * The <_ /> component.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Translate extends Xyl\Element\Concrete
{
    /**
     * Attributes description.
     *
     * @var array
     */
    protected static $_attributes = [
        'n'    => self::ATTRIBUTE_TYPE_NORMAL,
        'with' => self::ATTRIBUTE_TYPE_CUSTOM
    ];



    /**
     * Paint the element.
     *
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    public function paint(Stream\IStream\Out $out)
    {
        $root  = $this->getAbstractElementSuperRoot();
        $value = $this->computeValue();
        $with  = '__main__';

        if (true === $this->abstract->attributeExists('with')) {
            $with = $this->abstract->readAttribute('with');
        }


        $translation = $root->getTranslation($with);

        if (null === $translation) {
            $out->writeAll($value);

            return;
        }

        $callable  = null;
        $arguments = [$value];

        if (true === $this->abstract->attributeExists('n')) {
            $callable    = xcallable($translation, '_n');
            $arguments[] = $this->abstract->readAttribute('n');
        } else {
            $callable = xcallable($translation, '_');
        }

        $with = $this->abstract->readCustomAttributes('with');

        if (!empty($with)) {
            foreach ($with as $w) {
                $arguments[] = $this->computeAttributeValue($w);
            }
        }

        $result = $callable->distributeArguments($arguments);

        if (false !== strpos($result, '<')) {
            $this->computeFromString($result);
        } else {
            $out->writeAll($result);
        }

        return;
    }
}
