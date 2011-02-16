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
 * \Hoa\Memory\Exception
 */
-> import('Memory.Exception');

}

namespace Hoa\Memory {

/**
 * Class \Hoa\Memory\GarbageCollector.
 *
 * Manage the PHP Garbage Collector.
 * Please, read http://www.research.ibm.com/people/d/dfb/papers/Bacon03Pure.pdf
 * to know more about its behavior.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class GarbageCollector {

    /**
     * Active the garbage collector.
     *
     * @access  public
     * @return  void
     */
    public static function enable ( ) {

        return gc_enable();
    }

    /**
     * Disactive the garbage collector.
     *
     * @access  public
     * @return  void
     */
    public static function disable ( ) {

        return gc_disable();
    }

    /**
     * Return the circular reference collector status.
     *
     * @access  public
     * @return  bool
     */
    public static function isEnabled ( ) {

        return gc_enabled();
    }

    /**
     * Force collection of any existing garbage cycles.
     *
     * @access  public
     * @return  int
     */
    public static function collect ( ) {

        return gc_collect_cycles();
    }
}

}
