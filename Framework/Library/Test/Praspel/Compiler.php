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
 * \Hoa\Test\Praspel\Exception
 */
-> import('Test.Praspel.Exception')

/**
 * \Hoa\Test\Praspel\Contract
 */
-> import('Test.Praspel.Contract')

/**
 * \Hoa\Compiler\Ll1
 */
-> import('Compiler.Ll1');

}

namespace Hoa\Test\Praspel {

/**
 * Class \Hoa\Test\Praspel\Compiler.
 *
 * The Praspel compiler.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Compiler extends \Hoa\Compiler\Ll1 {

    /**
     * The Praspel's object model root.
     *
     * @var \Hoa\Test\Praspel object
     */
    protected $_praspel = null;

    /**
     * The current node in the Praspel's object model.
     *
     * @var \Hoa\Test\Praspel object
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
                '#\s+',                // white spaces
                '#//.*',               // inline comment
                '#/\*(.|\n)*\*/'       // block comment
            ),

            // Tokens.
            array(
                // 1. Clauses.
                array(
                    '#@requires',       // r
                    '#@ensures',        // e
                    '#@throwable',      // t
                    '#@invariant',      // i
                    '#@predicate',      // p
                    ';'
                ),

                // 2. Expressions.
                array(
                    '#and',             // &
                    ':',                // :
                    '#domainof'         // do
                ),

                // 3. List.
                array(
                    '#\w+',             // id
                    ','                 // ,
                ),

                // 4. Extend.
                array(

                ),

                // 5. Variable.
                array(
                    '#\w+',             // id
                    '#\\\result',       // \r
                    '#\\\old\(',        // \o
                    ')'                 // )
                ),

                // 6. Domains.
                array(
                    '#or',              // or
                    '#\w+',             // id
                    '(',                // (
                    ')'                 // )
                ),

                // 7. Arguments.
                array(
                    ',',                // ,
                    '#([+-]?0[xX][0-9a-fA-F]+)',                 // 0x
                    '#([+-]?0[0-7]+)',                           // 07
                    '#([+-]?([0-9]*\.[0-9]+)|([0-9]+\.[0-9]*))', // 0.
                    '#([+-]?[1-9][0-9]*|0)',                     // 09
                    '#true',            // t
                    '#false',           // f
                    '(',                // (
                    ')',                // )
                    '[',                // [
                    '#\'.*?(?<!\\\)\'', // s
                    '#\w+'              // id
                ),

                // 8. Array.
                array(
                    ']'                 // ]
                ),

                // 9. Pairs.
                array(
                    ',',                // ,
                    '#from',            // fr
                    '#to'               // to
                )
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
                    'DM', // domain
                    'DO'  // domainof
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

                // 6. Domains.
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
                )
            ),

            // Terminal.
            array(
                // 1. Clauses.
                array('GO'),

                // 2. Expressions.
                array('GO', 'DM', 'DO'),

                // 3. List.
                array('GO', 'CO'),

                // 4. Extend.
                array(),

                // 5. Variable.
                array('OK'),

                // 6. Domains
                array('GO', 'OK'),

                // 7. Arguments.
                array('GO', '[]', 'OK'),

                // 8. Array.
                array('OK'),

                // 9. Pairs.
                array('GO', 'TO', 'OK')
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
                    /*               &     :    do
                    /* __ */ array( __ ,  __ ,  __ ),
                    /* GO */ array( __ , 'DM', 'DO'),
                    /* DM */ array('GO',  __ ,  __ ),
                    /* DO */ array('GO',  __ ,  __ )
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

                // 6. Domains.
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
                    /*               ,    0x    07    0.    09     t     f     (     )     [     s    id
                    /* __ */ array( __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ),
                    /* GO */ array( __ , 'OK', 'OK', 'OK', 'OK', 'OK', 'OK',  __ ,  __ , '[]', 'OK', 'ID'),
                    /* ID */ array( __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ , 'AR',  __ ,  __ ,  __ ,  __ ),
                    /* AR */ array( __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ , 'OK',  __ ,  __ ,  __ ),
                    /* [] */ array('GO',  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ),
                    /* OK */ array('GO',  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ ,  __ )
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
                )
            ),

            // Actions.
            array(
                // 1. Clauses.
                array(
                    /*              r    e    t    i    p    ;
                    /* __ */ array( 0 ,  0 ,  0 ,  0 ,  0 ,  0 ),
                    /* GO */ array('r', 'e', 't', 'i',  0 ,  0 ),
                    /* EX */ array( 2 ,  2 ,  0 ,  2 ,  0 , 'D'),
                    /* LI */ array( 0 ,  0 ,  3 ,  0 ,  0 , 'l'),
                    /* IN */ array( 0 ,  0 ,  0 ,  0 ,  0 ,  0 )
                ),

                // 2. Expressions.
                array(
                    /*              &    :   do
                    /* __ */ array( 0 ,  0 ,  0 ),
                    /* GO */ array( 5 , ':', 'd'),
                    /* DM */ array('&',  6 ,  0 ),
                    /* DO */ array('D',  0 ,  5 )
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

                // 6. Domains.
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
                    /*              ,   0x   07   0.   09    t    f    (    )    [    s   id
                    /* __ */ array( 0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ),
                    /* GO */ array( 0 , 'x', '7', '.', '9', 'T', 'F',  0 ,  0 , '[', 's', -3 ),
                    /* ID */ array( 0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 , 'z',  0 ,  0 ,  0 ,  0 ),
                    /* AR */ array( 0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  7 , 'Y',  0 ,  0 ,  0 ),
                    /* [] */ array( 0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  8 ,  0 ,  0 ),
                    /* OK */ array('c',  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 )
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
                    /* __ */ array( 0 ,  0,   0 ),
                    /* GO */ array( 0 ,  0,   0 ),
                    /* FR */ array( 0 ,  6, 'to'),
                    /* TO */ array(',',  0,   6 ),
                    /* OK */ array( 9 ,  0,   0 )
                )
            ),

            // Names.
            array(
                'Clauses',
                'Expressions',
                'List',
                'Extend',
                'Variable',
                'Domains',
                'Arguments',
                'Array',
                'Pairs'
            )
        );

        parent::disableCache();

        return;
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

            // @invariant
            case 'i':
                $this->_current = $this->_praspel->clause('invariant');
              break;

            // variable:
            // variable domainof
            case ':':
            case 'd':
                $this->_current = $this->_current->variable(
                    $this->buffers[0]
                );
                unset($this->buffers[0]);
              break;

            // variable: domain(
            case 'y':
                $this->_current = $this->_current->belongsTo(
                    $this->buffers[1]
                );
                unset($this->buffers[1]);
              break;

            // variable: domain(…, domain(
            case 'z':
                $this->_current = $this->_current->withDomain(
                    $this->buffers[1]
                );
                unset($this->buffers[1]);
              break;

            // variable: domain(…)
            case 'Y':
                if(isset($this->buffers[3])) {

                    $this->_current = $this->_current->with(
                        $this->buffers[3]
                    );
                    unset($this->buffers[3]);
                }

                $this->_current = $this->_current->_ok();
              break;

            // variable: domain([
            case '[':
                $this->_current = $this->_current->withArray()->from();
              break;

            // variable: domain([…,
            case ',':
                $this->_current = $this->_current->from();
              break;

            // variable: domain([… to
            case 'to':
                $this->_current = $this->_current->to();
              break;

            // variable: domain([…]
            case ']':
                $this->_current = $this->_current->end();
              break;

            // variable: domain(…,
            case 'c':
                if(!isset($this->buffers[3]))
                    break;

                $this->_current = $this->_current->with(
                    $this->buffers[3]
                )->_comma;
                unset($this->buffers[3]);
              break;

            // variable: domain() or
            case '|':
                $this->_current = $this->_current->_or;
              break;

            // variable: domain() and
            case '&':
                $this->_current = $this->_current->_and;
              break;

            // variable domainof variable and
            // variable domainof variable;
            case 'D':
                if(!isset($this->buffers[0]))
                    break;

                $this->_current = $this->_current->hasTheSameDomainAs(
                    $this->buffers[0]
                )->_and;
                unset($this->buffers[0]);
              break;

            // @throwable T_1,
            // @throwable T_1;
            case 'l':
                if(!isset($this->buffers[2]))
                    break;

                $this->_current = $this->_current->couldThrow(
                    $this->buffers[2]
                )->_comma;
                unset($this->buffers[2]);
              break;

            // Number: hexadecimal.
            case 'x':
                $this->buffers[3] = hexdec(substr($this->buffers[-1], 2));
              break;

            // Number: octal.
            case '7':
                $this->buffers[3] = intval($this->buffers[-1], 8);
              break;

            // Number: float.
            case '.':
                $this->buffers[3] = floatval($this->buffers[-1]);
              break;

            // Number: decimal.
            case '9':
                $this->buffers[3] = intval($this->buffers[-1], 10);
              break;

            // Boolean: true.
            case 'T':
                $this->buffers[3] = true;
              break;

            // Boolean: false.
            case 'T':
                $this->buffers[3] = false;
              break;

            // String.
            case 's':
                $this->buffers[3] = str_replace(
                    "\'",
                    "'",
                    substr($this->buffers[-1], 1, -1)
                );
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

        $this->_praspel = new Contract();

        $search  = array('&&',  '∧',   '||', '∨' );
        $replace = array('and', 'and', 'or', 'or');
        $in      = str_replace($search, $replace, $in);

        return;
    }

    /**
     * Get the result of the compiling.
     *
     * @access  public
     * @return  \Hoa\Test\Praspel
     */
    public function getResult ( ) {

        return $this->getRoot();
    }

    /**
     * Get the Praspel's objet model root.
     *
     * @access  public
     * @return  \Hoa\Test\Praspel
     */
    public function getRoot ( ) {

        return $this->_praspel;
    }
}

}
