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
    implements Hoa_Xyl_Element,
               Hoa_Core_Parameterizable {

    /**
     * The Hoa_Xyl parameters.
     *
     * @var Hoa_Core_Parameter object
     */
    private $_parameters = null;

    /**
     * Data bucket.
     *
     * @var Hoa_Xyl array
     */
    protected $_data     = array();

    /**
     * Map and store index.
     *
     * @var Hoa_Xyl array
     */
    private $_i          = 0;

    /**
     * Map index to XYL element.
     *
     * @var Hoa_Xyl array
     */
    private $_map        = array();

    /**
     * Store data of XYL element.
     *
     * @var Hoa_Xyl array
     */
    private $_store      = array();



    /**
     * Interprete a stream as XYL.
     *
     * @access  public
     * @param   Hoa_Stream  $stream          Stream to interprete as XYL.
     * @param   array       $parameters      Parameters.
     * @return  void
     * @throw   Hoa_Xml_Exception
     */
    public function __construct ( Hoa_Stream $stream,
                                  Array      $parameters = array() ) {

        parent::__construct('Hoa_Xyl_Element_Basic', $stream);

        $this->_parameters = new Hoa_Core_Parameter(
            $this,
            array(),
            array()
        );

        return;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in    Parameters to set.
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
     * @param   string  $key    Key.
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

    /**
     * Compute <use /> tags.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Xml_Exception
     */
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

            if(false === file_exists($href))
                throw new Hoa_Xyl_Exception(
                    'File %s is not found, cannot use it.', 1, $href);

            if(true === in_array($href, $hrefs))
                continue;

            $hrefs[]  = $href;
            $fragment = new Hoa_Xyl(new $streamClass($href));

            if('definition' !== $fragment->getName())
                throw new Hoa_Xyl_Exception(
                    '%s must only contain <definition> of <yield> (and some ' .
                    '<use />) elements.', 2, $href);

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

    /**
     * Compute <yield /> tags.
     *
     * @access  public
     * @return  void
     */
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
