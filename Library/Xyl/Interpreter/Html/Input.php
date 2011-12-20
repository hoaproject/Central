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

namespace {

from('Hoa')

/**
 * \Hoa\Xyl\Interpreter\Html\Generic
 */
-> import('Xyl.Interpreter.Html.Generic');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Input.
 *
 * The <input /> component.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Input extends Generic {

    /**
     * Attributes description.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input array
     */
    protected static $_attributes        = array(
        'accept'         => parent::ATTRIBUTE_TYPE_NORMAL,
        'alt'            => parent::ATTRIBUTE_TYPE_NORMAL,
        'autocomplete'   => parent::ATTRIBUTE_TYPE_NORMAL,
        'checked'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'dirname'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'formaction'     => parent::ATTRIBUTE_TYPE_LINK,
        'formenctype'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'formmethod'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'formnovalidate' => parent::ATTRIBUTE_TYPE_NORMAL,
        'formtarget'     => parent::ATTRIBUTE_TYPE_NORMAL,
        'height'         => parent::ATTRIBUTE_TYPE_NORMAL,
        'list'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'max'            => parent::ATTRIBUTE_TYPE_NORMAL,
        'maxlength'      => parent::ATTRIBUTE_TYPE_NORMAL,
        'min'            => parent::ATTRIBUTE_TYPE_NORMAL,
        'multiple'       => parent::ATTRIBUTE_TYPE_NORMAL,
        'pattern'        => parent::ATTRIBUTE_TYPE_NORMAL,
        'placeholder'    => parent::ATTRIBUTE_TYPE_NORMAL,
        'readonly'       => parent::ATTRIBUTE_TYPE_NORMAL,
        'required'       => parent::ATTRIBUTE_TYPE_NORMAL,
        'size'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'src'            => parent::ATTRIBUTE_TYPE_LINK,
        'step'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'type'           => parent::ATTRIBUTE_TYPE_NORMAL,
        'value'          => parent::ATTRIBUTE_TYPE_NORMAL,
        'width'          => parent::ATTRIBUTE_TYPE_NORMAL
    );

    /**
     * Attributes mapping between XYL and HTML.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input array
     */
    protected static $_attributesMapping = …;

    /**
     * Whether content could exist or not.
     * 0 to false, 1 to true, 2 to maybe.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input int
     */
    protected $_contentFlow              = 0;
}

}
