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

namespace Hoa\Xml\Bin;

use Hoa\Console;
use Hoa\Exception;
use Hoa\File;

/**
 * Class \Hoa\Xml\Bin\Shell.
 *
 * Interactive XML shell.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Shell extends Console\Dispatcher\Kit
{
    /**
     * Options description.
     *
     * @var array
     */
    protected $options = [
        ['color', Console\GetOption::OPTIONAL_ARGUMENT, 'c'],
        ['help',  Console\GetOption::NO_ARGUMENT,       'h'],
        ['help',  Console\GetOption::NO_ARGUMENT,       '?']
    ];

    /**
     * Current color level (among: 1, 8 and 256).
     *
     * @var int
     */
    protected $_color  = 1;

    /**
     * Palette.
     *
     * @var array
     */
    protected $_colors = [
        8 => [
            'oc'        => '34',
            'tagname'   => '32',
            'attrname'  => '34',
            'attrvalue' => '34',
            '='         => '34',
            'q'         => '34',
            'comment'   => '37',
            'pi'        => '35',
            'entity'    => '37',
            'text'      => '33'
        ],
        256 => [
            'oc'        => '38;5;240',
            'tagname'   => '38;5;64',
            'attrname'  => '38;5;245',
            'attrvalue' => '38;5;136',
            '='         => '38;5;240',
            'q'         => '38;5;240',
            'comment'   => '38;5;241',
            'pi'        => '38;5;125',
            'entity'    => '38;5;255',
            'text'      => '38;5;166'
        ]
    ];



    /**
     * The entry method.
     *
     * @return  int
     */
    public function main()
    {
        $filename = null;

        while (false !== $c = $this->getOption($v)) {
            switch ($c) {
                case 'c':
                    $v = true === $v ? 256 : intval($v);

                    switch ($v) {
                        case 1:
                        case 8:
                        case 256:
                            $this->_color = $v;

                            break;
                    }

                  break;

                case 'h':
                case '?':
                    return $this->usage();

                case '__ambiguous':
                    $this->resolveOptionAmbiguity($v);

                    break;
            }
        }

        $this->parser->listInputs($filename);

        if (null === $filename) {
            return $this->usage();
        }

        $line = 'load ' . $filename;

        do {
            try {
                @list($command, $argument) = preg_split('#\s+#', $line, 2);

                switch ($command) {
                    case 'load':
                        if ('%' === $argument) {
                            $root = new \Hoa\Xml\Read(
                                new \Hoa\File\Read(
                                    $root->getInnerStream()->getStreamName()
                                ),
                                false
                            );
                            echo 'Reloaded!';
                        } else {
                            $root = new \Hoa\Xml\Read(
                                new \Hoa\File\Read($argument),
                                false
                            );
                            echo 'Loaded!';
                        }

                        $current = $root;
                        echo "\n";

                    case 'h':
                    case 'help':
                        echo
                            'Usage:', "\n",
                            '    h[elp] to print this help;', "\n",
                            '    %      to print loaded filename;', "\n",
                            '    load   to load a file (`load %` to reload);', "\n",
                            '    ls     to print current tree;', "\n",
                            '    cd     to move in the tree with XPath;', "\n",
                            '    pwd    to print the current path;', "\n",
                            '    color  to change color (among 1, 8 and 256);', "\n",
                            '    q[uit] to quit.', "\n";

                        break;

                    case '%':
                        echo $root->getInnerStream()->getStreamName(), "\n";

                        break;

                    case 'ls':
                        echo $this->cout($current->readDOM()), "\n";

                        break;

                    case 'cd':
                        if (null === $argument) {
                            echo 'Need an argument.', "\n";

                            break;
                        }

                        $handle = $current->xpath($argument);

                        if (empty($handle)) {
                            echo $argument, ' is not found.', "\n";

                            break;
                        }

                        $current = $handle[0];

                        break;

                    case 'pwd':
                        echo $current->readDOM()->getNodePath(), "\n";

                        break;

                    case 'color':
                        $argument = intval($argument);

                        if (1   !== $argument &&
                            8   !== $argument &&
                            256 !== $argument) {
                            echo
                                $argument,
                                ' is not valid color (1, 8 or 256).', "\n";

                            break;
                        }

                        $this->_color = $argument;

                        break;

                    case 'q':
                    case 'quit':
                        break 2;

                    default:
                        if (!empty($command)) {
                            echo 'Command ', $command, ' not found.', "\n";
                        }
                }
            } catch (Exception\Exception $e) {
                echo $e->getMessage(), "\n";

                continue;
            }

            echo "\n";
        } while (false !== $line = $this->readLine('> '));

        return;
    }

    /**
     * The command usage.
     *
     * @return  int
     */
    public function usage()
    {
        echo
            'Usage   : xml:shell <options> [filename]', "\n",
            'Options :', "\n",
            $this->makeUsageOptionsList([
                'c'    => 'Allowed colors number among 1, 8 and 256 (default).',
                'help' => 'This help.'
            ]), "\n";

        return;
    }

    /**
     * Pretty print XML tree.
     *
     * @param   \DOMNode  $element    Element.
     * @return  string
     */
    public function cout(\DOMNode $element)
    {
        if (1 === $this->_color) {
            return $element->C14N();
        }

        $out   = null;
        $nodes = $element->childNodes;

        for ($i = 0, $max = $nodes->length; $i < $max; ++$i) {
            $node = $nodes->item($i);

            switch ($node->nodeType) {
                case XML_ELEMENT_NODE:
                    $out .=
                        $this->color('<', 'oc') .
                        $this->color($node->tagName, 'tagname');

                    foreach ($node->attributes as $attr) {
                        $out .=
                            ' ' .
                            $this->color($attr->name, 'attrname') .
                            $this->color('=', '=') .
                            $this->color('"', 'q') .
                            $this->color($attr->value, 'attrvalue') .
                            $this->color('"', 'q');
                    }

                    $out .=
                        $this->color('>', 'oc') .
                        $this->cout($node) .
                        $this->color('</', 'oc') .
                        $this->color($node->tagName, 'tagname') .
                        $this->color('>', 'oc');

                    break;

                case XML_TEXT_NODE:
                    $out .= $this->color($node->wholeText, 'text');

                    break;

                case XML_CDATA_SECTION_NODE:
                    break;

                case XML_ENTITY_REF_NODE:
                    $out .= $this->color('&' . $node->name . ';', 'entity');

                    break;

                case XML_ENTITY_NODE:
                    break;

                case XML_PI_NODE:
                    $out .= $this->color(
                        '<?' . $node->target . ' ' . $node->data . '?>',
                        'pi'
                    );

                    break;

                case XML_COMMENT_NODE:
                    $out .= $this->color(
                        '<!--' . $node->data . '-->',
                        'comment'
                    );

                    break;

                default:
                    var_dump($node->nodeType);
            }
        }

        return $out;
    }

    /**
     * Use colors.
     *
     * @param   string  $text     Text.
     * @param   string  $token    Token.
     * @return  string
     */
    public function color($text, $token)
    {
        return
            "\033[" . $this->_colors[$this->_color][$token] . 'm' .
            $text .
            "\033[0m";
    }
}

__halt_compiler();
Interactive XML shell.
