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
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Router_Rewrite
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Controller_Router_Interface
 */
import('Controller.Router.Interface');

/**
 * Class Hoa_Controller_Router_Rewrite.
 *
 * The rewrite router ameliorates the using of URLs rewriting.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Router_Rewrite
 */

class Hoa_Controller_Router_Rewrite implements Hoa_Controller_Router_Interface {

    /**
     * All router parameters.
     *
     * @var Hoa_Controller_Router_Rewrite array
     */
    protected $_parameters = array();

    /**
     * All rules
     *
     * @var Hoa_Controller_Router_Rewrite array
     */
    protected $_rules      = null;

    /**
     * The URL.
     *
     * @var Hoa_Controller_Router_Rewrite string
     */
    protected $_url        = null;

    /**
     * The URL base.
     *
     * @var Hoa_Controller_Router_Rewrite string
     */
    protected $_base       = null;



    /**
     * Start the routing.
     *
     * @access  public
     * @param   array   $parameters    Parameters of the router.
     * @return  array
     * @throw   Hoa_Controller_Exception
     */
    public function route ( Array $parameters = array() ) {

        $this->setParameters($parameters);
        $this->setBase();
        $this->setUrl();

        $rules      = $this->prepareASetOfRules($parameters);
        $candidates = $this->findCandidatesRules($rules);
        $theRule    = $this->findTheRule($candidates);

        if(null === $theRule)
            throw new Hoa_Controller_Exception(
                'No rule was adapted.', 0);

        $return     = $this->completeWithDefaultValue($theRule);

        return current($return);
    }

    /**
     * Prepare a set of rules.
     *
     * @access  protected
     * @param   array      $parameters    Parameters.
     * @return  array
     * @throw   Hoa_Controller_Exception
     */
    protected function prepareASetOfRules ( Array $parameters = array() ) {

        if(!isset($parameters['rules']))
            throw new Hoa_Controller_Exception(
                'Rules must be defined.', 1);

        if(empty($parameters['rules']))
            throw new Hoa_Controller_Exception(
                'At least, one rule must be defined', 2);

        $return = array();
        foreach($parameters['rules'] as $name => $patdef)
            $return[$name] = $patdef['pattern'];

        return $return;
    }

    /**
     * Find all candidates rules.
     *
     * @access  protected
     * @param   array      $rules    Rules.
     * @return  array
     */
    protected function findCandidatesRules ( Array $rules = array() ) {

        $prefixe = array_map(
                       create_function('$pattern',
                                       'return substr($pattern, 0,
                                               strpos($pattern, \'(:\'));'),
                       $rules);
        uasort($prefixe,
               create_function('$a,$b', 'return strlen($a) < strlen($b);'));

        $keepV = '';
        foreach($prefixe as $name => $pre)
            if(0 !== preg_match('#^' . $pre . '#', $this->getUrl())) {
                $keepV = $pre;
                break;
            }

        $keep = array();
        foreach($prefixe as $name => $pre)
            if($pre == $keepV)
                $keep[$name] = $rules[$name];

        unset($prefixe);

        return $keep;
    }

    /**
     * Find the rule to apply.
     *
     * @access  protected
     * @param   array      $candidates    The candidates rules.
     * @return  array
     * @throw   Hoa_Controller_Exception
     */
    protected function findTheRule ( Array $candidates = array() ) {

        // Prepare the patterns.
        $withRegularPatterns = array();
        foreach($candidates as $name => $pattern) {

            $withRegularPatterns[$name] = preg_replace('#(\(:[^\)]+\)(.))#e',
                                          "'([^' .  preg_quote('\\2') . ']+)' . preg_quote('\\2')",
                                          $pattern);
        }

        $found = array();
        foreach($withRegularPatterns as $name => $newPattern) {

            // Refine the search pattern.
            if(0 === preg_match_all('#([^\(]+)(\(\[\^(.{1,2})\]\+\))#',
                                    $newPattern, $foo,
                                    PREG_SET_ORDER))
                throw new Hoa_Controller_Exception(
                    'Cannot split the new pattern : %s.', 3, $newPattern);

            $handle = array();
            foreach($foo as $key => $splitted) {

                $handle[] = $splitted[1];
                $handle[] = $splitted[2];
                $handle[] = $splitted[3];
            }

            // Adapt the pattern to find the more adapted pattern and place
            // default value.
            if(0 === preg_match('#^' . $newPattern . '$#',
                                $this->getUrl(), $matches)) {

                $matches = '';
                $i       = count($handle);
                while($i-- > 0 &&
                      0 === preg_match('#^' . implode('', $this->myImplode($handle)) . '$#',
                                       $this->getUrl(), $matches))

                    array_pop($handle);
            }

            array_shift($matches);

            // Find the pattern name.
            preg_match_all('#\(:([^\)]+)\)#', $candidates[$name],
                           $patternName, PREG_PATTERN_ORDER);
            $patternName = $patternName[1];

            foreach($patternName as $key => $pname) {

                $found[$name][$pname] = isset($matches[$key])
                                            ? $matches[$key]
                                            : null;
            }
        }

        if(count($found) == 1)
            return $found;

        // We do not found one pattern, so search the more adequate pattern.
        // First, we only choice pattern that totally matche.
        $full = array();
        foreach($found as $name => $result) {

            end($result);
            if(current($result) !== null) {
                $full[$name] = $result;
                unset($found[$name]);
            }
            reset($result);
        }

        // If we got many patterns, we sort by genericity (desc).
        if(count($full) > 1) {

            foreach($full as $name => &$foo)
                $foo['__Hoa:count'] = substr_count($candidates[$name], '(:');

            uasort($full,
                   create_function(
                       '$a,$b',
                       'return $a[\'__Hoa:count\'] < $b[\'__Hoa:count\'];'));

            foreach($full as $name => &$foo)
                unset($foo['__Hoa:count']);
        }

        // For each pattern, we look if the last matched element matches
        // with the end of URL. If it matches, it is not the good pattern,
        // so we look the next pattern.
        $foundName = null;
        foreach($full as $name => $match) {

            end($match);
            $match = current($match);

            if(0 === preg_match('#' . $match . '$#', $this->getUrl())) {

                $foundName = $name;
                break;
            }
        }

        if(null !== $foundName)
            return array($foundName => $full[$foundName]);

        // If none of them matche, we look on the imcomplete match pattern.
        if(count($found) > 1) {

            foreach($found as $name => &$foo)
                $foo['__Hoa:count'] = substr_count($candidates[$name], '(:');

            uasort($found,
                   create_function('$a,$b', 'return $a[\'__Hoa:count\'] > $b[\'__Hoa:count\'];'));

            foreach($found as $name => &$foo)
                unset($foo['__Hoa:count']);
        }

        $foundName = null;
        foreach($found as $name => $match) {

            $handle = null;
            foreach($match as $param => $matched)
                if($matched !== null)
                    $handle = $matched;

            if(0 === preg_match('#' . $handle . '$#', $this->getUrl())) {

                $foundName = $name;
                break;
            }
        }

        if(null !== $foundName)
            return array($foundName => $found[$foundName]);

        if(isset($found['default']))
            return array('default' => $found['default']);

        if(isset($full['default']))
            return array('default' => $full['default']);

        return null;
    }

    /**
     * Complete the rules with default value.
     *
     * @access  protected
     * @param   array      $theRule    The rule to complete.
     * @return  array
     */
    protected function completeWithDefaultValue ( Array $theRule = array() ) {

        $ruleName = key($theRule);
        $default  = $this->getParameters();    
        $default  = $default['rules'][$ruleName]['default'];

        foreach($default as $key => $value)
            if(   !isset($theRule[$ruleName][$key])
               || null === $theRule[$ruleName][$key])
                    $theRule[$ruleName][$key] = $value;

        return $theRule;
    }

    /**
     * Remove all third line.
     *
     * @access  private
     * @param   array   $array    Array to manipulate.
     * @return  array
     */
    private function myImplode ( Array $array = array() ) {

        $handle = array();
        $pop    = array_pop($array);

        foreach($array as $key => $value) {

            if(($key + 1) % 3 != 0)
                $handle[] = $value;
        }

        $handle[] = $pop;

        return $handle;
    }

    /**
     * Set parameters
     *
     * @access  protected
     * @param   array      $parameters    Parameters.
     * @return  array
     */
    protected function setParameters ( Array $parameters = array() ) {

        $old               = $this->_parameters;
        $this->_parameters = $parameters;

        return $old;
    }

    /**
     * Set base of URL.
     *
     * @access  protected
     * @return  string
     */
    protected function setBase ( ) {

        $old         = $this->_base; 
        $this->_base = false !== $this->getParameter('base')
                           ? $this->getParameter('base')
                           : '';

        return $old;
    }

    /**
     * Set URL.
     *
     * @access  protected
     * @return  string
     * @throw   Hoa_Controller_Exception
     */
    protected function setUrl ( ) {

        if(!isset($_SERVER['REQUEST_URI']))
            throw new Hoa_Controller_Exception(
                'REQUEST_URI variable is not defined.', 4);

        if(0 === preg_match('#^' . $this->getBase() . '(.*)?$#',
                            $_SERVER['REQUEST_URI'], $elements))
            throw new Hoa_Controller_Exception(
                'Cannot match the base %s.', 5, $this->getBase());

        $old        = $this->_url;
        $this->_url = $elements[1];

        return $old;
    }

    /**
     * Get all parameters.
     *
     * @access  protected
     * @return  array
     */
    protected function getParameters ( ) {

        return $this->_parameters;
    }

    /**
     * Get a specific parameter.
     *
     * @access  protected
     * @param   string     $param    The parameter.
     * @return  mixed
     */
    protected function getParameter ( $param = null ) {

        $p = $this->getParameters();

        return isset($p[$param]) ? $p[$param] : false;
    }

    /**
     * Get base of URL.
     *
     * @access  protected
     * @return  string
     */
    protected function getBase ( ) {

        return $this->_base;
    }

    /**
     * Get URL.
     *
     * @access  protected
     * @return  string
     */
    protected function getUrl ( ) {

        return $this->_url;
    }
}
