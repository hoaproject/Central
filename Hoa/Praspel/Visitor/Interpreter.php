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

namespace Hoa\Praspel\Visitor;

use Hoa\Consistency;
use Hoa\Praspel;
use Hoa\Realdom;
use Hoa\Ustring;
use Hoa\Visitor;

/**
 * Class \Hoa\Praspel\Visitor\Interpreter.
 *
 * Compile Praspel to model.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Interpreter implements Visitor\Visit
{
    /**
     * Root.
     *
     * @var \Hoa\Praspel\Model\Specification
     */
    protected $_root            = null;

    /**
     * Current clause.
     *
     * @var \Hoa\Praspel\Model\Clause
     */
    protected $_clause          = null;

    /**
     * Current object.
     *
     * @var \Hoa\Praspel\Model
     */
    protected $_current         = null;

    /**
     * Classname to bind to the specification.
     *
     * @var string
     */
    protected $_classnameToBind = null;



    /**
     * Visit an element.
     *
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     * @return  mixed
     * @throws  \Hoa\Praspel\Exception\Interpreter
     */
    public function visit(
        Visitor\Element $element,
        &$handle = null,
        $eldnah  = null
    ) {
        $id = $element->getId();

        switch ($id) {
            case '#specification':
                $this->_clause       =
                    $this->_current  =
                        $this->_root =
                            new Praspel\Model\Specification();

                if (null !== $classname = $this->getBindedClass()) {
                    $this->_root->bindToClass($classname);
                }

                foreach ($element->getChildren() as $child) {
                    $child->accept($this, $handle, $eldnah);
                }

                return $this->_root;

            case '#is':
                $this->_clause = $this->_root->getClause('is');

                foreach ($element->getChildren() as $child) {
                    $this->_clause->addProperty(
                        $this->_clause->getPropertyValue(
                            $child->accept($this, $handle, $eldnah)
                        )
                    );
                }

                break;

            case '#requires':
            case '#ensures':
            case '#invariant':
                $this->_clause = $this->_current->getClause(substr($id, 1));

                foreach ($element->getChildren() as $child) {
                    $child->accept($this, $handle, $eldnah);
                }

                break;

            case '#behavior':
                $children      = $element->getChildren();
                $child0        = array_shift($children);
                $identifier    = $child0->accept($this, $handle, false);
                $previous      = $this->_current;

                $this->_clause      =
                    $this->_current =
                        $this
                            ->_current
                            ->getClause('behavior')
                            ->get($identifier);

                foreach ($children as $child) {
                    $child->accept($this, $handle, $eldnah);
                }

                $this->_current = $previous;

                break;

            case '#default':
                $children           = $element->getChildren();
                $previous           = $this->_current;
                $this->_clause      =
                    $this->_current =
                        $this
                            ->_current
                            ->getClause('default');

                foreach ($children as $child) {
                    $child->accept($this, $handle, $eldnah);
                }

                $this->_current = $previous;

                break;

            case '#throwable':
                $this->_clause = $this->_current->getClause('throwable');
                $identifier    = null;

                foreach ($element->getChildren() as $child) {
                    switch ($child->getId()) {
                        case '#exception_identifier':
                            $_identifier = $child->getChild(1)->accept(
                                $this,
                                $handle,
                                false
                            );
                            $_instanceof = $child->getChild(0)->accept(
                                $this,
                                $handle,
                                false
                            );

                            $this->_clause[$_identifier] = $_instanceof;

                            if (null === $identifier) {
                                $identifier = $_identifier;
                            } else {
                                $this->_clause[$_identifier]->disjunctionWith(
                                    $identifier
                                );
                            }

                            break;

                        case '#exception_with':
                            $old           = $this->_clause;
                            $this->_clause = $old->newWith();

                            foreach ($child->getChildren() as $_child) {
                                $_child->accept($this, $handle, $eldnah);
                            }

                            $old[$identifier]->setWith($this->_clause);
                            $this->_clause = $old;

                            break;
                    }
                }

                break;

            case '#description':
                $this->_clause   = $this->_root->getClause('description');
                $this->_clause[] = $element->getChild(0)->accept(
                    $this,
                    $handle,
                    $eldnah
                );

                break;

            case '#declaration':
                $left  = $element->getChild(0)->accept($this, $handle, false);
                $right = $element->getChild(1)->accept($this, $handle, $eldnah);

                $variable = $left;

                if ($right instanceof Praspel\Model\Variable) {
                    $right = realdom()->variable($right);
                }

                $this->_clause[$variable]->in = $right;

                break;

            case '#local_declaration':
                $left  = $element->getChild(0)->accept($this, $handle, false);
                $right = $element->getChild(1)->accept($this, $handle, $eldnah);

                $variable = $left;

                if ($right instanceof Praspel\Model\Variable) {
                    $right = realdom()->variable($right);
                }

                $this->_clause->let[$variable]->in = $right;

                break;

            case '#qualification':
                $children = $element->getChildren();
                $variable = $this->_clause[array_shift($children)->accept(
                    $this,
                    $handle,
                    false
                )];

                foreach ($children as $child) {
                    $variable->is($child->accept($this, $handle, false));
                }

                break;

            case '#contains':
                $variable = $element->getChild(0)->accept($this, $handle, false);
                $value    = $element->getChild(1)->accept($this, $handle, false);

                $this->_clause[$variable]->contains($value);

                break;

            case '#domainof':
                $left  = $element->getChild(0)->accept($this, $handle, false);
                $right = $element->getChild(1)->accept($this, $handle, false);

                $this->_clause[$left]->domainof($right);

                break;

            case '#predicate':
                $this->_clause->predicate(
                    $element->getChild(0)->accept($this, $handle, $eldnah)
                );

                break;

            case '#disjunction':
                $disjunction = realdom();

                foreach ($element->getChildren() as $child) {
                    $value = $child->accept($this, $handle, $eldnah);

                    if ($value instanceof Realdom\Disjunction) {
                        $disjunction[] = $value;
                    } elseif ($value instanceof Praspel\Model\Variable) {
                        $disjunction->variable($value);
                    } else {
                        $disjunction->const($value);
                    }
                }

                return $disjunction;

            case '#realdom':
                $children  = $element->getChildren();
                $child0    = array_shift($children);
                $name      = $child0->accept($this, $handle, false);
                $arguments = [];

                foreach ($children as $child) {
                    $argument = $child->accept($this, $handle, $eldnah);

                    if ($argument instanceof Realdom\Disjunction) {
                        $realdoms = $argument->getRealdoms();
                        $argument = $realdoms[0];
                    }

                    $arguments[] = $argument;
                }

                return realdom()->_call($name, $arguments);

            case '#concatenation':
                $string = null;

                foreach ($element->getChildren() as $child) {
                    $string .= $child->accept($this, $handle, $eldnah);
                }

                return $string;

            case '#array':
                $array = [];

                foreach ($element->getChildren() as $child) {
                    if ('#pair' === $child->getId()) {
                        $key     = $child->getChild(0)->accept($this, $handle, $eldnah);
                        $value   = $child->getChild(1)->accept($this, $handle, $eldnah);
                        $array[] = [$key, $value];

                        continue;
                    }

                    $key     = realdom()->natural(0, 1);
                    $value   = $child->accept($this, $handle, $eldnah);
                    $array[] = [$key, $value];
                }

                return $array;

            case '#range':
                $left  = $element->getChild(0)->accept($this, $handle, $eldnah);
                $right = $element->getChild(1)->accept($this, $handle, $eldnah);

                if (is_float($left) || is_float($right)) {
                    return realdom()->boundfloat(
                        floatval($left),
                        floatval($right)
                    );
                }

                return realdom()->boundinteger(intval($left), intval($right));

            case '#left_range':
                $left = $element->getChild(0)->accept($this, $handle, $eldnah);

                if (is_float($left)) {
                    return realdom()->boundfloat($left);
                }

                return realdom()->boundinteger($left);

            case '#right_range':
                $right = $element->getChild(0)->accept($this, $handle, $eldnah);

                if (is_float($right)) {
                    return realdom()->boundfloat(null, $right);
                }

                return realdom()->boundinteger(null, $right);

            case '#arrayaccessbykey':
                $variable = $element->getChild(0)->accept($this, $handle, $eldnah);
                $key      = $element->getChild(1)->accept($this, $handle, $eldnah);

                $this->_clause[$variable]->key($key);

                return $variable;

            case '#dynamic_resolution':
                $value = null;

                foreach ($element->getChildren() as $child) {
                    if (null !== $value) {
                        $value .= '->';
                    }

                    $value .= $child->accept($this, $handle, false);
                }

                if (false !== $eldnah) {
                    return $this->_clause->getVariable($value, true);
                }

                return $value;

            case '#self_identifier':
            case '#static_identifier':
            case '#parent_identifier':
                $identifier = substr($id, 1, strpos($id, '_', 1) - 1);

                foreach ($element->getChildren() as $child) {
                    $identifier .= '::' . $child->accept($this, $handle, $eldnah);
                }

                return $identifier;

            case '#old':
                $value =
                    '\old(' .
                     $element->getChild(0)->accept($this, $handle, false) .
                     ')';

                if (false !== $eldnah) {
                    return $this->_clause->getVariable($value);
                }

                return $value;

            case '#result':
                return '\result';

            case '#classname':
                $classname = [];

                foreach ($element->getChildren() as $child) {
                    $classname[] = $child->accept($this, $handle, $eldnah);
                }

                return implode('\\', $classname);

            case '#nowdoc':
            case '#heredoc':
                return $element->getChild(1)->accept($this, $handle, $eldnah);

            case '#regex':
                $regex = $element->getChild(0)->accept($this, $handle, $eldnah);

                if (true === $element->childExists(1)) {
                    $length = $element->getChild(1)->accept($this, $handle, $eldnah);
                }

                return realdom()->regex($regex);

            case '#class':
                $classname = $element->getChild(0)->accept($this, $handle, false);

                return realdom()->class($classname);

            case 'token':
                $tId   = $element->getValueToken();
                $value = $element->getValueValue();

                switch ($tId) {
                    case 'identifier':
                        if (false !== $eldnah) {
                            return $this->getIdentifier($value);
                        }

                        return $value;

                    case 'this':
                        if (false !== $eldnah) {
                            return $this->_root->getImplicitVariable($value);
                        }

                        return $value;

                    case 'content':
                    case 'pure':
                    case 'result':
                        return $value;

                    case 'default':
                        return …;

                    case 'null':
                        return null;

                    case 'true':
                        return true;

                    case 'false':
                        return false;

                    case 'binary':
                        $int = intval(substr($value, strpos($value, 'b') + 1), 2);

                        if ('-' === $value[0]) {
                            return -$int;
                        }

                        return $int;

                    case 'octal':
                        $int = intval(substr($value, strpos($value, '0') + 1), 8);

                        if ('-' === $value[0]) {
                            return -$int;
                        }

                        return $int;

                    case 'hexa':
                        $value = strtolower($value);
                        $int   = intval(substr($value, strpos($value, 'x') + 1), 16);

                        if ('-' === $value[0]) {
                            return -$int;
                        }

                        return $int;

                    case 'decimal':
                        if (true === ctype_digit(ltrim($value, '+-'))) {
                            return intval($value);
                        }

                        return floatval($value);

                    case 'escaped':
                        switch ($value[1]) {
                            case 'n':
                                return "\n";

                            case 'r':
                                return "\r";

                            case 't':
                                return "\t";

                            case 'v':
                                return "\v";

                            case 'e':
                                return "\033";

                            case 'f':
                                return "\f";

                            case 'b':
                                return "\033[D";

                            case 'x':
                                return Ustring::fromCode(hexdec($value));

                            case '\\':
                                return $value[1];

                            default:
                                return Ustring::fromCode(octdec($value));
                        }

                    case 'accepted':
                    case 'string':
                    case 'regex':
                        return $value;

                    default:
                        throw new Praspel\Exception\Interpreter(
                            'Token %s is not yet implemented.',
                            1,
                            $tId
                        );
                }

                break;

            default:
                throw new Praspel\Exception\Interpreter(
                    'Element %s is unknown.',
                    2,
                    $id
                );
        }
    }

    /**
     * Get identifier object.
     *
     * @param   string  $identifier    Identifier.
     * @return  \Hoa\Praspel\Model\Variable
     */
    public function getIdentifier($identifier)
    {
        if (!isset($this->_clause[$identifier])) {
            throw new Praspel\Exception\Interpreter(
                'The identifier %s does not exist on clause %s.',
                3,
                [$identifier, $this->_clause->getName()]
            );
        }

        return $this->_clause[$identifier];
    }

    /**
     * Get root.
     *
     * @return  \Hoa\Praspel\Model\Specification
     */
    public function getRoot()
    {
        return $this->_root;
    }

    /**
     * Get current clause.
     *
     * @return  \Hoa\Praspel\Model\Clause
     */
    public function getClause()
    {
        return $this->_clause;
    }

    /**
     * Set classname to bind.
     *
     * @param   string  $classname    Classname.
     * @return  string
     */
    public function bindToClass($classname)
    {
        $old                    = $this->_classnameToBind;
        $this->_classnameToBind = $classname;

        return $old;
    }

    /**
     * Get classname to bind.
     *
     * @return  string
     */
    public function getBindedClass()
    {
        return $this->_classnameToBind;
    }
}

if (false === Consistency::entityExists('Hoa\Realdom\Disjunction', true)) {
    throw new Praspel\Exception('Hoa\Realdom seems to not be loaded.');
}
