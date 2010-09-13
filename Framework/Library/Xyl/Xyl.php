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
 * Hoa_Xml_Attribute
 */
import('Xml.Attribute');

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
     * Evaluate XPath expression.
     *
     * @var DOMXPath object
     */
    private $_xe         = null;



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
        $this->_xe         = new DOMXPath(new DOMDocument());

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

            $id                = ++$this->_i;
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
     * Compute <?xyl-use?> processing-instruction.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Xml_Exception
     * @TODO    Maybe overlay should support <?xyl-use?>?
     */
    public function computeUse ( ) {

        // Mowgli c'est le p'tit DOM (euh, p'tit homme !)
        $mowgli      = $this->getStream()->readDOM()->ownerDocument;
        $streamClass = get_class($this->getInnerStream());
        $hrefs       = array();
        $uses        = array();
        $xpath       = new DOMXPath($mowgli);
        $xyl_use     = $xpath->query('/processing-instruction(\'xyl-use\')');
        unset($xpath);

        if(0 === $xyl_use->length)
            return false;

        for($i = 0, $m = $xyl_use->length; $i < $m; ++$i)
            $uses[] = $xyl_use->item($i);

        do {

            $use       = array_pop($uses);
            $useParsed = new Hoa_Xml_Attribute($use->data);

            if(false === $useParsed->attributeExists('href'))
                continue;

            $href = $useParsed->readAttribute('href');
            unset($useParsed);

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
                    '<?xyl-use) elements.', 2, $href);

            foreach($fragment->xpath('//yield[@name]') as $yield)
                $mowgli->documentElement->appendChild(
                    $mowgli->importNode($yield->readDOM(), true)
                );

            unset($use);
            unset($xyl_use);

            $xpath   = new DOMXPath($fragment->readDOM()->ownerDocument);
            $xyl_use = $xpath->query('/processing-instruction(\'xyl-use\')');
            unset($xpath);

            for($i = 0, $m = $xyl_use->length; $i < $m; ++$i)
                $uses[] = $xyl_use->item($i);

        } while(!empty($uses));
        
        return true;
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
     * Compute <?xyl-overlay?> processing-instruction.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Xml_Exception
     */
    public function computeOverlay ( ) {

        // Mowgli c'est le p'tit DOM (euh, p'tit homme !)
        $mowgli      = $this->getStream()->readDOM()->ownerDocument;
        $streamClass = get_class($this->getInnerStream());
        $hrefs       = array();
        $overlays    = array();
        $xpath       = new DOMXPath($mowgli);
        $xyl_overlay = $xpath->query('/processing-instruction(\'xyl-overlay\')');
        unset($xpath);

        if(0 === $xyl_overlay->length)
            return false;

        for($i = 0, $m = $xyl_overlay->length; $i < $m; ++$i)
            $overlays[] = $xyl_overlay->item($i);

        do {

            $overlay       = array_pop($overlays);
            $overlayParsed = new Hoa_Xml_Attribute($overlay->data);

            if(false === $overlayParsed->attributeExists('href'))
                continue;

            $href = $overlayParsed->readAttribute('href');
            unset($overlayParsed);

            if(false === file_exists($href))
                throw new Hoa_Xyl_Exception(
                    'File %s is not found, cannot use it.', 2, $href);

            if(true === in_array($href, $hrefs))
                continue;

            $hrefs[]  = $href;
            $fragment = new Hoa_Xyl(new $streamClass($href));

            if('overlay' !== $fragment->getName())
                throw new Hoa_Xyl_Exception(
                    '%s must only contain <overlay> (and some <?xyl-overlay) ' .
                    'elements.', 2, $href);

            foreach($fragment->selectChildElement() as $e => $element)
                $this->_computeOverlay(
                    $mowgli->documentElement,
                    $mowgli->importNode($element->readDOM(), true)
                );

            unset($overlay);
            unset($xyl_overlay);

            $xpath       = new DOMXPath($fragment->readDOM()->ownerDocument);
            $xyl_overlay = $xpath->query('/processing-instruction(\'xyl-overlay\')');
            unset($xpath);

            for($i = 0, $m = $xyl_overlay->length; $i < $m; ++$i)
                $overlays[] = $xyl_overlay->item($i);

        } while(!empty($overlays));

        return true;
    }

    /**
     * Next step for computing overlay.
     *
     * @access  private
     * @param   DOMElement  $from    Receiver fragment.
     * @param   DOMElement  $to      Overlay fragment.
     * @return  void
     */
    private function _computeOverlay ( DOMElement $from, DOMElement $to ) {

        if(false === $to->hasAttribute('id'))
            return $this->_computeOverlayPosition($from, $to);

        $xpath = new DOMXPath($from->ownerDocument);
        $query = $xpath->query('//*[@id="' . $to->getAttribute('id') . '"]');

        if(0 === $query->length)
            return $this->_computeOverlayPosition($from, $to);

        $from  = $query->item(0);

        foreach($to->attributes as $name => $node)
            switch($name) {

                case 'id':
                    break;

                case 'class':
                    if(false === $from->hasAttribute('class')) {

                        $from->setAttribute('class', $node->value);

                        break;
                    }

                    $classListTo   = explode(' ', $node->value);
                    $classListFrom = explode(' ', $from->getAttribute('class'));

                    $from->setAttribute(
                        'class',
                        implode(
                            ' ',
                            array_unique(
                                array_merge($classListFrom, $classListTo)
                            )
                        )
                    );
                  break;

                default:
                    $from->setAttribute($name, $node->value);
            }

        $children = array();

        for($h = $to->childNodes, $i = 0, $m = $h->length; $i < $m; ++$i) {

            $element = $h->item($i);

            if(XML_ELEMENT_NODE != $element->nodeType)
                continue;

            $children[] = $element;
        }

        foreach($children as $i => $child)
            $this->_computeOverlay($from, $child);

        return;
    }

    /**
     * Compute position while computing overlay.
     *
     * @access  private
     * @param   DOMElement  $from    Receiver fragment.
     * @param   DOMElement  $to      Overlay fragment.
     * @return  void
     */
    private function _computeOverlayPosition ( DOMElement $from,
                                               DOMElement $to ) {

        if(false === $to->hasAttribute('position')) {

            $from->appendChild($to);

            return;
        }

        $children  = $from->childNodes;
        $positions = array();
        $e         = 0;
        $search    = array();
        $replace   = array();
        $child     = null;

        for($i = 0, $m = $children->length; $i < $m; $i++) {

            $child = $children->item($i);

            if(XML_ELEMENT_NODE != $child->nodeType)
                continue;

            $positions[$e] = $i;

            if($child->hasAttribute('id')) {

                $search[]  = 'element(#' . $child->getAttribute('id') . ')';
                $replace[] = $e + 1;
            }

            ++$e;
        }

        $last      = count($positions);
        $search[]  = 'last()';
        $replace[] = $last;
        $handle    = str_replace($search, $replace, $to->getAttribute('position'));
        $position  = max(0, (int) $this->_xe->evaluate($handle));

        if($position < $last)
            $from->insertBefore(
                $to,
                $from->childNodes->item($positions[$position])
            );
        else
            $from->appendChild($to);

        $to->removeAttribute('position');

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
