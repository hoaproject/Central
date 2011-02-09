<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Test\Exception
 */
-> import('Test.Exception')

/**
 * \Hoa\Test\Orchestrate
 */
-> import('Test.Orchestrate')

/**
 * \Hoa\Test\Praspel
 */
-> import('Test.Praspel.~')

/**
 * \Hoa\Test\Sampler\Random
 */
-> import('Test.Sampler.Random')

/**
 * \Hoa\Test\Selector\Random
 */
-> import('Test.Selector.Random')

/**
 * \Hoa\Realdom
 */
-> import('Realdom.~');

}

namespace Hoa\Test {

/**
 * Class \Hoa\Test.
 *
 * Make tests.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Test implements \Hoa\Core\Parameterizable, \Hoa\Core\Event\Source {

    /**
     * Singleton.
     *
     * @var \Hoa\Test object
     */
    private static $_instance = null;

    /**
     * Parameters of \Hoa\Test.
     *
     * @var \Hoa\Core\Parameter object
     */
    protected $_parameters    = null;



    /**
     * Singleton, and set parameters.
     *
     * @access  private
     * @param   array    $parameters    Parameters.
     * @return  void
     */
    public function __construct ( Array $parameters = array() ) {

        $this->_parameters = new \Hoa\Core\Parameter(
            $this,
            array(),
            array(
                'convict'      => null,

                'root'         => 'hoa://Data/Variable/Test/',

                'repository'   => '(:%root:)Repository/',
                'revision'     => '(:_YmdHis:)/',

                'incubator'    => '(:%repository:)(:%revision:)Incubator/',
                'instrumented' => '(:%repository:)(:%revision:)Instrumented/',

                'dictionary'   => '(:%root:)Dictionary/',
                'maxtry'       => 64
            )
        );

        $this->setParameters($parameters);
        \Hoa\Core\Event::register(
            'hoa://Event/Test/Sample:open-iteration', 
            $this
        );
        \Hoa\Core\Event::register(
            'hoa://Event/Test/Sample:close-iteration', 
            $this
        );

        return;
    }

    /**
     * Initialize tests, i.e. create a new revision in the repository of test:
     * incubator + instrumented.
     *
     * @access  public
     * @param   string  $directory    Directory of the SUT (System Under Test).
     * @return  void
     */
    public function initialize ( $directory ) {

        $this->setParameter('convict', $directory);
        $orchestrate = new Orchestrate($this->_parameters);
        $this->_parameters->shareWith(
            $this,
            $orchestrate,
            \Hoa\Core\Parameter::PERMISSION_READ
        );
        $orchestrate->compute();

        return;
    }

    /**
     * Use sampler to call a method.
     *
     * @access  public
     * @param   string  $contractId    Contract ID.
     * @param   string  $class         Class to call.
     * @param   string  $method        Method to call.
     * @return  void
     * @throw   \Hoa\Test\Exception
     */
    public function sample ( $contractId, $class, $method ) {

        if(!class_exists($class))
            throw new Exception(
                'Class %s does not exist and cannot be tested.', 0, $class);

        \Hoa\Realdom::setSampler(new Sampler\Random());

        $cut       = new $class();
        $hop       = '__hoa_' . $method . '_contract';
        $cut->$hop();
        $praspel   = Praspel::getInstance();
        $contract  = $praspel->getContract($contractId);
        $i         = 0;

        if(false === $contract->clauseExists('requires')) {

            \Hoa\Core\Event::notify(
                'hoa://Event/Test/Sample:open-iteration',
                $this,
                new \Hoa\Core\Event\Bucket(array('iteration' => $i))
            );

            // Prevent if an exception is thrown from the called method.
            try {

                call_user_func_array(
                    array($cut, '__hoa_magicCaller'),
                    array(0 => $method)
                );
            }
            catch ( Exception $e ) { }

            \Hoa\Core\Event::notify(
                'hoa://Event/Test/Sample:close-iteration',
                $this,
                new \Hoa\Core\Event\Bucket(array(
                    'iteration' => $i,
                    'contract'  => $contract
                ))
            );
            $contract->reset();

            return;
        }

        $variables = $contract->getClause('requires')->getVariables();
        $selector  = new Selector\Random($variables);

        foreach($selector as $e => $selection) {

            \Hoa\Core\Event::notify(
                'hoa://Event/Test/Sample:open-iteration',
                $this,
                new \Hoa\Core\Event\Bucket(array('iteration' => $i))
            );

            $parameters = array(0 => $method);

            foreach($variables as $variable)
                $parameters[] = $variable->selectDomain($selection)
                                         ->sample();

            // Prevent if an exception is thrown from the called method.
            try {

                call_user_func_array(
                    array($cut, '__hoa_magicCaller'),
                    $parameters
                );
            }
            catch ( Exception $e ) { }

            \Hoa\Core\Event::notify(
                'hoa://Event/Test/Sample:close-iteration',
                $this,
                new \Hoa\Core\Event\Bucket(array(
                    'iteration' => $i,
                    'contract'  => $contract
                ))
            );
            $contract->reset();
            ++$i;
        }

        return;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in      Parameters to set.
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
     * @param   string  $key      Key.
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
}

}
