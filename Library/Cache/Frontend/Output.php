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
 * @subpackage  Hoa_Cache_Frontend_Output
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
 * Class Hoa_Cache_Frontend_Output.
 *
 * Ouput catching system for frontend cache.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Frontend_Output
 */

class Hoa_Cache_Frontend_Output extends Hoa_Cache {

    /**
     * Output buffer level.
     *
     * @var Hoa_Cache_Frontend_Output array
     */
    protected $level = array();



    /**
     * Start an output buffering.
     *
     * @access  public
     * @param   string  id    ID of cache.
     * @return  bool
     */
    public function start ( $id ) {

        $this->makeId($id);

        if(false !== $out = $this->load($this->getId(Hoa_Cache::GET_ID_MD5, $id))) {
            echo $out;
            return false;
        }

        ob_start();
        ob_implicit_flush(false);
        $this->level[$this->getId(Hoa_Cache::GET_ID_MD5, $id)] = ob_get_level();

        return true;
    }

    /**
     * End an output buffering.
     *
     * @access  public
     * @return  bool
     */
    public function end ( ) {

        $content = '';

        while(ob_get_level() >= $this->level[$this->getId(Hoa_Cache::GET_ID_MD5)])
            $content .= ob_get_clean();

        $this->save($this->getId(Hoa_Cache::GET_ID_MD5), $content);
        $this->removeId();

        echo $content;
    }
}
