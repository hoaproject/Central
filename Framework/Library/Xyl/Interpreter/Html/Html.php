<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
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
        'yield'           => '\Hoa\Xyl\Interpreter\Common\Yield',

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
        'mark'            => '\Hoa\Xyl\Interpreter\Html\Mark',
        'small'           => '\Hoa\Xyl\Interpreter\Html\Small',
        'strong'          => '\Hoa\Xyl\Interpreter\Html\Strong',
        'sub'             => '\Hoa\Xyl\Interpreter\Html\Sub',
        'sup'             => '\Hoa\Xyl\Interpreter\Html\Sup',

        // Form.
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
