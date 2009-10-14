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
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Frontend_Function
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Cache
 */
import('Cache.~');

/**
 * Class Hoa_Cache_Frontend_Function.
 *
 * Function catching system for frontend cache.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Frontend_Function
 */

class Hoa_Cache_Frontend_Function extends Hoa_Cache {

    /**
     * Method arguments.
     *
     * @var Hoa_Cache_Frontend_Class array
     */
    protected $arguments = array();



    /**
     * Overload member class with __call.
     *
     * @access  public
     * @param   string  $function     Function called.
     * @param   array   $arguments    Arguments of method.
     * @return  mixed
     * @throw   Hoa_Cache_Exception
     */
    public function __call ( $function, Array $arguments ) {

        if(!function_exists($function))
            throw new Hoa_Cache_Exception('Function %s does not exists.',
                0, $function);

        $this->arguments = $this->ksort($arguments);

        $id = $function;
        $this->makeId($id);

        if(false !== $content = $this->load($this->getId(Hoa_Cache::GET_ID_MD5, $id))) {
            echo $content[0];   // output
            return $content[1]; // return
        }

        ob_start();
        ob_implicit_flush(false);
        $return = call_user_func_array($function, $arguments);
        $output = ob_get_contents();
        ob_end_clean();

        $this->save($this->getId(Hoa_Cache::GET_ID_MD5), array($output, $return));
        $this->removeId();

        echo $output;
        return $return;
    }

    /**
     * Own _makeId class method.
     *
     * @access  public
     * @param   string  $id    ID.
     * @return  string
     * @throw   Hoa_Cache_Exception
     */
    public function _makeId ( $id = null ) {

        if(!preg_match('#([a-zA-Z0-9]+)#', $id))
            throw new Hoa_Cache_Exception('%s is not a valid ID.', 1, $id);

        return serialize($this->arguments);
    }
}
