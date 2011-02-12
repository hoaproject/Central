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
 * \Hoa\Xyl\Interpreter\Common_*
 */
-> import('Xyl.Interpreter.Common.*')

/**
 * \Hoa\Xyl\Interpreter\Html5_*
 */
-> import('Xyl.Interpreter.Html5.*');

}

namespace Hoa\Xyl\Interpreter\Html5 {

/**
 * Class \Hoa\Xyl\Interpreter\Html5.
 *
 * HTML5 intepreter.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Html5 extends \Hoa\Xyl\Interpreter { 

    /**
     * Rank: abstract elements to concrete elements.
     *
     * @var \Hoa\Xyl\Interpreter\Html5 array
     */
    protected $_rank         = array(
        // XYL.
        'document'        => '\Hoa\Xyl\Interpreter\Html5\Document',
        'yield'           => '\Hoa\Xyl\Interpreter\Common\Yield',

        // Layout.
        'box'             => '\Hoa\Xyl\Interpreter\Html5\Box',
        'hbox'            => '\Hoa\Xyl\Interpreter\Html5\Hbox',
        'vbox'            => '\Hoa\Xyl\Interpreter\Html5\Vbox',
        'grid'            => '\Hoa\Xyl\Interpreter\Html5\Grid',
        'grows'           => '\Hoa\Xyl\Interpreter\Html5\Grows',
        'grow'            => '\Hoa\Xyl\Interpreter\Html5\Grow',

        // Section.
        'section1'        => '\Hoa\Xyl\Interpreter\Html5\Section1',
        'section2'        => '\Hoa\Xyl\Interpreter\Html5\Section2',
        'section3'        => '\Hoa\Xyl\Interpreter\Html5\Section3',
        'section4'        => '\Hoa\Xyl\Interpreter\Html5\Section4',
        'section5'        => '\Hoa\Xyl\Interpreter\Html5\Section5',
        'section6'        => '\Hoa\Xyl\Interpreter\Html5\Section6',
        'tableofcontents' => '\Hoa\Xyl\Interpreter\Html5\Tableofcontents',
        'navigation'      => '\Hoa\Xyl\Interpreter\Html5\Navigation',
        'header'          => '\Hoa\Xyl\Interpreter\Html5\Header',
        'content'         => '\Hoa\Xyl\Interpreter\Html5\Content',
        'footer'          => '\Hoa\Xyl\Interpreter\Html5\Footer',
        'title'           => '\Hoa\Xyl\Interpreter\Html5\Title',

        // Grouping content.
        'p'               => '\Hoa\Xyl\Interpreter\Html5\P',
        'ulist'           => '\Hoa\Xyl\Interpreter\Html5\Ulist',
        'olist'           => '\Hoa\Xyl\Interpreter\Html5\Olist',
        'item'            => '\Hoa\Xyl\Interpreter\Html5\Item',
        'error'           => '\Hoa\Xyl\Interpreter\Html5\Error',

        // Text-level semantics.
        'big'             => '\Hoa\Xyl\Interpreter\Html5\Big',
        'em'              => '\Hoa\Xyl\Interpreter\Html5\Em',
        'fbreak'          => '\Hoa\Xyl\Interpreter\Html5\Fbreak',
        'mark'            => '\Hoa\Xyl\Interpreter\Html5\Mark',
        'small'           => '\Hoa\Xyl\Interpreter\Html5\Small',
        'strong'          => '\Hoa\Xyl\Interpreter\Html5\Strong',
        'sub'             => '\Hoa\Xyl\Interpreter\Html5\Sub',
        'sup'             => '\Hoa\Xyl\Interpreter\Html5\Sup',

        // Form.
        'form'            => '\Hoa\Xyl\Interpreter\Html5\Form',
        'input'           => '\Hoa\Xyl\Interpreter\Html5\Input',
        'select'          => '\Hoa\Xyl\Interpreter\Html5\Select',
        'optgroup'        => '\Hoa\Xyl\Interpreter\Html5\Optgroup',
        'option'          => '\Hoa\Xyl\Interpreter\Html5\Option',
        'textarea'        => '\Hoa\Xyl\Interpreter\Html5\Textarea',
        'label'           => '\Hoa\Xyl\Interpreter\Html5\Label',

        // Phrasing model.
        '__text'          => '\Hoa\Xyl\Interpreter\Html5\Text'
    );

    /**
     * Resource path.
     *
     * @var \Hoa\Xyl\Interpreter\Html5 string
     */
    protected $_resourcePath = 'Html5/Resource/';
}

}
