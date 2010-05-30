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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Test_Praspel_Compiler
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Test_Praspel_Exception
 */
import('Test.Praspel.Exception');

/**
 * Hoa_Test_Praspel
 */
import('Test.Praspel.~');

/**
 * Hoa_Compiler_Ll1
 */
import('Compiler.Ll1');

/**
 * Class Hoa_Test_Praspel_Compiler.
 *
 * The Praspel compiler.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Compiler
 */

class Hoa_Test_Praspel_Compiler extends Hoa_Compiler_Ll1 {

    /**
     * The Praspel's object model root.
     *
     * @var Hoa_Test_Praspel object
     */
    protected $_praspel = null;

    /**
     * The current node in the Praspel's object model.
     *
     * @var Hoa_Test_Praspel object
     */
    private $_current   = null;



    /**
     * Set the compiler.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        parent::__construct(
            // Skip.
            array(
                '#\s+',          // white spaces
                '#//.*',         // inline comment
                '#/\*(.|\n)*\*/' // block comment
            ),

            // Tokens.
            array(
                // 1. Clauses.
                array(
                    '#@requires',  // r
                    '#@ensures',   // e
                    '#@throwable', // t
                    '#@invariant', // i
                    '#@predicate', // p
                    ';'
                ),

                // 2. Expressions.
                array(
                    '#and',        // &
                    ':',           // :
                    '#typeof'      // to
                ),

                // 3. List.
                array(
                    '#\w+',        // id
                    ','            // ,
                ),

                // 4. Extend.
                array(

                ),

                // 5. Variable.
                array(
                    '#\w+',        // id
                    '#\\\result',  // \r
                    '#\\\old\(',   // \o
                    ')'            // )
                ),

                // 6. Types.
                array(
                    '#or',         // or
                    '#\w+',        // id
                    '(',           // (
                    ')'            // )
                ),

                // 7. Arguments.
                array(
                    ',',           // ,
                    '#\d+',        // 09
                    '(',           // (
                    ')',           // )
                    '[',           // [
                    '\'',          // '
                    '#\w+'         // id
                ),

                // 8. Array.
                array(
                    ']'            // ]
                ),

                // 9. Pairs.
                array(
                    ',',           // ,
                    '#from',       // fr
                    '#to'          // to
                ),

                // 10. String.
                array(
                    '#[\w|\s]+',   // st
                    '\''           // '
                ),
            ),

            // States.
            array(
                // 1. Clauses.
                array(
                     __ , // error
                    'GO', // start
                    'EX', // expressions
                    'LI', // list
                    'IN', // extend/inheritance
                ),

                // 2. Expressions.
                array(
                     __ , // error
                    'GO', // start
                    'TY', // type
                    'TO'  // typeof
                ),

                // 3. List.
                array(
                     __ , // error
                    'GO', // start
                    'CO'  // comma
                ),

                // 4. Extend.
                array(

                ),

                // 5. Variable.
                array(
                     __ , // error
                    'GO', // start
                    'OL', // old(
                    'D)', // )
                    'OK'  // terminal
                ),

                // 6. Types.
                array(
                     __ , // error
                    'GO', // start
                    'ID', // id
                    'AR', // arguments
                    'OK'  // terminal
                ),

                // 7. Arguments.
                array(
                     __ , // error
                    'GO', // start
                    'ID', // id
                    'AR', // arguments
                    '[]', // array
                    'ST', // string
                    'OK'  // terminal
                ),

                // 8. Array.
                array(
                     __ , // error
                    'GO', // start
                    'OK'  // terminal
                ),

                // 9. Pairs.
                array(
                     __ , // error
                    'GO', // start
                    'FR', // from
                    'TO', // to
                    'OK'  // terminal
                ),

                // 10. String.
                array(
                     __ , // error
                    'GO', // start
                    'ST', // string
                    'OK'  // terminal
                ),
            ),

            // Terminal.
            array(
                // 1. Clauses.
                array('GO'),

                // 2. Expressions.
                array('GO', 'TY', 'TO'),

                // 3. List.
                array('GO', 'CO'),

                // 4. Extend.
                array(),

                // 5. Variable.
                array('OK'),

                // 6. Types.
                array('GO', 'OK'),

                // 7. Arguments.
                array('GO', '[]', 'ST', 'OK'),

                // 8. Array.
                array('OK'),

                // 9. Pairs.
                array('GO', 'TO', 'OK'),

                // 10. String.
                array('OK'),
            ),

            // Transitions.
            array(
                // 1. Clauses.
                array(
                    /*               r     e     t     i     p     ;
                    /* __ */ array( __ ,  __ ,  __ ,  __ ,  __ ,  __ ),
                    /* GO */ array('EX', 'EX', 'LI', 'EX', 'IN',  __ ),
                    /* EX */ array( __ ,  __ ,  __ ,  __ ,  __ , 'GO'),
                    /* LI */ array( __ ,  __ ,  __ ,  __ ,  __ , 'GO'),
                    /* IN */ array( __ ,  __ ,  __ ,  __ ,  __ , 'GO')
                ),

                // 2. Expressions.
                array(
                    /*               &     :    to
                    /* __ */ array( __ ,  __ ,  __ ),
                    /* GO */ array( __ , 'TY', 'TO'),
                    /* TY */ array('GO',  __ ,  __ ),
                    /* TO */ array('GO',  __ ,  __ )
                ),

                // 3. List.
                array(
                    /*              \w     ,
                    /* __ */ array( __ ,  __ ),
                    /* GO */ array('CO',  __ ),
                    /* CO */ array( __ , 'GO')
                ),

                // 4. Extend.
                array(
                ),

                // 5. Variable.
                array(
                    /*              id    \r    \o    )
                    /* __ */ array( __ ,  __ ,  __ ,  __ ),
                    /* GO */ array('OK', 'OK', 'OL',  __ ),
                    /* OL */ array('D)',  __ ,  __ ,  __ ),
                    /* D) */ array( __ ,  __ ,  __ , 'OK'),
                    /* OK */ array( __ ,  __ ,  __ ,  __ )
                ),

                // 6. Types.
                array(
                    /*              or    id     (    )
                    /* __ */ array( __ ,  __ ,  __ ,  __ ),
                    /* GO */ array( __ , 'ID',  __ ,  __ ),
                    /* ID */ array( __ ,  __ , 'AR',  __ ),
                    /* AR */ array( __ ,  __ ,  __ , 'OK'),
                    /* OK */ array('GO',  __ ,  __ ,  __ )
                ),

                // 7. Arguments.
                array(
                    /*               ,    09     (     )     [     '    id 
                    /* __ */ array( __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ),
                    /* GO */ array( __ , 'OK',  __ ,  __ , '[]', 'ST', 'ID'),
                    /* ID */ array( __ ,  __ , 'AR',  __ ,  __ ,  __ ,  __ ),
                    /* AR */ array( __ ,  __ ,  __ , 'OK',  __ ,  __ ,  __ ),
                    /* [] */ array('GO',  __ ,  __ ,  __ ,  __ ,  __ ,  __ ),
                    /* ST */ array('GO',  __ ,  __ ,  __ ,  __ ,  __ ,  __ ),
                    /* OK */ array('GO',  __ ,  __ ,  __ ,  __ ,  __ ,  __ )
                ),

                // 8. Array.
                array(
                    /*               ]
                    /* __ */ array( __ ),
                    /* GO */ array('OK'),
                    /* OK */ array( __ )
                ),

                // 9. Pairs.
                array(
                    /*               ,    fr    to
                    /* __ */ array( __ ,  __ ,  __ ),
                    /* GO */ array( __ , 'FR', 'TO'),
                    /* FR */ array( __ ,  __ , 'TO'),
                    /* TO */ array('OK',  __ ,  __ ),
                    /* OK */ array( __ ,  __ ,  __ )
                ),

                // 10. String.
                array(
                    /*              id     '
                    /* __ */ array( __ ,  __ ),
                    /* GO */ array('ST', 'OK'),
                    /* ST */ array( __ , 'OK'),
                    /* OK */ array( __ ,  __ )
                ),
            ),

            // Actions.
            array(
                // 1. Clauses.
                array(
                    /*              r    e    t    i    p    ;
                    /* __ */ array( 0 ,  0 ,  0 ,  0 ,  0 ,  0 ),
                    /* GO */ array('r', 'e', 't',  0 ,  0 ,  0 ),
                    /* EX */ array( 2 ,  2 ,  0 ,  2 ,  0 , 'D'),
                    /* LI */ array( 0 ,  0 ,  3 ,  0 ,  0 , 'l'),
                    /* IN */ array( 0 ,  0 ,  0 ,  0 ,  0 ,  0 )
                ),

                // 2. Expressions.
                array(
                    /*              &    :   to
                    /* __ */ array( 0 ,  0 ,  0 ),
                    /* GO */ array( 5 , ':', 'd'),
                    /* TY */ array('&',  6 ,  0 ),
                    /* TO */ array('D',  0 ,  5 )
                ),

                // 3. List.
                array(
                    /*             id    ,
                    /* __ */ array( 0 ,  0 ),
                    /* GO */ array(-5 ,  0 ),
                    /* CO */ array( 0 , 'l')
                ),

                // 4. Extend.
                array(

                ),

                // 5. Variable.
                array(
                    /*             id   \r   \o    )
                    /* __ */ array( 0 ,  0 ,  0 ,  0 ),
                    /* GO */ array(-1 , -1 , -1 ,  0 ),
                    /* OL */ array(-1 ,  0 ,  0 ,  0 ),
                    /* D) */ array( 0 ,  0 ,  0 , -1 ),
                    /* OK */ array( 0 ,  0 ,  0 ,  0 )
                ),

                // 6. Types.
                array(
                    /*             or   id    (    )
                    /* __ */ array( 0 ,  0 ,  0 ,  0 ),
                    /* GO */ array('|', -3,   0 ,  0 ),
                    /* ID */ array( 0 ,  0 , 'y',  0 ),
                    /* AR */ array( 0 ,  0 ,  7 , 'Y'),
                    /* OK */ array( 0 ,  0 ,  0 ,  0 )
                ),

                // 7. Arguments.
                array(
                    /*              ,   09    (    )    [    '   id
                    /* __ */ array( 0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ),
                    /* GO */ array( 0 , -7 ,  0 ,  0 , '[',  0 ,  0 ),
                    /* ID */ array( 0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ),
                    /* AR */ array( 0 ,  0 ,  7 ,  0 ,  0 ,  0 ,  0 ),
                    /* [] */ array( 0 ,  0 ,  0 ,  0 ,  8 ,  0 ,  0 ),
                    /* ST */ array( 0 ,  0 ,  0 ,  0 ,  0 , 10 ,  0 ),
                    /* OK */ array('c',  0 ,  0 ,  0 ,  0 ,  0 ,  0 )
                ),

                // 8. Array.
                array(
                    /*              ]
                    /* __ */ array( 0   ),
                    /* GO */ array('9,]'),
                    /* OK */ array( 0   )
                ),

                // 9. Pairs.
                array(
                    /*              ,   fr  to
                    /* __ */ array( 0 ,  0,  0  ),
                    /* GO */ array( 0 ,  0,  0  ),
                    /* FR */ array( 0 ,  6, 'to'),
                    /* TO */ array(',',  0,  6  ),
                    /* OK */ array( 9 ,  0,  0  )
                ),

                // 10. String
                array(
                    /*             st    ' 
                    /* __ */ array( 0 ,  0 ),
                    /* GO */ array( 0 ,  0 ),
                    /* ST */ array( 0 ,  0 ),
                    /* OK */ array( 0 ,  0 )
                ),
            ),

            // Names.
            array(
                'Clauses',
                'Expressions',
                'List',
                'Extend',
                'Variable',
                'Types',
                'Arguments',
                'Array',
                'Pairs',
                'String'
            )
        );
    }

    /**
     * Consume actions.
     * Please, see the actions table definition to learn more.
     *
     * @access  protected
     * @param   int        $action    Action.
     * @return  void
     */
    protected function consume ( $action ) {

        switch($action) {

            // @requires
            case 'r':
                $this->_current = $this->_praspel->clause('requires');
              break;

            // @ensures
            case 'e':
                $this->_current = $this->_praspel->clause('ensures');
              break;

            // @throwable
            case 't':
                $this->_current = $this->_praspel->clause('throwable');
              break;

            // variable:
            // variable typeof
            case ':':
            case 'd':
                $this->_current = $this->_current->variable(
                    $this->buffers[0]
                );
                unset($this->buffers[0]);
              break;

            // variable: type(
            case 'y':
                $this->_current = $this->_current->isTypedAs(
                    $this->buffers[1]
                );
                unset($this->buffers[1]);
              break;

            // variable: type(…)
            case 'Y':
                if(isset($this->buffers[3])) {

                    if(ctype_digit($this->buffers[3]))
                        $this->buffers[3] = (int) $this->buffers[3];

                    $this->_current = $this->_current->with(
                        $this->buffers[3]
                    );
                    unset($this->buffers[3]);
                }

                $this->_current = $this->_current->_ok();
              break;

            // variable: type([
            case '[':
                $this->_current = $this->_current->withArray()->from();
              break;

            // variable: type([…,
            case ',':
                $this->_current = $this->_current->from();
              break;

            // variable: type([… to
            case 'to':
                $this->_current = $this->_current->to();
              break;

            // variable: type([…]
            case ']':
                $this->_current = $this->_current->end();
              break;

            // variable: type(…,
            case 'c':
                if(ctype_digit($this->buffers[3]))
                    $this->buffers[3] = (int) $this->buffers[3];

                $this->_current = $this->_current->with(
                    $this->buffers[3]
                )->_comma;
                unset($this->buffers[3]);
              break;

            // variable: type() or
            case '|':
                $this->_current = $this->_current->_or;
              break;

            // variable: type() and
            case '&':
                $this->_current = $this->_current->_and;
              break;

            // variable typeof variable and
            // variable typeof variable;
            case 'D':
                if(!isset($this->buffers[0]))
                    break;

                $this->_current = $this->_current->hasTheSameTypeAs(
                    $this->buffers[0]
                );
                $this->_current = $this->_current->_and;
                unset($this->buffers[0]);
              break;

            // @throwable T_1,
            // @throwable T_1;
            case 'l':
                if(!isset($this->buffers[2]))
                    break;

                $this->_current = $this->_current->couldThrow(
                    $this->buffers[2]
                );
                $this->_current = $this->_current->_and;
                unset($this->buffers[2]);
              break;
        }
    }

    /**
     * Compute source code before compiling it.
     *
     * @access  protected
     * @param   string  &$in    Source code.
     * @return  void
     */
    protected function pre ( &$in ) {

        $this->_praspel = new Hoa_Test_Praspel();

        $search  = array('&&',  '∧',   '||', '∨' );
        $replace = array('and', 'and', 'or', 'or');
        $in      = str_replace($search, $replace, $in);

        return;
    }

    /**
     * Get the Praspel's objet model root.
     *
     * @access  public
     * @return  Hoa_Test_Praspel
     */
    public function getRoot ( ) {

        return $this->_praspel;
    }
}
