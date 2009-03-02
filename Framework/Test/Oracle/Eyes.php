<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Oracle_Eyes
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Test_Oracle_Exception
 */
import('Test.Oracle.Exception');

/**
 * Hoa_Test_Praspel_Compiler
 */
import('Test.Praspel.Compiler');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Pom_Token_Root
 */
import('Pom.Token.Root');

/**
 * Hoa_Pom_Token_New
 */
import('Pom.Token.New');

/**
 * Hoa_Pom_Token_String
 */
import('Pom.Token.String');

/**
 * Hoa_Pom_Token_String_Null
 */
import('Pom.Token.String.Null');

/**
 * Hoa_Pom_Token_Class
 */
import('Pom.Token.Class');

/**
 * Hoa_Pom_Token_Instruction
 */
import('Pom.Token.Instruction');

/**
 * Hoa_Pom_Token_LateParsing
 */
import('Pom.Token.LateParsing');

/**
 * Hoa_Pom_Token_Variable
 */
import('Pom.Token.Variable');

/**
 * Hoa_Pom_Token_Operation
 */
import('Pom.Token.Operation');

/**
 * Hoa_Pom_Token_Operator_Assignement
 */
import('Pom.Token.Operator.Assignement');

/**
 * Hoa_Pom_Token_Whitespace
 */
import('Pom.Token.Whitespace');

/**
 * Hoa_Pom_Token_Php
 */
import('Pom.Token.Php');

/**
 * Hoa_Pom_Parser_Lexer
 */
import('Pom.Parser.Lexer');

/**
 * Class Hoa_Test_Oracle_Eyes.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Oracle_Eyes
 */

class Hoa_Test_Oracle_Eyes {

    /**
     * The request object.
     *
     * @var Hoa_Test_Request object
     */
    protected static $_request = null;



    /**
     * Set the request object.
     *
     * @access  public
     * @param   Hoa_Test_Request  $request    The request object.
     * @return  Hoa_Test_Request
     */
    public function setRequest ( Hoa_Test_Request $request ) {

        $old            = self::$_request;
        self::$_request = $request;

        return $old;
    }

    /**
     * Oracle opens eyes and looks the future.
     *
     * @access  public
     * @return  void
     */
    public function open ( ) {

        $incubator    = self::getRequest()->getParameter('test.incubator');
        $battleground = self::getRequest()->getParameter('test.ordeal.battleground');
        $files        = self::getRequest()->getParameter('convict.result');
        $prefix       = self::getRequest()->getParameter('test.ordeal.methodPrefix');

        foreach($files as $i => $file) {

            $parser  = Hoa_Pom::parse($incubator . $file, Hoa_Pom::TOKENIZE_FILE);
            $root    = new Hoa_Pom_Token_Root();
            $out     = null;
            $classes = array(); // only classes names.

            $root->addElement(new Hoa_Pom_Token_Php('<?php'));

            foreach($parser->getElements() as $i => $element)
                if($element instanceof Hoa_Pom_Token_Class) {

                    $classes[] = 'Test_' . $element->getName()->getString();

                    $out = new Hoa_Pom_Token_Class(
                        new Hoa_Pom_Token_String(
                            'Test_' . $element->getName()->getString()
                        )
                    );
                    $out->addAttribute(
                        new Hoa_Pom_Token_Class_Attribute(
                            new Hoa_Pom_Token_Variable(
                                new Hoa_Pom_Token_String('_convict')
                            ),
                            new Hoa_Pom_Token_String_Null('null')
                        )
                    );

                    $setup = new Hoa_Pom_Token_Class_Method(
                        new Hoa_Pom_Token_String('setUp')
                    );

                    if(true === $element->isAbstract()) {

                        $out->addMethod($setup);
                        continue;
                    }

                    foreach($element->getMethods() as $e => $method) {

                        $construct = strtolower($method->getName()->getString()) == '__construct';

                        $praspel  = Hoa_Test_Praspel_Compiler::compile(
                            $method->getComment()->getComment()
                        );
                        $praspel .= "\n" .
                                    (true === $construct
                                         ? '$this->_convict = '
                                         : ''
                                    ) .
                                    '$praspel->call(' . "\n" .
                                    (true === $construct
                                         ? '    \'' . $element->getName()->getString() . "',\n"
                                         : '    $this->_convict,' . "\n"
                                    ) .
                                    '    \'' . $prefix . 'magicCaller\',' . "\n" .
                                    '    \'' . $method->getName()->getString() . '\'' . "\n" .
                                    ');' . "\n\n" .
                                    '$praspel->verify();' . "\n";
                        $praspel  = '        ' .
                                    str_replace("\n", "\n        ", trim($praspel));
                        $praspel  = "\n\n" . $praspel . "\n    ";

                        $praspel  = array(array(
                            0 => -1,
                            1 => $praspel,
                            2 => -1,
                        ));

                        $name = new Hoa_Pom_Token_String(
                            '__test_' . $method->getName()->getString()
                        );
                        $meth = new Hoa_Pom_Token_Class_Method($name);
                        $meth->addBody(new Hoa_Pom_Token_LateParsing($praspel));

                        $setup->addBody(
                            new Hoa_Pom_Token_Instruction(
                                new Hoa_Pom_Token_Call_Method(
                                    new Hoa_Pom_Token_Variable(
                                        new Hoa_Pom_Token_String('this')
                                    ),
                                    $name
                                )
                            )
                        );
                        $out->addMethod($meth);
                    }

                    $out->addMethod($setup);
                    $root->addElement($out);
                }

            $root->addElement(new Hoa_Pom_Token_Whitespace("\n"));

            foreach($classes as $i => $class)
                $root->addElements(array(
                    new Hoa_Pom_Token_Whitespace("\n"),
                    new Hoa_Pom_Token_Instruction(
                        new Hoa_Pom_Token_Operation(array(
                            new Hoa_Pom_Token_Variable(
                                new Hoa_Pom_Token_String('bootstrap')
                            ),
                            new Hoa_Pom_Token_Operator_Assignement('='),
                            new Hoa_Pom_Token_New(
                                new Hoa_Pom_Token_String($class)
                            )
                        ))
                    ),
                    new Hoa_Pom_Token_Instruction(
                        new Hoa_Pom_Token_Call_Method(
                            new Hoa_Pom_Token_Variable(
                                new Hoa_Pom_Token_String('bootstrap')
                            ),
                            new Hoa_Pom_Token_String('setUp')
                        )
                    )
                ));

            file_put_contents($battleground . $file, Hoa_Pom::dump($root));
        }
    }

    /**
     * Get the request object.
     *
     * @access  protected
     * @return  Hoa_Test_Request
     */
    protected static function getRequest ( ) {

        return self::$_request;
    }
}
