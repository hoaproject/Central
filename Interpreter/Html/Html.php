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

namespace Hoa\Xyl\Interpreter\Html;

use Hoa\Consistency;
use Hoa\Xyl;

/**
 * Class \Hoa\Xyl\Interpreter\Html.
 *
 * HTML interpreter.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Html extends Xyl\Interpreter
{
    /**
     * Rank: abstract elements to concrete elements.
     *
     * @var array
     */
    protected $_rank = [
        // XYL.
        'document'        => '\Hoa\Xyl\Interpreter\Html\Document',
        'title'           => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'definition'      => '\Hoa\Xyl\Interpreter\Common\XylYield',
        'value'           => '\Hoa\Xyl\Interpreter\Common\Value',
        'yield'           => '\Hoa\Xyl\Interpreter\Common\XylYield',
        'fragment'        => '\Hoa\Xyl\Interpreter\Common\XylYield',
        'snippet'         => '\Hoa\Xyl\Interpreter\Common\Snippet',
        'if'              => '\Hoa\Xyl\Interpreter\Common\XylIf',
        'elseif'          => '\Hoa\Xyl\Interpreter\Common\XylElseif',
        'else'            => '\Hoa\Xyl\Interpreter\Common\XylElse',
        '_'               => '\Hoa\Xyl\Interpreter\Common\Translate',

        // Script.
        'script'          => '\Hoa\Xyl\Interpreter\Html\Script',
        'noscript'        => '\Hoa\Xyl\Interpreter\Html\Generic',

        // Sections.
        'section'         => '\Hoa\Xyl\Interpreter\Html\Generic',
        'nav'             => '\Hoa\Xyl\Interpreter\Html\Generic',
        'article'         => '\Hoa\Xyl\Interpreter\Html\Generic',
        'aside'           => '\Hoa\Xyl\Interpreter\Html\Generic',
        'h1'              => '\Hoa\Xyl\Interpreter\Html\Heading',
        'h2'              => '\Hoa\Xyl\Interpreter\Html\Heading',
        'h3'              => '\Hoa\Xyl\Interpreter\Html\Heading',
        'h4'              => '\Hoa\Xyl\Interpreter\Html\Heading',
        'h5'              => '\Hoa\Xyl\Interpreter\Html\Heading',
        'h6'              => '\Hoa\Xyl\Interpreter\Html\Heading',
        'hgroup'          => '\Hoa\Xyl\Interpreter\Html\Generic',
        'header'          => '\Hoa\Xyl\Interpreter\Html\Generic',
        'footer'          => '\Hoa\Xyl\Interpreter\Html\Generic',
        'address'         => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',

        'tableofcontents' => '\Hoa\Xyl\Interpreter\Html\Tableofcontents',

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
        'main'            => '\Hoa\Xyl\Interpreter\Html\Generic',
        'div'             => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',

        'error'           => '\Hoa\Xyl\Interpreter\Html\Error',

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
        'data'            => '\Hoa\Xyl\Interpreter\Html\Data',
        'time'            => '\Hoa\Xyl\Interpreter\Html\Time',
        'code'            => '\Hoa\Xyl\Interpreter\Html\Code',
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

        // Embedded content.
        'img'             => '\Hoa\Xyl\Interpreter\Html\Img',
        'iframe'          => '\Hoa\Xyl\Interpreter\Html\Iframe',
        'video'           => '\Hoa\Xyl\Interpreter\Html\Video',
        'audio'           => '\Hoa\Xyl\Interpreter\Html\Media',
        'source'          => '\Hoa\Xyl\Interpreter\Html\Source',
        'track'           => '\Hoa\Xyl\Interpreter\Html\Track',
        'object'          => '\Hoa\Xyl\Interpreter\Html\HtmlObject',
        'param'           => '\Hoa\Xyl\Interpreter\Html\Param',
        'canvas'          => '\Hoa\Xyl\Interpreter\Html\Canvas',

        // Tabular data.
        'table'           => '\Hoa\Xyl\Interpreter\Html\Table',
        'caption'         => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'colgroup'        => '\Hoa\Xyl\Interpreter\Html\TableCol',
        'col'             => '\Hoa\Xyl\Interpreter\Html\TableCol',
        'tbody'           => '\Hoa\Xyl\Interpreter\Html\Generic',
        'thead'           => '\Hoa\Xyl\Interpreter\Html\Generic',
        'tfoot'           => '\Hoa\Xyl\Interpreter\Html\Generic',
        'tr'              => '\Hoa\Xyl\Interpreter\Html\Generic',
        'td'              => '\Hoa\Xyl\Interpreter\Html\Td',
        'th'              => '\Hoa\Xyl\Interpreter\Html\Th',

        // Forms.
        'form'            => '\Hoa\Xyl\Interpreter\Html\Form',
        'async'           => '\Hoa\Xyl\Interpreter\Html\Async',
        'fieldset'        => '\Hoa\Xyl\Interpreter\Html\Fieldset',
        'legend'          => '\Hoa\Xyl\Interpreter\Html\GenericPhrasing',
        'label'           => '\Hoa\Xyl\Interpreter\Html\Label',
        'input'           => '\Hoa\Xyl\Interpreter\Html\Input',
        'button'          => '\Hoa\Xyl\Interpreter\Html\Button',
        'select'          => '\Hoa\Xyl\Interpreter\Html\Select',
        'optgroup'        => '\Hoa\Xyl\Interpreter\Html\Optgroup',
        'option'          => '\Hoa\Xyl\Interpreter\Html\Option',
        'textarea'        => '\Hoa\Xyl\Interpreter\Html\Textarea',
        'keygen'          => '\Hoa\Xyl\Interpreter\Html\Keygen',
        'output'          => '\Hoa\Xyl\Interpreter\Html\Output',
        'progress'        => '\Hoa\Xyl\Interpreter\Html\Progress',
        'meter'           => '\Hoa\Xyl\Interpreter\Html\Meter',

        // Widgets.
        'tabs'            => '\Hoa\Xyl\Interpreter\Html\Tabs',
        'tablist'         => '\Hoa\Xyl\Interpreter\Html\Tablist',
        'tab'             => '\Hoa\Xyl\Interpreter\Html\Tab',
        'tabpanel'        => '\Hoa\Xyl\Interpreter\Html\Tabpanel',

        // Extra.
        'checkpoint'      => '\Hoa\Xyl\Interpreter\Html\Checkpoint',

        // Phrasing model.
        '__text'          => '\Hoa\Xyl\Interpreter\Html\Text'
    ];

    /**
     * Resource path.
     *
     * @var string
     */
    protected $_resourcePath = 'Html/Resource/';
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Xyl\Interpreter\Html\Html');
