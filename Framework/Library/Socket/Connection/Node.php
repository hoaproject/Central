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

namespace Hoa\Socket\Connection {

/**
 * Class \Hoa\Socket\Connection\Node.
 *
 * Represent a generic node.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Node {

    /**
     * Node's ID.
     *
     * @var \Hoa\Socket\Connection\Node string
     */
    protected $_id   = null;

    /**
     * Node's socket resource.
     *
     * @var \Hoa\Socket\Connection\Node resource
     */
    private $_socket = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   string    $id        ID.
     * @param   resource  $socket    Socket.
     * @return  void
     */
    public function __construct ( $id, $socket ) {

        $this->_id     = $id;
        $this->_socket = $socket;

        return;
    }

    /**
     * Get node's ID.
     *
     * @access  public
     * @return  string
     */
    public function getId ( ) {

        return $this->_id;
    }

    /**
     * Get node's socket resource.
     *
     * @access  public
     * @return  resource
     */
    public function getSocket ( ) {

        return $this->_socket;
    }
}

}
