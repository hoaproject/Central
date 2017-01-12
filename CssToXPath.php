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

namespace Hoa\Xml;

use Hoa\Compiler;

/**
 * Class \Hoa\Xml\CssToXPath.
 *
 * Compiler CSS3 to XPath2.0.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class CssToXPath extends Compiler\Ll1
{
    /**
     * XPath root.
     *
     * @var string
     */
    protected $_root    = null;

    /**
     * XPath current path part.
     *
     * @var string
     */
    protected $_current = null;

    /**
     * Set a default namespace prefix.
     *
     * @var string
     */
    protected $_prefix  = null;



    /**
     * Set up the compiler.
     *
     */
    public function __construct()
    {

        // http://w3.org/TR/css3-selectors/#w3cselgrammar
        parent::__construct(
            // Skip.
            [
                '#\/\*[^*]*\*+([^/*][^*]*\*+)*\/' // /* … */
            ],

            // Tokens.
            [
                // 1. Selectors group.
                [
                    '#,\s*' // ,
                ],

                // 2. Selector.
                [
                    '#\s*\+\s*', // +
                    '#\s*\>\s*', // >
                    '#\s*~\s*',  // ~
                    '#\s* \s*'   // s
                ],

                // 3. Simple selector sequence.
                [
                    '#(\*|\w+)\|', // tu (type selector or universal)
                    '#\w+',        // w
                    '|',           // |
                    '#\*'          // *
                ],

                // 4. Hacpn.
                [
                    '[',                // [
                    ']',                // ]
                    '#(\*|\w+)\|(?!=)', // tu (type selector or universal)
                    '#[\w\-]+\(((\+|\-|\d+\w+|\d+|\'\w+\'|\w+)\s*)+\)', // f
                    '#[\w\-]+',         // w
                    '#\^=',             // ^
                    '#\$=',             // $
                    '#~=',              // ~
                    '#\*=',             // *
                    '#\|=',             // |=
                    '=',                // =
                    '#\|',              // |
                    '#(\'|").*?(?<!\\\)\2', // 's
                    '##\w+',            // # (hash)
                    '#\.\w+',           // . (class)
                    ':'                 // :
                ]
            ],

            // States.
            [
                // 1. Selectors group.
                [
                     __,  // error
                    'GO'  // start
                ],

                // 2. Selector.
                [
                     __,  // error
                    'GO'  // start
                ],

                // 3. Simple selector sequence.
                [
                     __,  // error
                    'GO', // start
                    'TU', // start type selector or universal
                    'OK'  // terminal
                ],

                // 4. Hacpn.
                [
                     __,  // error
                    'GO', // start
                    'OB', // open bracket: [
                    'NS', // namespace prefix
                    'ID', // identifier
                    'OP', // operator
                    'VA', // value
                    'PC', // pseudo-class (:)
                    'PE', // pseudo-element (::)
                    'OK'  // terminal
                ]
            ],

            // Terminal.
            [
                // 1. Selectors group.
                ['GO'],

                // 2. Selector.
                ['GO'],

                // 3. Simple selector sequence.
                ['GO', 'OK'],

                // 4. Hacpn.
                ['GO', 'OK']
            ],

            // Transitions.
            [
                // 1. Selectors group.
                [
                    /*         ,
                    /* __ */ [ __ ],
                    /* GO */ ['GO']
                ],

                // 2. Selector.
                [
                    /*         +     >     ~     s
                    /* __ */ [ __,   __,   __,   __ ],
                    /* GO */ ['GO', 'GO', 'GO', 'GO']
                ],

                // 3. Simple selector sequence.
                [
                    /*         tu     w     |     *
                    /* __ */ [ __,   __,   __,   __ ],
                    /* GO */ ['TU', 'OK', 'TU', 'OK'],
                    /* TU */ [ __,  'OK',  __,  'OK'],
                    /* OK */ [ __,   __,   __,   __ ]
                ],

                // 4. Hacpn.
                [
                    /*         [     ]    tu    f     w     ^     $     ~     *     |=    =    |     's    #     .     :
                    /* __ */ [ __,  __,   __,   __,   __,   __,   __,   __,   __,   __,   __,  __,   __,   __,   __,   __ ],
                    /* GO */ ['OB', __,   __,   __,   __,   __,   __,   __,   __,   __,   __,  __,   __,  'OK', 'OK', 'PC'],
                    /* OB */ [ __,  __,  'NS',  __,  'ID',  __,   __,   __,   __,   __,   __, 'NS',  __,   __,   __,   __ ],
                    /* NS */ [ __,  __,   __,   __,  'ID',  __,   __,   __,   __,   __,   __,  __,   __,   __,   __,   __ ],
                    /* ID */ [ __, 'OK',  __,   __,   __,  'OP', 'OP', 'OP', 'OP', 'OP', 'OP', __,   __,   __,   __,   __ ],
                    /* OP */ [ __,  __,   __,   __,  'VA',  __,   __,   __,   __,   __,   __,  __,  'VA',  __,   __,   __ ],
                    /* VA */ [ __, 'OK',  __,   __,   __,   __,   __,   __,   __,   __,   __,  __,   __,   __,   __,   __ ],
                    /* PC */ [ __,  __,   __,  'OK', 'OK',  __,   __,   __,   __,   __,   __,  __,   __,   __,   __,  'PE'],
                    /* PE */ [ __,  __,   __,  'OK', 'OK',  __,   __,   __,   __,   __,   __,  __,   __,   __,   __,   __ ],
                    /* OK */ [ __,  __,   __,   __,   __,   __,   __,   __,   __,   __,   __,  __,   __,   __,   __,   __ ]
                ]
            ],

            // Actions.
            [
                // 1. Selectors group.
                [
                    /*                ,
                    /* __ */ [   0 ],
                    /* GO */ ['2,,']
                ],

                // 2. Selector.
                [
                    /*           +      >      ~      s
                    /* __ */ [   0,     0,     0,     0 ],
                    /* GO */ ['3,+', '3,>', '3,~', '3, ']
                ],

                // 3. Simple selector sequence.
                [
                    /*            tu       w       |       *
                    /* __ */ [     0,      0,      0,      0 ],
                    /* GO */ [ '4,-1', '4,-1', '4,-1', '4,-1'],
                    /* TU */ [     0,     -1,      0,     -1 ],
                    /* OK */ [     0,      4,      0,      4 ]
                ],

                // 4. Hacpn.
                [
                    /*        [   ]  tu      f     w    ^    $    ~     *   |=    =    |  's    #    .    :
                    /* __ */ [0,  0,   0,    0,    0,   0,   0,   0,   0,   0,    0,   0,  0,   0,   0,   0],
                    /* GO */ [0,  0,   0,    0,    0,   0,   0,   0,   0,   0,    0,   0,  0,  '#', '.',  0],
                    /* OB */ [0,  0,  -3,    0,   -3,   0,   0,   0,   0,   0,    0,  -3,  0,   0,   0,   0],
                    /* NS */ [0,  0,   0,    0,   -3,   0,   0,   0,   0,   0,    0,   0,  0,   0,   0,   0],
                    /* ID */ [0, ']',  0,    0,    0,  '^', '$', '~=', '*', '|', '=',  0,  0,   0,   0,   0],
                    /* OP */ [0,  0,   0,    0,   'v',  0,   0,   0,   0,   0,    0,   0, 'v',  0,   0,   0],
                    /* VA */ [0,  0,   0,    0,    0,   0,   0,   0,   0,   0,    0,   0,  0,   0,   0,   0],
                    /* PC */ [0,  0,   0,  ':f',  ':',  0,   0,   0,   0,   0,    0,   0,  0,   0,   0,   0],
                    /* PE */ [0,  0,   0, '::f', '::',  0,   0,   0,   0,   0,    0,   0,  0,   0,   0,   0],
                    /* OK */ [0,  4,   0,    4,    4,   0,   0,   0,   0,   0,    0,   0,  0,   4,   4,   0]
                ]
            ]
        );
    }

    /**
     * Flush xpath current part.
     *
     * @param   string     $element     Element.
     * @param   array      $selector    Selectors collection.
     * @param   string     $pseudo      Pseudo-classes and pseudo-elements.
     * @return  string
     */
    protected function flush($element, $selector, $pseudo)
    {
        $out = $element;

        if (!empty($selector)) {
            $out .= '[(' . implode(') and (', $selector) . ')]';
        }

        $out .= $pseudo;

        return $out;
    }

    /**
     * Consume actions.
     * Please, see the actions table definition to learn more.
     *
     * @param   int        $action    Action.
     * @return  void
     */
    protected function consume($action)
    {
        static $__element = '*';
        static $_element  = '*';
        static $element   = '*';
        static $selector  = [];
        static $pseudo    = null;
        static $attribute = null;
        static $operator  = null;

        if (isset($this->buffers[0])) {
            if (false !== strpos($this->buffers[0], '|')) {
                $__element
                    = $_element
                    = $element
                    = str_replace('|', ':', $this->buffers[0]);
            } else {
                $__element = $this->buffers[0];

                if (null !== $p = $this->getDefaultNamespacePrefix()) {
                    $_element = $element = $p . ':' . $__element;
                } else {
                    $_element = $element = $__element;
                }
            }

            unset($this->buffers[0]);
        }

        switch ($action) {
            case '__init':
                $_element  = '*';
                $element   = '*';
                $selector  = [];
                $pseudo    = null;
                $attribute = null;
                $operator  = null;

                break;

            case '__flush':
                $this->_current .= $this->flush($element, $selector, $pseudo);
                $element         = null;
                $selector        = [];
                $pseudo          = null;

                break;

            case '+':
                $this->consume('__flush');
                $this->_current .= '/following-sibling::*[1]/self::';

                break;

            case '>':
                $this->consume('__flush');
                $this->_current .= '/';

                break;

            case '~':
                $this->consume('__flush');
                $this->_current .= '/following-sibling::';

                break;

            case ' ':
                $this->consume('__flush');
                $this->_current .= '//';

                break;

            case '#':
                $w          = substr($this->buffers[-1], 1);
                $selector[] = '@id = "' . $w . '"';

                break;

            case '.':
                $w          = substr($this->buffers[-1], 1);
                $selector[] =
                    'contains(concat(' .
                        '" ", ' .
                        'normalize-space(@class), ' .
                        '" "' .
                    '), " ' . $w . ' ")';

                break;

            case '=':
            case '^':
            case '$':
            case '~=':
            case '*':
            case '|':
                $attribute = str_replace('|', ':', $this->buffers[1]);
                unset($this->buffers[1]);
                $operator  = $action[0];

                break;

            case 'v':
                $w =
                    '"' === $this->buffers[-1][0]
                        ? str_replace('\"', '"', substr($this->buffers[-1], 1, -1))
                        : str_replace('\\\'', '\'', substr($this->buffers[-1], 1, -1));

                switch ($operator) {
                    case '=':
                        $selector[] = '@' . $attribute . ' = "' . $w . '"';

                        break;

                    case '^':
                        $selector[] = 'starts-with(@' . $attribute . ', "' . $w . '")';

                        break;

                    case '$':
                        $length     = strlen($w) - 1;
                        $selector[] =
                            'substring(@' . $attribute .
                            ', string-length(@' . $attribute . ') - ' .
                            $length . ') = "' . $w . '"';

                        break;

                    case '~':
                        $selector[] =
                            'contains(concat(" ", normalize-space(@' .
                            $attribute . '), " "), " ' . $w . ' ")';

                        break;

                    case '*':
                        $selector[] = 'contains(@' . $attribute . ', "' . $w . '")';

                        break;

                    case '|':
                        $selector[] =
                            '@' . $attribute . ' = "' . $w . '" or ' .
                            'starts-with(@' . $attribute . ', "' . $w .
                            '-")';

                        break;
                }

                $attribute = null;
                $operator  = null;

                break;

            case ']':
                $w = str_replace('|', ':', $this->buffers[1]);
                unset($this->buffers[1]);
                $selector[] = '@' . $w;

                break;

            case ':':
                $pc = $this->buffers[-1];

                switch ($pc) {
                    case 'root':
                        $this->_root = 'self::';

                        break;

                    case 'first-child':
                        if ('*' != $_element) {
                            $element    = '*';
                            $selector[] = 'name() = "' . $__element . '"';
                        }
                        $selector[] = 'position() = 1';

                        break;

                    case 'last-child':
                        if ('*' != $_element) {
                            $element    = '*';
                            $selector[] = 'name() = "' . $__element . '"';
                        }
                        $selector[] = 'position() = last()';

                        break;

                    case 'first-of-type':
                        if ('*' == $_element) {
                            throw new Compiler\Exception(
                                'Cannot have a :first-of-type without element.',
                                0
                            );
                        }

                        $selector[] = 'position() = 1';

                      break;

                    case 'last-of-type':
                        if ('*' == $_element) {
                            throw new Compiler\Exception(
                                'Cannot have a :last-of-type without element.',
                                1
                            );
                        }

                        $selector[] = 'position() = last()';

                        break;

                    case 'only-child':
                        if ('*' != $_element) {
                            $element    = '*';
                            $selector[] = 'name() = "' . $_element . '"';
                        }
                        $selector[] = 'last() = 1';

                        break;

                    case 'only-of-type':
                        if ('*' == $_element) {
                            throw new Compiler\Exception(
                                'Cannot have a :only-of-type without element.',
                                2
                            );
                        }

                        $selector[] = 'last() = 1';

                        break;

                    case 'empty':
                        $selector[] = 'not(*)';
                        $selector[] = 'not(normalize-space())';

                        break;

                    default:
                        $selector[] = $this->callPseudoClass($element, $pc);
                }

                break;

            case '::':
                $pe         = $this->buffers[-1];
                $selector[] = $this->callPseudoElement($element, $pe);

                break;

            case ':f':
                $first = strpos($this->buffers[-1], '(');
                $pcf   = substr($this->buffers[-1], 0, $first);
                $args  = substr($this->buffers[-1], $first + 1, -1);

                switch ($pcf) {
                    case 'nth-child':
                    case 'nth-of-type':
                        preg_match(
                            '#^(?:([+|-])?\s*(\d+)?\s*(n))?\s*([+|-]?\s*\d+)?$#',
                            $args,
                            $matches
                        );

                        $group =
                            !empty($matches[3])
                                ? '' !== $matches[2]
                                    ? @$matches[1] . str_replace(' ', '', $matches[2])
                                    : @$matches[1] . '1'
                                : '0';

                        $offset =
                            isset($matches[4]) && null !== $matches[4]
                                ? str_replace(' ', '', $matches[4])
                                : '0';

                        if (0 <= (int) $offset) {
                            $offset = '+' . trim($offset, '+');
                        }

                        $tesffo =
                            '+' == $offset[0]
                                ? '- ' . substr($offset, 1)
                                : '+ ' . substr($offset, 1);

                        if ('nth-child' == $pcf && '*' != $_element) {
                            $element    = '*';
                            $selector[] = 'name() = "' . $_element . '"';
                        } elseif ('nth-of-type' == $pcf && '*' == $_element) {
                            throw new Compiler\Exception(
                                'Cannot have a :nth-of-type without element.',
                                3
                            );
                        }

                        if (0 != (int) $group) {
                            if ('1' != $group) {
                                $selector[] = 'position() ' . $tesffo . ') mod ' .
                                              $group . ' = 0';
                            }

                            $selector[] = 'position() >= ' . (int) $offset;
                        } else {
                            $selector[] = 'position() = ' . (int) $offset;
                        }

                        break;

                    case 'nth-last-child':
                    case 'nth-last-of-type':
                        preg_match(
                            '#^(?:([+|-])?\s*(\d+)?\s*(n))?\s*([+|-]?\s*\d+)?$#',
                            $args,
                            $matches
                        );

                        $group =
                            !empty($matches[3])
                                ? '' !== $matches[2]
                                    ? @$matches[1] . str_replace(' ', '', $matches[2])
                                    : @$matches[1] . '1'
                                : '0';

                        $offset =
                            isset($matches[4]) && null !== $matches[4]
                                ? str_replace(' ', '', $matches[4])
                                : '0';

                        if (0 <= (int) $group) {
                            $group  = '+' . trim($group, '+');
                        }

                        if (0 <= (int) $offset) {
                            $offset = '+' . trim($offset, '+');
                        }

                        $puorg  =
                            '+' == $group[0]
                                ? '-' . substr($group, 1)
                                : '+' . substr($group, 1);

                        $tesffo =
                            '+' == $offset[0]
                                ? '- ' . substr($offset, 1)
                                : '+ ' . substr($offset, 1);

                        if ('nth-last-child' === $pcf && '*' !== $_element) {
                            $element    = '*';
                            $selector[] = 'name() = "' . $_element . '"';
                        } elseif ('nth-last-of-type' === $pcf && '*' === $_element) {
                            throw new Compiler\Exception(
                                'Cannot have a :nth-last-of-type without element.',
                                4
                            );
                        }

                        if (0 !== (int) $group) {
                            if ('1' !== $group) {
                                $selector[] =
                                    'position() ' . $offset . ') mod ' .
                                    $puorg . ' = 0';
                            }

                            $selector[] = 'position() <= (last() - ' . (int) $offset . ')';
                        } else {
                            $selector[] = 'position() = (last() - ' . (int) $offset . ')';
                        }

                        break;

                    default:
                        $selector[] = $this->callPseudoClassFunction($element, $pcf);
                }

                break;

            case '::f':
                $pef = $this->buffers[-1];
                $this->_current .= $this->callPseudoElementFunction($element, $pef);

                break;
        }
    }

    /**
     * Compute source code before compiling it.
     *
     * @param   string     &$in    Source code.
     * @return  void
     */
    protected function pre(&$in)
    {
        $this->_root    = 'descendant-or-self::';
        $this->_current = null;
        $this->consume('__init');

        return;
    }

    /**
     * Verify compiler state when ending the source code.
     *
     * @return  bool
     */
    protected function end()
    {
        $this->consume('__flush');
        $this->_current = $this->_root . $this->_current;

        return true;
    }

    /**
     * Get the result of the compiling.
     *
     * @return  string
     */
    public function getResult()
    {
        return $this->getXPath();
    }

    /**
     * Get result.
     *
     * @return  string
     */
    public function getXPath()
    {
        return $this->_current;
    }

    /**
     * Call this method when a pseudo-class is unknown.
     *
     * @param   string     $element        Element.
     * @param   string     $pseudoClass    Pseudo-class.
     * @return  string
     */
    protected function callPseudoClass($element, $pseudoClass)
    {
        throw new Compiler\Exception(
            'The pseudo-class %s on the element %s is unknown.',
            5,
            [$pseudoClass, $element]
        );
    }

    /**
     * Call this method when a pseudo-element is unknown.
     *
     * @param   string     $element          Element.
     * @param   string     $pseudoElement    Pseudo-element.
     * @return  string
     */
    protected function callPseudoElement($element, $pseudoElement)
    {
        throw new Compiler\Exception(
            'The pseudo-element %s on the element %s is unknown.',
            6,
            [$pseudoElement, $element]
        );
    }

    /**
     * Call this method when a pseudo-class function is unknown.
     *
     * @param   string     $element     Element.
     * @param   string     $function    Pseudo-class function.
     * @return  string
     */
    protected function callPseudoClassFunction($element, $function)
    {
        throw new Compiler\Exception(
            'The pseudo-class function %s on the element %s is unknown.',
            7,
            [$function, $element]
        );
    }

    /**
     * Call this method when a pseudo-element funcion is unknown.
     *
     * @param   string     $element     Element.
     * @param   string     $function    Pseudo-element function.
     * @return  string
     */
    protected function callPseudoElementFunction($element, $function)
    {
        throw new Compiler\Exception(
            'The pseudo-element function %s on the element %s is unknown.',
            8,
            [$function, $element]
        );
    }

    /**
     * Set the default namespace prefix (e.g. __current_ns).
     *
     * @param   string  $prefix    Default prefix.
     * @return  string
     */
    public function setDefaultNamespacePrefix($prefix)
    {
        $old           = $this->_prefix;
        $this->_prefix = $prefix;

        return $old;
    }

    /**
     * Get the default namespace prefix.
     *
     * @return  string
     */
    public function getDefaultNamespacePrefix()
    {
        return $this->_prefix;
    }
}
