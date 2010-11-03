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
 * @subpackage  Hoa_Test_Orchestrate
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_File_Finder
 */
import('File.Finder');

/**
 * Hoa_File_Finder
 */
import('File.Finder');

/**
 * Hoa_File_Directory
 */
import('File.Directory');

/**
 * Hoa_Reflection_RClass
 */
import('Reflection.RClass');

/**
 * Hoa_Reflection_Fragment_RMethod
 */
import('Reflection.Fragment.RMethod');

/**
 * Hoa_Reflection_Fragment_RParameter
 */
import('Reflection.Fragment.RParameter');

/**
 * Hoa_Reflection_Visitor_Prettyprinter
 */
import('Reflection.Visitor.Prettyprinter');

/**
 * Class Hoa_Test_Orchestrate.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Orchestrate
 */

class Hoa_Test_Orchestrate implements Hoa_Core_Parameterizable {

    /**
     * Parameters of Hoa_Test_Orchestrate.
     *
     * @var Hoa_Core_Parameter object
     */
    protected $_parameters    = null;

    /**
     * Pretty-printer.
     *
     * @var Hoa_Reflection_Visitor_Prettyprinter object
     */
    protected $_prettyPrinter = null;

    private $_magicSetter     = null;
    private $_magicGetter     = null;
    private $_magicCaller     = null;



    /**
     * Construct the conductor.
     *
     * @access  public
     * @param   Hoa_Core_Parameter  $parameters    Parameters of Hoa_Test.
     * @return  void
     */
    public function __construct ( Hoa_Core_Parameter $parameters ) {

        $this->_parameters = $parameters;
        $this->setPrettyPrinter(new Hoa_Reflection_Visitor_Prettyprinter());

        return;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in      Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setParameters ( Array $in ) {

        return $this->_parameters->setParameters($this, $in);
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Exception
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
     * @throw   Hoa_Exception
     */
    public function setParameter ( $key, $value ) {

        return $this->_parameters->setParameter($this, $key, $value);
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @return  mixed
     * @throw   Hoa_Exception
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
     * @throw   Hoa_Exception
     */
    public function getFormattedParameter ( $key ) {

        return $this->_parameters->getFormattedParameter($this, $key);
    }

    public function setPrettyPrinter ( $printer ) {

        $old                  = $this->_prettyPrinter;
        $this->_prettyPrinter = $printer;

        return $old;
    }

    public function getPrettyPrinter ( ) {

        return $this->_prettyPrinter;
    }

    public function compute ( ) {

        $finder = new Hoa_File_Finder(
            $this->getParameter('convict'),
            Hoa_File_Finder::LIST_VISIBLE
        );

        $this->incubator($finder);
        $this->instrumentation($finder);
        $this->sampler($finder);

        return;
    }

    protected function incubator ( Hoa_File_Finder $finder ) {

        $incubator = $this->getFormattedParameter('incubator');

        Hoa_File_Directory::create($incubator);

        foreach($finder as $i => $file)
            $file->define()->copy($incubator . $file->getBasename());

        return;
    }

    protected function instrumentation ( Hoa_File_Finder $finder ) {

        $this->_magicSetter = new Hoa_Reflection_Fragment_RMethod(
            '__hoa_magicSetter'
        );
        $this->_magicSetter->setCommentContent('Magic setter.');
        $this->_magicSetter->importFragment(
            new Hoa_Reflection_Fragment_RParameter('attribute')
        );
        $this->_magicSetter->importFragment(
            new Hoa_Reflection_Fragment_RParameter('value')
        );
        $this->_magicSetter->setBody(
            '        $old              = $this->$attribute;' . "\n" .
            '        $this->$attribute = $value;' . "\n\n" .
            '        return $old;'
        );

        $this->_magicGetter = new Hoa_Reflection_Fragment_RMethod(
            '__hoa_magicGetter'
        );
        $this->_magicGetter->setCommentContent('Magic getter.');
        $this->_magicGetter->importFragment(
            new Hoa_Reflection_Fragment_RParameter('attribute')
        );
        $this->_magicGetter->setBody(
            '        return $this->$attribute;'
        );

        $this->_magicCaller = new Hoa_Reflection_Fragment_RMethod(
            '__hoa_magicCaller'
        );
        $this->_magicCaller->setCommentContent('Magic caller.');
        $this->_magicCaller->importFragment(
            new Hoa_Reflection_Fragment_RParameter('method')
        );
        $this->_magicCaller->setBody(
            '        $arguments = func_get_args();' . "\n" .
            '        array_shift($arguments);' . "\n\n" .
            '        return call_user_func_array(' . "\n" .
            '            array($this, $method),' . "\n" .
            '            $arguments' . "\n" .
            '        );'
        );

        $from = $this->getFormattedParameter('convict');
        $to   = $this->getFormattedParameter('instrumented');
        Hoa_File_Directory::create($to);

        return $this->_instrumentation($finder, $from, $to);
    }

    private function _instrumentation ( Hoa_File_Finder $finder, $from, $to ) {

        foreach($finder as $i => $file) {

            $basename = $file->getBasename();
            $path     = $to . DS . $basename;

            if(true === $file->isDirectory()) {

                Hoa_File_Directory::create($path);

                return $this->_instrumentation(
                    new Hoa_File_Finder(
                        $file->getStreamName(),
                        Hoa_File_Finder::LIST_VISIBLE
                    ),
                    $from . DS . $basename,
                    $path
                );
            }

            $handle  = $file->define($path);
            $classes = get_declared_classes();

            require_once $from . DS . $basename;

            $classes = array_diff(get_declared_classes(), $classes);
            $handle->truncate(0);

            foreach($classes as $classname) {

                $class = new Hoa_Reflection_RClass($classname);

                foreach($class->getMethods() as $method) {

                    $name = $method->getName();
                    $method->setName('__hoa_' . $name . '_body');

                    $main = new Hoa_Reflection_Fragment_RMethod($name);
                    $main->setCommentContent('The new ' . $name . ' method.');
                    $main->setBody(
                        '        $this->__hoa_' . $name . '_pre();' . "\n" .
                        '        $return = $this->__hoa_' . $name . '_body();' . "\n" .
                        '        $this->__hoa_' . $name . '_post();' . "\n\n" .
                        '        return $return;'
                    );
                    $main->setVisibility($method->getVisibility());
                    $class->importFragment($main);

                    $method->setVisibility(_protected);

                    $pre = new Hoa_Reflection_Fragment_RMethod(
                        '__hoa_' . $name . '_pre'
                    );
                    $pre->setCommentContent(
                        'Pre-condition of the ' . $name . ' method.'
                    );
                    $pre->setBody('        // pre-condition.');
                    $pre->setVisibility(_protected);
                    $class->importFragment($pre);

                    $post = new Hoa_Reflection_Fragment_RMethod(
                        '__hoa_' . $name . '_post'
                    );
                    $post->setCommentContent(
                        'Post-condition of the ' . $name . ' method.'
                    );
                    $post->setBody('        // post-condition.');
                    $post->setVisibility(_protected);
                    $class->importFragment($post);
                }

                $class->importFragment($this->_magicSetter);
                $class->importFragment($this->_magicGetter);
                $class->importFragment($this->_magicCaller);
                $handle->writeAll('<?php' . "\n\n");
                $handle->writeAll($class->accept($this->getPrettyPrinter()));
            }

            $handle->close();
            unset($handle);
        }

        return;
    }

    protected function sampler ( Hoa_File_Finder $finder ) {

        /*
        $sampler = $this->getFormattedParameter('sampler');

        Hoa_File_Directory::create($sampler);

        foreach($finder as $i => $file)
            $file->define()->copy($sampler . $file->getBasename());
        */

        return;
    }
}
