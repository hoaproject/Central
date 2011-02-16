<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Yaml
 * @subpackage  Hoa_Yaml_Node
 *
 */

/**
 * Class Hoa_Yaml_Node.
 *
 * Manage Yaml nodes (like a tree).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Yaml
 * @subpackage  Hoa_Yaml_Node
 */

class Hoa_Yaml_Node {

    /**
     * Pointers nodes list.
     *
     * @var Hoa_Yaml_Node array
     */
    protected $ptr = array();

    /**
     * Last node.
     *
     * @var Hoa_Yaml_Node string
     */
    protected $last = '';

    /**
     * Node track list.
     *
     * @var Hoa_Yaml_Node array
     */
    protected $list = array(-1 => 'node');



    /**
     * create
     * Create a node.
     *
     * @access  public
     * @param   i        int       Node number.
     * @param   node     string    Node name.
     * @param   value    string    Node value.
     * @param   return   string    Return.
     * @throw   Hoa_Yaml_Exception
     */
    public function create ( $i = -2, $node = '', $value = '', $return = 'value' ) {

        static $def = array('node' => '');


        if($i < -1)
            throw new Hoa_Yaml_Exception('i could not be less than -1.', 0);

        if(empty($node) && $node != 0)
            throw new Hoa_Yaml_Exception('Node could not be empty.', 1);

        if(!isset($this->ptr[-1]))
            $this->ptr[-1] = &$def;

        if(!isset($this->ptr[$i-1]))
            throw new Hoa_Yaml_Exception('Could not broken id pointers sequence (miss id %d).', 2, $i-1);

        $this->last               = $this->list[$i-1];
        $this->list[$i]           = $node;

        // Auto-increment
        if($node == '**auto**') {

            if(isset($this->ptr[$i-1]))
                $this->ptr[$i-1][$this->last][] = $value;
            else {
                $this->ptr[$i][] = $value;
                $this->ptr[$i-1] = array(key($this->ptr[$i-1]) => $this->ptr[$i]);
            }
            $this->ptr[$i]       = &$this->ptr[$i-1][$this->last];
            end($this->ptr[$i]);
            $this->list[$i]      = key($this->ptr[$i]);

            switch($return) {

                case 'value':
                    return $this->ptr[$i];
                  break;

                case 'key':
                    return $this->list[$i];
                  break;

                default:
                    return $this->ptr[$i];
            }
        }

        // Specific node
        else {

            if(isset($this->ptr[$i-1]))
                $this->ptr[$i-1][$this->last][$node] = $value;
            else {
                $this->ptr[$i][$node] = $value;
                $this->ptr[$i-1]      = array(key($this->ptr[$i-1]) => $this->ptr[$i]);
            }
            $this->ptr[$i]            = &$this->ptr[$i-1][$this->last];

            switch($return) {

                case 'value':
                    return $this->ptr[$i][$node];
                  break;

                case 'key':
                    return $this->list[$i];
                  break;

                default:
                    return $this->ptr[$i][$node];
            }
        }
    }

    /**
     * reach
     * Reach a node.
     *
     * @access  public
     * @param   i       int       Node number.
     * @param   node    string    Node name.
     * @return  mixed
     * @throw   Hoa_Yaml_Exception
     */
    public function reach ( $i = -2, $node = '' ) {

        if($i < -1)
            throw new Hoa_Yaml_Exception('i could not be less than -1.', 3);

        return isset($this->ptr[$i][$node]) ? $this->ptr[$i][$node] : false;
    }

    /**
     * remove
     * Remove a node.
     *
     * @access  public
     * @param   i       int       Node number.
     * @param   node    string    Node name.
     * @return  bool
     * @throw   Hoa_Yaml_Exception
     */
    public function remove ( $i = -2, $node = '' ) {

        if($i < -1)
            throw new Hoa_Yaml_Exception('i could not be less than -1.', 4);

        unset($this->ptr[$i][$node]);

        return true;
    }

    /**
     * clean
     * Reset array and variables.
     *
     * @access  public
     * @return  void
     */
    public function clean ( ) {

        $this->ptr[-1]['node'] = array();
        $this->last            = '';
        $this->list            = array(-1 => 'node');
    }

    /**
     * finalize
     * Finalize array.
     *
     * @access  public
     * @return  array
     */
    public function finalize ( ) {

        if(isset($this->ptr[-1]['node']))
            return $this->ptr[-1]['node'];
        else
            return array();
    }
}

