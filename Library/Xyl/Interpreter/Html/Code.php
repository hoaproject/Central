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
-> import('Xyl.Element.Executable');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Code.
 *
 * The <code /> component.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Code extends GenericPhrasing {

    /**
     * Attributes description.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input array
     */
    protected static $_attributes        = array(
        'language' => parent::ATTRIBUTE_TYPE_NORMAL
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

        if(false === $this->abstract->attributeExists('language')) {

            $out->writeAll('<code' . $this->readAttributesAsString() . '>');
            $this->computeValue($out);
            $out->writeAll('</code>');

            return;
        }

        $language = strtolower($this->abstract->readAttribute('language'));
        $this->writeAttribute('class', 'code-' . $language);
        $out->writeAll('<code' . $this->readAttributesAsString() . '>');

        switch($language) {

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
                $out->writeAll($this->computeValue());
              break;
        }

        $out->writeAll('</code>');

        return;
    }

    protected function colorizePhp ( \Hoa\Stream\IStream\Out $out ) {

        $language = '<?php ' . htmlspecialchars_decode($this->computeValue());

        foreach(token_get_all($language) as $tuplet) {

            if(!is_array($tuplet))
                $tuplet = array(-1, $tuplet, -1);

            list($token, $value) = $tuplet;
            $value               = htmlspecialchars($value);
            $newline             = false;

            if('' == trim($value)) {

                $out->writeAll($value);
                continue;
            }

            if("\n" == substr($value, -1)) {

                $newline = true;
                $value   = rtrim($value);
            }

            switch($token) {

                case T_ARRAY:
                case T_STRING:
                    $out->writeAll(
                        '<span class="token-id">' .
                        str_replace(
                            "\n",
                            '</span>' . "\n" . '<span class="token-id">',
                            $value
                        ) .
                        '</span>'
                    );
                  break;

                case T_CONSTANT_ENCAPSED_STRING:
                    $out->writeAll(
                        '<span class="token-string">' .
                        str_replace(
                            "\n",
                            '</span>' . "\n" . '<span class="token-string">',
                            $value
                        ) .
                        '</span>'
                    );
                  break;

                case T_VARIABLE:
                    $out->writeAll(
                        '<span class="token-variable">' .
                        str_replace(
                            "\n",
                            '</span>' . "\n" . '<span class="token-variable">',
                            $value
                        ) .
                        '</span>'
                    );
                  break;

                case T_OPEN_TAG:
                  continue;

                default:
                    $out->writeAll(
                        '<span class="token-keyword">' .
                        str_replace(
                            "\n",
                            '</span>' . "\n" . '<span class="token-keyword">',
                            $value
                        ) .
                        '</span>'
                    );
                  break;
            }

            if(true === $newline)
                $out->writeAll("\n");
        }

        return;
    }

    protected function colorizeShell ( \Hoa\Stream\IStream\Out $out ) {

        foreach(explode("\n", $this->computeValue()) as $line) {

            $line = trim($line);

            if(empty($line))
                $out->writeAll("\n");
            elseif('$' == $line[0])
                $out->writeAll(
                    '$ <span class="token-id">' . ltrim($line, '$ ') .
                    '</span>' . "\n"
                );
            else
                $out->writeAll(
                    '<span class="token-string">' . $line . '</span>' . "\n"
                );
        }
    }

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
            htmlspecialchars_decode($this->computeValue())
        );

        $out->writeAll($xml);

        return;
}
}

}
