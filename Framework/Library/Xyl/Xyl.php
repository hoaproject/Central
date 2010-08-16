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
 *
 *
 * @category    Framework
 * @package     Hoa_Xyl
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Xyl_Exception
 */
import('Xyl.Exception');

/**
 * Hoa_Xyl_Element
 */
import('Xyl.Element') and load();

/**
 * Hoa_Xyl_Element_Basic
 */
import('Xyl.Element.Basic');

/**
 * Hoa_Xml
 */
import('Xml.~') and load();

/**
 * Class Hoa_Xyl.
 *
 * 
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xyl
 */

class          Hoa_Xyl
    extends    Hoa_Xml
    implements Hoa_Xyl_Element {

    /**
     * Data bucket.
     *
     * @var Hoa_Xyl array
     */
    protected $_data  = array();

    /**
     * Map and store index.
     *
     * @var Hoa_Xyl array
     */
    private $_i       = 0;

    /**
     * Map index to XYL element.
     *
     * @var Hoa_Xyl array
     */
    private $_map     = array();

    /**
     * Store data of XYL element.
     *
     * @var Hoa_Xyl array
     */
    private $_store   = array();



    /**
     * Interprete a stream as XYL.
     *
     * @access  public
     * @param   Hoa_Stream  $stream    Stream to interprete as XYL.
     * @return  void
     * @throw   Hoa_Xml_Exception
     */
    public function __construct ( Hoa_Stream $stream ) {

        parent::__construct('Hoa_Xyl_Element_Basic', $stream);

        return;
    }

    /**
     * Get element store.
     *
     * @access  public
     * @param   Hoa_Xyl_Element  $element    Element as identifier.
     * @return  array
     */
    final public function &_getStore ( Hoa_Xyl_Element $element ) {

        if(false === $id = array_search($element, $this->_map)) {

            $id                = $this->_i++;
            $this->_map[$id]   = $element;
            $this->_store[$id] = null;
        }

        return $this->_store[$id];
    }

    /**
     * Add data to the data bucket.
     *
     * @access  public
     * @param   array  $data    Data to add.
     * @return  array
     */
    public function addData ( Array $data ) {

        return $this->_data = array_merge_recursive($this->_data, $data);
    }

    public function computeUse ( ) {

        // Mowgli c'est le p'tit DOM (euh, p'tit homme !)
        $mowgli      = $this->getStream()->readDOM()->ownerDocument;
        $streamClass = get_class($this->getInnerStream());
        $uses        = $this->getStream()->selectElement('use');
        $hrefs       = array();

        do {

            $use        = array_pop($uses);
            $usedomized = $use->readDOM();

            if(false === $usedomized->hasAttribute('href'))
                continue;

            $href = $usedomized->getAttribute('href');

            if(true === in_array($href, $hrefs))
                continue;

            $hrefs[]  = $href;
            $fragment = new Hoa_Xyl(new $streamClass($href));

            if('definition' !== $fragment->getName())
                throw new Hoa_Xyl_Exception(
                    '%s must only contain <definition> of <yield> (and some ' .
                    '<use />) elements.', 1, $href);

            foreach($fragment->xpath('//yield[@name]') as $yield)
                $mowgli->documentElement->appendChild(
                    $mowgli->importNode($yield->readDOM(), true)
                );

            $usedomized->parentNode->removeChild($usedomized);
            unset($usedomized);

            $uses += $fragment->selectElement('use');

        } while(!empty($uses));
        
        return;
    }

    public function computeYielder ( ) {

        foreach($this->getStream()->xpath('//yield[@name]') as $yield) {

            $yieldomized = $yield->readDOM();
            $name        = $yieldomized->getAttribute('name');
            $yieldomized->removeAttribute('name');
            $yieldomized->removeAttribute('bind');
            $yieldomized->parentNode->removeChild($yieldomized);

            foreach($this->getStream()->selectElement($name) as $ciao) {

                $placeholder = $ciao->readDOM();
                $parent      = $placeholder->parentNode;
                $handle      = $yieldomized->cloneNode(true);

                if(true === $placeholder->hasAttribute('bind'))
                    $handle->setAttribute(
                        'bind',
                        $placeholder->getAttribute('bind')
                    );

                $parent->replaceChild($handle, $placeholder);
            }
        }

        return;
    }

    /**
     * Distribute data into the XYL tree. Data are linked to element through a
     * reference to the data bucket in this object.
     *
     * @access  public
     * @return  void
     */
    public function computeDataBinding ( ) {

        return $this->getStream()->computeDataBinding($this->_data);
    }

    /**
     * Get data of this element.
     *
     * @access  public
     * @return  array
     */
    public function &getData ( ) {

        return $this->getStream()->getData();
    }

    /**
     * Get current data of this element.
     *
     * @access  public
     * @return  mixed
     */
    public function getCurrentData ( ) {

        return $this->getStream()->getCurrentData();
    }

    public function firstUpdate ( ) {

        return $this->getStream()->firstUpdate();
    }

    public function update ( ) {

        return $this->getStream()->update();
    }
}
