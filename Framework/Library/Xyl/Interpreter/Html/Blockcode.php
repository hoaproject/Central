<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
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
 * \Hoa\Xyl\Element\Concrete
 */
-> import('Xyl.Element.Concrete');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Blockcode.
 *
 * The <blockcode /> component.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Blockcode extends \Hoa\Xyl\Element\Concrete {

    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( \Hoa\Stream\IStream\Out $out ) {

        $out->writeAll('<pre' . $this->readAttributesAsString() . '><code>');

        if(false === $this->attributeExists('language'))
            $this->computeValue($out);
        else {

            switch(strtolower($this->readAttribute('language'))) {

                case 'php':
                    $this->colorizePhp($out);
                  break;

                case 'shell':
                    $this->colorizeShell($out);
                  break;

                case 'xml':
                    $this->colorizeXml($out);
                  break;

                default:
                    $out->writeAll(htmlspecialchars($this->computeValue()));
            }
        }

        $out->writeAll('</code></pre>');

        return;
    }

    /**
     * Colorize PHP code.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function colorizePhp ( \Hoa\Stream\IStream\Out $out ) {

        $lang = '<?php ' . $this->computeValue();

        foreach(token_get_all($lang) as $tuplet) {

            if(!is_array($tuplet))
                $tuplet = array(0 => -1, 1 => $tuplet, 2 => -1);

            list($token, $value) = $tuplet;
            $value               = htmlspecialchars($value);

            switch($token) {

                case T_ARRAY:
                case T_STRING:
                    $out->writeAll(
                        '<span class="token-id">' .
                        $value .
                        '</span>'
                    );
                  break;

                case T_CONSTANT_ENCAPSED_STRING:
                    $out->writeAll(
                        '<span class="token-string">' .
                        $value .
                        '</span>'
                    );
                  break;

                case T_VARIABLE:
                    $out->writeAll(
                        '<span class="token-variable">' .
                        $value .
                        '</span>'
                    );
                  break;

                case T_OPEN_TAG:
                  break;

                default:
                    $out->writeAll(
                        '<span class="token-keyword">' .
                        $value .
                        '</span>'
                    );
            }
        }

        return;
    }

    /**
     * Colorize Shell command lines.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function colorizeShell ( \Hoa\Stream\IStream\Out $out ) {

        foreach(explode("\n", $this->computeValue()) as $line) {

            $line = trim($line);

            if(empty($line))
                $out->writeAll("\n");

            elseif('$' == $line[0])
                $out->writeAll(
                    '$ <span class="token-id">' . ltrim($line, '$ ') . '</span>' .
                    "\n"
                );
            else
                $out->writeAll(
                    '<span class="token-string">' . $line . '</span>' . "\n"
                );
        }

        return;
    }

    /**
     * Colorize XML code.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function colorizeXml ( \Hoa\Stream\IStream\Out $out ) {

        $xml = preg_replace_callback(
            '#</([^>]+)>|<([^>]+)>#',
            function ( $matches ) {

                if(!isset($matches[2]))
                    return '<span class="token-keyword">&lt;/' .
                           $matches[1] . '&gt;</span>';

                preg_match('#^([^\s]+)\s*(.*)$#', $matches[2], $submatches);

                $end = '/' == substr($matches[2], -1, 1) ? ' /' : '';

                if(empty($end))
                    $end = '?' == substr($matches[2], -1, 1) ? '?' : '';

                if(empty($submatches[2]))
                    return '<span class="token-keyword">&lt;' .
                           $submatches[1] . $end . '&gt;</span>';

                $out = '<span class="token-keyword">&lt;' .
                       $submatches[1] . '</span>';

                preg_match_all(
                    '#(\w+)\s*(=\s*(?<!\\\)(?:("|\')|)(?(3)(.*?)(?<!\\\)\3|(\w+))\s*)?#',
                    $submatches[2],
                    $attributes,
                    PREG_SET_ORDER
                );


                if(!empty($attributes)) {

                    foreach($attributes as $i => $attribute)
                        // Boolean: abc
                        if(!isset($attribute[2]))
                            $out .= ' <span class="token-id">' . $attribute[1] .
                                    '</span>';

                        // Quote: abc="def" or abc='def'
                        elseif(!isset($attribute[5]))
                            $out .= ' <span class="token-id">' . $attribute[1] .
                                    '</span>=<span class="token-string">' .
                                    $attribute[3] . $attribute[4] .
                                    $attribute[3] . '</span>';

                        // No-quote: abc=def
                        else
                            $out .= ' <span class="token-id">' . $attribute[1] .
                                    '</span>=<span class="token-string">' .
                                    $attribute[5] . '</span>';
                }

                return $out . '<span class="token-keyword">' . $end .
                       '&gt;</span>';
            },
            $this->computeValue()
        );

        $out->writeAll($xml);

        return;
    }
}

}
