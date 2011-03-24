<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 * HTML5 intepreter.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
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
        'value'           => '\Hoa\Xyl\Interpreter\Common\Value',
        'yield'           => '\Hoa\Xyl\Interpreter\Common\Yield',
        'debug'           => '\Hoa\Xyl\Interpreter\Common\Debug',
        'script'          => '\Hoa\Xyl\Interpreter\Html\Script',

        // Layout.
        'box'             => '\Hoa\Xyl\Interpreter\Html\Box',
        'hbox'            => '\Hoa\Xyl\Interpreter\Html\Hbox',
        'vbox'            => '\Hoa\Xyl\Interpreter\Html\Vbox',
        'grid'            => '\Hoa\Xyl\Interpreter\Html\Grid',
        'grows'           => '\Hoa\Xyl\Interpreter\Html\Grows',
        'grow'            => '\Hoa\Xyl\Interpreter\Html\Grow',

        // Section.
        'section1'        => '\Hoa\Xyl\Interpreter\Html\Section1',
        'section2'        => '\Hoa\Xyl\Interpreter\Html\Section2',
        'section3'        => '\Hoa\Xyl\Interpreter\Html\Section3',
        'section4'        => '\Hoa\Xyl\Interpreter\Html\Section4',
        'section5'        => '\Hoa\Xyl\Interpreter\Html\Section5',
        'section6'        => '\Hoa\Xyl\Interpreter\Html\Section6',
        'tableofcontents' => '\Hoa\Xyl\Interpreter\Html\Tableofcontents',
        'navigation'      => '\Hoa\Xyl\Interpreter\Html\Navigation',
        'header'          => '\Hoa\Xyl\Interpreter\Html\Header',
        'content'         => '\Hoa\Xyl\Interpreter\Html\Content',
        'footer'          => '\Hoa\Xyl\Interpreter\Html\Footer',
        'title'           => '\Hoa\Xyl\Interpreter\Html\Title',

        // Grouping content.
        'blockcode'       => '\Hoa\Xyl\Interpreter\Html\Blockcode',
        'p'               => '\Hoa\Xyl\Interpreter\Html\P',
        'span'            => '\Hoa\Xyl\Interpreter\Html\Span',
        'ulist'           => '\Hoa\Xyl\Interpreter\Html\Ulist',
        'olist'           => '\Hoa\Xyl\Interpreter\Html\Olist',
        'item'            => '\Hoa\Xyl\Interpreter\Html\Item',
        'error'           => '\Hoa\Xyl\Interpreter\Html\Error',

        // Text-level semantics.
        'big'             => '\Hoa\Xyl\Interpreter\Html\Big',
        'code'            => '\Hoa\Xyl\Interpreter\Html\Code',
        'em'              => '\Hoa\Xyl\Interpreter\Html\Em',
        'fbreak'          => '\Hoa\Xyl\Interpreter\Html\Fbreak',
        'hseparator'      => '\Hoa\Xyl\Interpreter\Html\Hseparator',
        'mark'            => '\Hoa\Xyl\Interpreter\Html\Mark',
        'small'           => '\Hoa\Xyl\Interpreter\Html\Small',
        'strong'          => '\Hoa\Xyl\Interpreter\Html\Strong',
        'sub'             => '\Hoa\Xyl\Interpreter\Html\Sub',
        'sup'             => '\Hoa\Xyl\Interpreter\Html\Sup',

        // Form.
        'button'          => '\Hoa\Xyl\Interpreter\Html\Button',
        'form'            => '\Hoa\Xyl\Interpreter\Html\Form',
        'input'           => '\Hoa\Xyl\Interpreter\Html\Input',
        'select'          => '\Hoa\Xyl\Interpreter\Html\Select',
        'optgroup'        => '\Hoa\Xyl\Interpreter\Html\Optgroup',
        'option'          => '\Hoa\Xyl\Interpreter\Html\Option',
        'textarea'        => '\Hoa\Xyl\Interpreter\Html\Textarea',
        'label'           => '\Hoa\Xyl\Interpreter\Html\Label',

        // Link.
        'link'            => '\Hoa\Xyl\Interpreter\Html\Link',

        // Media.
        'image'           => '\Hoa\Xyl\Interpreter\Html\Image',

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
