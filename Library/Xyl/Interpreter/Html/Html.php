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
 * \Hoa\Xyl\Interpreter
 */
-> import('Xyl.Interpreter.~')

/**
 * \Hoa\Xyl\Interpreter\Common\*
 */
-> import('Xyl.Interpreter.Common.*')

/**
 * \Hoa\Xyl\Interpreter\Html\*
 */
-> import('Xyl.Interpreter.Html.*');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html.
 *
 * HTML interpreter.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Html extends \Hoa\Xyl\Interpreter { 

    /**
     * Rank: abstract elements to concrete elements.
     *
     * @var \Hoa\Xyl\Interpreter\Html array
     */
    protected $_rank         = array(
        // XYL.
        'document'        => '\Hoa\Xyl\Interpreter\Html\Document',
        'definition'      => '\Hoa\Xyl\Interpreter\Common\Yield',
        'value'           => '\Hoa\Xyl\Interpreter\Common\Value',
        'yield'           => '\Hoa\Xyl\Interpreter\Common\Yield',

        // Grouping content.
        'p'               => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'blockquote'      => '\Hoa\Xyl\Interpreter\Html\Q',
        'hr'              => '\Hoa\Xyl\Interpreter\Html\Hr',
        'pre'             => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'ol'              => '\Hoa\Xyl\Interpreter\Html\Ol',
        'ul'              => '\Hoa\Xyl\Interpreter\Html\Generic',
        'li'              => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'dl'              => '\Hoa\Xyl\Interpreter\Html\Generic',
        'dt'              => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'dd'              => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'figure'          => '\Hoa\Xyl\Interpreter\Html\Generic',
        'figcaption'      => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'div'             => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',

        // Text-level semantics.
        'a'               => '\Hoa\Xyl\Interpreter\Html\A',
        'em'              => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'strong'          => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'small'           => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        's'               => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'cite'            => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'q'               => '\Hoa\Xyl\Interpreter\Html\Q',
        'dfn'             => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'abbr'            => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'time'            => '\Hoa\Xyl\Interpreter\Html\Time',
        'code'            => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'var'             => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'samp'            => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'kbd'             => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'sub'             => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'sup'             => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'i'               => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'b'               => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'u'               => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'mark'            => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'ruby'            => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'rt'              => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'rp'              => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'bdi'             => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'bdo'             => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'span'            => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'br'              => '\Hoa\Xyl\Interpreter\Html\Br',
        'wbr'             => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',

        // Edits.
        'ins'             => '\Hoa\Xyl\Interpreter\Html\Mod',
        'del'             => '\Hoa\Xyl\Interpreter\Html\Mod',

        // Phrasing model.
        '__text'          => '\Hoa\Xyl\Interpreter\Html\Text'
    );

    /**
     * Resource path.
     *
     * @var \Hoa\Xyl\Interpreter\Html string
     */
    protected $_resourcePath = 'Html/Resource/';
}

}
