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
 * \Hoa\Xyl\Element\Concrete
 */
-> import('Xyl.Element.Concrete')

/**
 * \Hoa\Xyl\Element\Executable
 */
-> import('Xyl.Element.Executable')

/**
 * \Hoa\Xml\Element\Model\Phrasing
 */
-> import('Xml.Element.Model.Phrasing');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Link.
 *
 * The <link /> component.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class          Link
    extends    \Hoa\Xyl\Element\Concrete
    implements \Hoa\Xyl\Element\Executable,
               \Hoa\Xyl\Element\Model\Phrasing {

    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( \Hoa\Stream\IStream\Out $out ) {

        $out->writeAll('<a' . $this->readAttributesAsString() . '>' . "\n");
        $this->computeValue($out);
        $out->writeAll('</a>' . "\n");

        return;
    }

    /**
     * Execute an element.
     *
     * @access  public
     * @return  void
     */
    public function execute ( ) {

        $router = $this->getAbstractElementSuperRoot()->getRouter();

        if(null === $router)
            return;

        $href = $this->readAttribute('href');

        if(0 != preg_match('#^@([^:]+):(.*)$#', $href, $matches)) {

            $id = $matches[1];
            $kv = array();

            foreach(explode('&', $matches[2]) as $value) {

                $handle                    = explode('=', $value);
                $kv[urldecode($handle[0])] = urldecode($handle[1]);
            }

            $this->writeAttribute('href', $router->unroute($id, $kv));
        }

        return;
    }
}

}
