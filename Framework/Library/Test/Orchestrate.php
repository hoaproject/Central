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
 * \Hoa\File\Finder
 */
-> import('File.Finder')

/**
 * \Hoa\File\Directory
 */
-> import('File.Directory')

/**
 * \Hoa\Reflection\RClass
 */
-> import('Reflection.RClass')

/**
 * \Hoa\Reflection\Fragment\RMethod
 */
-> import('Reflection.Fragment.RMethod')

/**
 * \Hoa\Reflection\Fragment\RParameter
 */
-> import('Reflection.Fragment.RParameter')

/**
 * \Hoa\Reflection\Visitor\Prettyprinter
 */
-> import('Reflection.Visitor.Prettyprinter')

/**
 * \Hoa\Test\Praspel\Compiler
 */
-> import('Test.Praspel.Compiler')

/**
 * \Hoa\Test\Praspel\Visitor\Php
 */
-> import('Test.Praspel.Visitor.Php');

}

namespace Hoa\Test {

/**
 * Class \Hoa\Test\Orchestrate.
 *
 * Orchestrate the repository and the code.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Orchestrate implements \Hoa\Core\Parameterizable {

    /**
     * Parameters of \Hoa\Test\Orchestrate.
     *
     * @var \Hoa\Core\Parameter object
     */
    protected $_parameters    = null;

    /**
     * Praspel' compiler.
     *
     * @var \Hoa\Test\Praspel\Compiler object
     */
    protected $_compiler      = null;

    /**
     * Pretty-printer.
     *
     * @var \Hoa\Reflection\Visitor\Prettyprinter object
     */
    protected $_prettyPrinter = null;

    /**
     * Temporize magic setter.
     *
     * @var \Hoa\Reflection\Fragment\RMethod object
     */
    private $_magicSetter     = null;

    /**
     * Temporize magic getter.
     *
     * @var \Hoa\Reflection\Fragment\RMethod object
     */
    private $_magicGetter     = null;

    /**
     * Temporize magic caller.
     *
     * @var \Hoa\Reflection\Fragment\RMethod object
     */
    private $_magicCaller     = null;



    /**
     * Construct the conductor.
     *
     * @access  public
     * @param   \Hoa\Core\Parameter  $parameters    Parameters of \Hoa\Test.
     * @return  void
     */
    public function __construct ( \Hoa\Core\Parameter $parameters ) {

        $this->_parameters = $parameters;
        $this->_compiler   = new Praspel\Compiler();
        $this->setPrettyPrinter(new \Hoa\Reflection\Visitor\Prettyprinter());

        return;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in    Parameters to set.
     * @return  void
     * @throw   \Hoa\Core\Exception
     */
    public function setParameters ( Array $in ) {

        return $this->_parameters->setParameters($this, $in);
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Core\Exception
     */
    public function getParameters ( ) {

        return $this->_parameters->getParameters($this);
    }

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function setParameter ( $key, $value ) {

        return $this->_parameters->setParameter($this, $key, $value);
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function getParameter ( $key ) {

        return $this->_parameters->getParameter($this, $key);
    }

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function getFormattedParameter ( $key ) {

        return $this->_parameters->getFormattedParameter($this, $key);
    }

    /**
     * Set pretty printer.
     *
     * @access  public
     * @param   object  $printer    Pretty-printer.
     * @return  object
     */
    public function setPrettyPrinter ( $printer ) {

        $old                  = $this->_prettyPrinter;
        $this->_prettyPrinter = $printer;

        return $old;
    }

    /**
     * Get pretty printer.
     *
     * @access  public
     * @return  object
     */
    public function getPrettyPrinter ( ) {

        return $this->_prettyPrinter;
    }

    /**
     * Compute, i.e. start to orchestrate!
     *
     * @access  public
     * @return  void
     */
    public function compute ( ) {

        $finder = new \Hoa\File\Finder(
            $this->getParameter('convict'),
            \Hoa\File\Finder::LIST_VISIBLE
        );

        $this->incubator($finder);
        $this->instrumentation($finder);

        return;
    }

    /**
     * Prepare the incubator.
     *
     * @access  protected
     * @param   \Hoa\File\Finder  $finder    Finder.
     * @return  void
     */
    protected function incubator ( \Hoa\File\Finder $finder ) {

        $incubator = $this->getFormattedParameter('incubator');

        \Hoa\File\Directory::create($incubator);

        foreach($finder as $i => $file)
            $file->define()->copy($incubator . $file->getBasename());

        return;
    }

    /**
     * Prepare the instrumentation.
     *
     * @access  protected
     * @param   \Hoa\File\Finder  $finder    Finder.
     * @return  void
     */
    protected function instrumentation ( \Hoa\File\Finder $finder ) {

        $this->_magicSetter = new \Hoa\Reflection\Fragment\RMethod(
            '__hoa_magicSetter'
        );
        $this->_magicSetter->setCommentContent('Magic setter.');
        $this->_magicSetter->importFragment(
            new \Hoa\Reflection\Fragment\RParameter('attribute')
        );
        $this->_magicSetter->importFragment(
            new \Hoa\Reflection\Fragment\RParameter('value')
        );
        $this->_magicSetter->setBody(
            '        $old              = $this->$attribute;' . "\n" .
            '        $this->$attribute = $value;' . "\n\n" .
            '        return $old;'
        );

        $this->_magicGetter = new \Hoa\Reflection\Fragment\RMethod(
            '__hoa_magicGetter'
        );
        $this->_magicGetter->setCommentContent('Magic getter.');
        $this->_magicGetter->importFragment(
            new \Hoa\Reflection\Fragment\RParameter('attribute')
        );
        $this->_magicGetter->setBody(
            '        return $this->$attribute;'
        );

        $this->_magicCaller = new \Hoa\Reflection\Fragment\RMethod(
            '__hoa_magicCaller'
        );
        $this->_magicCaller->setCommentContent('Magic caller.');
        $this->_magicCaller->importFragment(
            new \Hoa\Reflection\Fragment\RParameter('method')
        );
        $this->_magicCaller->setBody(
            '        $arguments = func_get_args();' . "\n" .
            '        array_shift($arguments);' . "\n\n" .
            '        return call_user_func_array(' . "\n" .
            '            array($this, $method),' . "\n" .
            '            $arguments' . "\n" .
            '        );'
        );

        $from      = $this->getFormattedParameter('convict');
        $to        = $this->getFormattedParameter('instrumented');
        $incubator = $this->getFormattedParameter('incubator');
        \Hoa\File\Directory::create($to);

        return $this->_instrumentation($finder, $from, $to, $incubator);
    }

    /**
     * Recursive instrumentation.
     *
     * @access  private
     * @param   \Hoa\File\Finder  $finder       Finder.
     * @param   string           $from         From.
     * @param   string           $to           To.
     * @param   string           $incubator    Incubator path.
     * @return  void
     */
    private function _instrumentation ( \Hoa\File\Finder $finder, $from, $to,
                                        $incubator ) {

        foreach($finder as $i => $file) {

            $basename = $file->getBasename();
            $path     = $to . DS . $basename;

            if(true === $file->isDirectory()) {

                \Hoa\File\Directory::create($path);

                return $this->_instrumentation(
                    new \Hoa\File\Finder(
                        $file->getStreamName(),
                        \Hoa\File\Finder::LIST_VISIBLE
                    ),
                    $from . DS . $basename,
                    $path,
                    $incubator . DS . $basename
                );
            }

            $handle  = $file->define($path);
            $lines   = explode("\n", $file->define()->readAll());
            $classes = get_declared_classes();

            require_once $from . DS . $basename;

            $classes = array_diff(get_declared_classes(), $classes);

            foreach($classes as $classname) {

                $class = new \Hoa\Reflection\RClass($classname);

                // Invariants.
                $inv   = new \Hoa\Reflection\Fragment\RMethod('__hoa_invariants');
                $invG  = new \Hoa\Reflection\Fragment\RMethod('__hoa_getInvariantsValues');
                $invBd = 'false and' . "\n";
                $invCc = null;
                $invT  = '    ';

                if(false !== $class->getParentClass()) {

                    $invBg = 'return array_merge(' . "\n" .
                            '    parent::__hoa_getInvariantsValues(),' . "\n" .
                            '    array(' . "\n";
                    $invT  = '        ';
                }
                else
                    $invBg = 'return array(' . "\n";

                foreach($class->getProperties() as $property) {

                    $invCc .= $property->getCommentContent() . "\n";
                    $invBg .= $invT . '\'' . $property->getName() . '\' => ' .
                              (false === $property->isStatic()
                                   ? '$this->'
                                   : 'self::$') .
                              $property->getName() . ',' . "\n";
                }

                if(false !== $class->getParentClass())
                    $invBg .= '    )' . "\n";

                $invBg .= ');';

                $inv->importFragment(
                    new \Hoa\Reflection\Fragment\RParameter('contract')
                );
                $inv->setCommentContent('Invariants.');
                $this->_compiler->compile($invCc);
                $inv->setBody(
                    '        ' .
                    str_replace(
                        "\n",
                        "\n" . '        ',
                        $invBd . $this->_compiler->getResult()->accept(
                            new \Hoa\Test\Praspel\Visitor\Php()
                        )
                    ) .
                    (false !== $class->getParentClass()
                        ? "\n" . '        parent::__hoa_invariants($contract);' . "\n"
                        : '') . "\n" .
                    '        return;'
                );
                $inv->setVisibility(_protected);

                $invG->setCommentContent('Get all invariants values.');
                $invG->setBody(
                    '        ' .
                    str_replace("\n", "\n" . '        ', $invBg)
                );
                $invG->setVisibility(_protected);

                foreach($class->getMethods() as $method) {

                    $name         = $method->getName();
                    $id           = $classname . '::' . $name;
                    $mainName     = $name;
                    $originalName = '__hoa_' . $name . '_body';
                    $contractName = '__hoa_' . $name . '_contract';
                    $preName      = '__hoa_' . $name . '_pre';
                    $postName     = '__hoa_' . $name . '_post';
                    $excepName    = '__hoa_' . $name . '_exception';

                    $main  = new \Hoa\Reflection\Fragment\RMethod($mainName);
                    $cont  = new \Hoa\Reflection\Fragment\RMethod($contractName);
                    $pre   = new \Hoa\Reflection\Fragment\RMethod($preName);
                    $post  = new \Hoa\Reflection\Fragment\RMethod($postName);
                    $excep = new \Hoa\Reflection\Fragment\RMethod($excepName);


                    // Original.
                    $method->setName($originalName);
                    $this->_compiler->compile($method->getCommentContent());


                    // Contract.
                    $contract = $this->_compiler->getResult()->accept(
                        new \Hoa\Test\Praspel\Visitor\Php()
                    );
                    $contract = str_replace("\n", "\n" . '        ', $contract);

                    $cont->setCommentContent('Create contract of the ' . $name . ' method');
                    $cont->setBody(
                        '        $praspel = \Hoa\Test\Praspel::getInstance();' . "\n\n" .
                        '        if(true === $praspel->contractExists(\'' . $id . '\'))' . "\n" .
                        '            return;' . "\n\n" .
                        '        $class     = \'' . $classname . '\';' . "\n" .
                        '        $method    = \'' . $name . '\';' . "\n" .
                        '        $file      = \'' . $incubator . $basename . '\';' . "\n" .
                        '        $startLine = '   . $method->getStartLine() . ';' . "\n" .
                        '        $endLine   = '   . $method->getEndLine() . ';' . "\n" .
                        '        ' . $contract . "\n" .
                        '        $this->__hoa_invariants($contract); '. "\n" .
                        '        \Hoa\Test\Praspel::getInstance()->addContract($contract);' . "\n\n" .
                        '        return;'
                    );
                    $cont->setVisibility(_public);
                    $class->importFragment($cont);


                    // Parameters.
                    $post->importFragment(
                        new \Hoa\Reflection\Fragment\RParameter('result')
                    );
                    $p  = null;
                    $pp = '$result';

                    foreach($method->getParameters() as $parameter) {

                        if(null !== $p)
                            $p  .= ', ';

                        $p  .= '$' . $parameter->getName();
                        $pp .= ', $' . $parameter->getName();
                        $main->importFragment($parameter);
                        $pre->importFragment($parameter);
                        $post->importFragment($parameter);
                    }

                    $excep->importFragment(
                        new \Hoa\Reflection\Fragment\RParameter('exception')
                    );


                    // Fake original.
                    $main->setCommentContent(
                        'Test method ' . $classname . '::' . $name . '()' . "\n" .
                        'in file ' . $incubator . $basename . "\n" .
                        'from line ' . $method->getStartLine() .
                        ' to ' . $method->getEndLine()
                    );
                    $main->setBody(
                        '        $this->__hoa_' . $name . '_contract();' . "\n" .
                        '        $this->__hoa_' . $name . '_pre(' . $p . ');' . "\n\n" .
                        '        try {' . "\n\n" .
                        '            $result = $this->__hoa_' . $name . '_body(' . $p . ');' . "\n" .
                        '        }' . "\n" .
                        '        catch ( Exception $e ) {' . "\n\n" .
                        '            $this->__hoa_' . $name . '_exception($e);' . "\n\n" .
                        '            throw $e;' . "\n" .
                        '        }' . "\n\n" .
                        '        $this->__hoa_' . $name . '_post(' . $pp . ');' . "\n\n" .
                        '        return $result;'
                    );
                    $main->setVisibility($method->getVisibility());
                    $class->importFragment($main);
                    $method->setVisibility(_protected);


                    // Pre-condition.
                    $pre->setCommentContent(
                        'Pre-condition of the ' . $name . ' method.'
                    );
                    $pre->setBody(
                        '        $praspel  = \Hoa\Test\Praspel::getInstance();' . "\n" .
                        '        $contract = $praspel->getContract(\'' . $id . '\');' . "\n\n" .
                        '        return    $contract->verifyInvariants(' . "\n" .
                        '                      $this->__hoa_getInvariantsValues()' . "\n" .
                        '                  )' . "\n" .
                        '               && $contract->verifyPreCondition(' . $p . ');' 
                    );
                    $pre->setVisibility(_public);
                    $class->importFragment($pre);


                    // Post-condition.
                    $post->setCommentContent(
                        'Post-condition of the ' . $name . ' method.'
                    );
                    $post->setBody(
                        '        $praspel  = \Hoa\Test\Praspel::getInstance();' . "\n" .
                        '        $contract = $praspel->getContract(\'' . $id . '\');' . "\n\n" .
                        '        return    $contract->verifyPostCondition(' . $pp . ')' . "\n" .
                        '               && $contract->verifyInvariants(' . "\n" .
                        '                      $this->__hoa_getInvariantsValues()' . "\n" .
                        '                  );'
                    );
                    $post->setVisibility(_public);
                    $class->importFragment($post);


                    // Exception.
                    $excep->setCommentContent(
                        'Exceptional condition of the ' . $name . ' method.'
                    );
                    $excep->setBody(
                        '        $praspel  = \Hoa\Test\Praspel::getInstance();' . "\n" .
                        '        $contract = $praspel->getContract(\'' . $id . '\');' . "\n\n" .
                        '        return    $contract->verifyException($exception)' . "\n" .
                        '               && $contract->verifyInvariants(' . "\n" .
                        '                      $this->__hoa_getInvariantsValues()' . "\n" .
                        '                  );'
                    );
                    $excep->setVisibility(_public);
                    $class->importFragment($excep);
                }

                $class->importFragment($inv);
                $class->importFragment($invG);
                $class->importFragment($this->_magicSetter);
                $class->importFragment($this->_magicGetter);
                $class->importFragment($this->_magicCaller);

                $startLine = $class->getStartLine() - 1;
                $endLine   = $class->getEndLine() - 1;
                $line      = $lines[$endLine];

                for($end = strlen($line) - 1; '}' != $line[$end]; --$end);

                $line[$end]      = ' ';
                $lines[$endLine] = $line;

                array_splice(
                    $lines,
                    $startLine,
                    $endLine - $startLine,
                    $class->accept($this->getPrettyPrinter())
                );
            }

            $handle->writeAll(implode("\n", $lines));
            $handle->close();
            unset($handle);
        }

        return;
    }
}

}
