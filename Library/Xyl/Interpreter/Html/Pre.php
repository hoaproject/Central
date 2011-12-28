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
 * \Hoa\Xyl\Interpreter\Html\GenericPhrasing
 */
-> import('Xyl.Interpreter.Html.GenericPhrasing')

/**
 * \Hoa\Xyl\Element\Executable
 */
-> import('Xyl.Element.Executable')

/**
 * \Hoa\StringBuffer\ReadWrite
 */
-> import('StringBuffer.ReadWrite');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Pre.
 *
 * The <pre /> component.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Pre extends GenericPhrasing {

    /**
     * Attributes description.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input array
     */
    protected static $_attributes        = array(
        // Only decimal and decimal-leading-zero are supported.
        'line-type'      => parent::ATTRIBUTE_TYPE_NORMAL,
        'line-highlight' => parent::ATTRIBUTE_TYPE_NORMAL,
        'line-downlight' => parent::ATTRIBUTE_TYPE_NORMAL
    );

    /**
     * Attributes mapping between XYL and HTML.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input array
     */
    protected static $_attributesMapping = null;



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( \Hoa\Stream\IStream\Out $out ) {

        $line = $this->abstract->readCustomAttributes('line');

        if(empty($line)) {

            parent::paint($out);

            return;
        }

        /**
         * This solution could broke some markup.
         */

        if(isset($line['type']))
            $this->writeAttribute('data-line-type', $line['type']);

        $light = array();

        if(isset($line['highlight']))
            $this->parseInterval($line['highlight'], 'highlight', $light);

        if(isset($line['downlight']))
            $this->parseInterval($line['downlight'], 'downlight', $light);

        $oout    = new \Hoa\StringBuffer\ReadWrite();
        $this->computeValue($oout);
        $content = explode("\n", $oout->readAll());
        $id      = $this->readAttribute('id');

        if(null !== $id)
            $id .= '_';

        $id    .= 'line';
        $_zero  = '';

        if(true === $zero = 'decimal-leading-zero' === $line['type'])
            $_zero = (0 === $log = (int) log10(count($content) - 1))
                         ? ''
                         : str_repeat('0', $log);

        foreach($content as $_i => &$line) {

            $i = $_i + 1;

            if(      10 === $i
               ||   100 === $i
               ||  1000 === $i
               || (9999 < $i && ctype_digit((string) log10($i))))
                $_zero = substr($_zero, 1);

            $a    = $id . $i;
            $line = '<span class="line' .
                    (isset($light[$i])
                        ? ' line-' . $light[$i]
                        : '') .
                    '"><span class="line-number"><a href="#' . $a . '" name="' .
                    $a . '">' . $_zero . $i . '</a><span>' . $line .
                    '</span></span>';
        }

        $out->writeAll(
            '<pre' . $this->readAttributesAsString() . '>' .
            implode("\n", $content) .
            '</pre>'
        );

        return;
    }

    /**
     * Parse a list of intervals of the form: “x-y” or “z”.
     *
     * @access  protected
     * @param   string  $interval    Interval.
     * @param   mixed   $value       Associated value.
     * @param   array   &$out        Array to fill.
     * @return  void
     */
    public function parseInterval ( $interval, $value, &$out ) {

        foreach(explode(' ', $interval) as $i) {

            $a = null;
            $b = null;

            if(0 !== preg_match('#(\d+)-(\d+)#', $i, $matches))
                list($_, $a, $b) = $matches;
            else
                $a = $b = $i;

            foreach(range($a, $b) as $_i)
                $out[$_i] = $value;
        }

        return;
    }
}

}
