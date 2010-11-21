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
import('Xyl.Element.Basic') and load();

/**
 * Hoa_Xyl_Element_Executable
 */
import('Xyl.Element.Executable');

/**
 * Hoa_Xml
 */
import('Xml.~') and load();

/**
 * Hoa_Xml_Attribute
 */
import('Xml.Attribute');

/**
 * Hoa_View_Viewable
 */
import('View.Viewable');

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
               Hoa_View_Viewable {

    /**
     * XYL's namespace.
     *
     * @const string
     */
    const NAMESPACE_ID = 'http://hoa-project.net/ns/xyl';

    /**
     * Data bucket.
     *
     * @var Hoa_Xyl array
     */
    protected $_data        = null;

    /**
     * Concrete tree.
     *
     * @var Hoa_Xyl_Element_Concrete object
     */
    protected $_concrete    = null;

    /**
     * Evaluate XPath expression.
     *
     * @var DOMXPath object
     */
    protected $_xe          = null;

    /**
     * Output stream.
     *
     * @var Hoa_Stream_Interface_Out object
     */
    protected $_out         = null;

    /**
     * Interpreter.
     *
     * @var Hoa_Xyl_Interpreter object
     */
    protected $_interpreter = null;

    /**
     * Mowgli c'est le p'tit DOM (euh, p'tit homme !)
     * Well, it's the document root.
     *
     * @var DOMDocument object
     */
    protected $_mowgli      = null;



    /**
     * Interprete a stream as XYL.
     *
     * @access  public
     * @param   Hoa_Stream_Interface_In   $in             Stream to interprete
     *                                                    as XYL.
     * @param   Hoa_Stream_Interface_Out  $out            Stream for rendering.
     * @param   Hoa_Xyl_Interpreter       $interpreter    Interpreter.
     * @return  void
     * @throw   Hoa_Xml_Exception
     */
    public function __construct ( Hoa_Stream_Interface_In  $in,
                                  Hoa_Stream_Interface_Out $out,
                                  Hoa_Xyl_Interpreter      $interpreter = null ) {

        parent::__construct('Hoa_Xyl_Element_Basic', $in);
        
        $this->_xe = new DOMXPath(new DOMDocument());

        if(false === $this->namespaceExists(self::NAMESPACE_ID))
            throw new Hoa_Xyl_Exception(
                'The XYL file %s has no XYL namespace declared.',
                0, $stream->getStreamName());

        $this->useNamespace(self::NAMESPACE_ID);
        $this->_data        = new Hoa_Core_Data();
        $this->_out         = $out;
        $this->_interpreter = $interpreter;
        $this->_mowgli      = $this->getStream()->readDOM()->ownerDocument;

        return;
    }

    /**
     * Get data.
     *
     * @access  public
     * @return  Hoa_Core_Data
     */
    public function getData ( ) {

        return $this->_data;
    }

    /**
     * Get output stream.
     *
     * @access  public
     * @return  Hoa_Stream_Interface_Out
     */
    public function getOutputStream ( ) {

        return $this->_out;
    }

    /**
     * Add a <?xyl-use?> processing-instruction (only that).
     *
     * @access  public
     * @return  void
     */
    public function addUse ( $href ) {

        $this->_mowgli->insertBefore(
            new DOMProcessingInstruction(
                'xyl-use',
                'href="' . str_replace('"', '\"', $href) . '"'
            ),
            $this->_mowgli->firstChild
        );

        return;
    }

    /**
     * Compute <?xyl-use?> processing-instruction.
     *
     * @access  protected
     * @return  bool
     * @throw   Hoa_Xml_Exception
     */
    protected function computeUse ( ) {

        $streamClass = get_class($this->getInnerStream());
        $hrefs       = array();
        $uses        = array();
        $xpath       = new DOMXPath($this->_mowgli);
        $xyl_use     = $xpath->query('/processing-instruction(\'xyl-use\')');
        unset($xpath);

        if(0 === $xyl_use->length)
            return false;

        for($i = 0, $m = $xyl_use->length; $i < $m; ++$i) {

            $item   = $xyl_use->item($i);
            $uses[] = $item;
            $this->_mowgli->removeChild($item);
        }

        do {

            $use       = array_pop($uses);
            $useParsed = new Hoa_Xml_Attribute($use->data);

            if(false === $useParsed->attributeExists('href'))
                continue;

            $href = $useParsed->readAttribute('href');
            unset($useParsed);

            if(false === file_exists($href))
                throw new Hoa_Xyl_Exception(
                    'File %s is not found, cannot use it.', 0, $href);

            if(true === in_array($href, $hrefs))
                continue;

            $hrefs[]  = $href;
            $fragment = new Hoa_Xyl(new $streamClass($href), $this->_out);

            if('definition' !== $fragment->getName())
                throw new Hoa_Xyl_Exception(
                    '%s must only contain <definition> of <yield> (and some ' .
                    '<?xyl-use) elements.', 1, $href);

            foreach($fragment->xpath('//__current_ns:yield[@name]') as $yield)
                $this->_mowgli->documentElement->appendChild(
                    $this->_mowgli->importNode($yield->readDOM(), true)
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
     * @access  protected
     * @return  void
     */
    protected function computeYielder ( ) {

        foreach($this->getStream()->xpath('//__current_ns:yield[@name]') as $yield) {

            $yieldomized = $yield->readDOM();
            $name        = $yieldomized->getAttribute('name');
            $yieldomized->removeAttribute('name');
            $yieldomized->removeAttribute('bind');

            foreach($this->getStream()->xpath('//__current_ns:' . $name) as $ciao) {

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

            $yieldomized->parentNode->removeChild($yieldomized);
        }

        return;
    }

    /**
     * Add a <?xyl-overlay?> processing-instruction (only that).
     *
     * @access  public
     * @return  void
     */
    public function addOverlay ( $href ) {

        $this->_mowgli->insertBefore(
            new DOMProcessingInstruction(
                'xyl-overlay',
                'href="' . str_replace('"', '\"', $href) . '"'
            ),
            $this->_mowgli->firstChild
        );

        return;
    }

    /**
     * Compute <?xyl-overlay?> processing-instruction.
     *
     * @access  protected
     * @return  bool
     * @throw   Hoa_Xml_Exception
     */
    protected function computeOverlay ( ) {

        $streamClass = get_class($this->getInnerStream());
        $hrefs       = array();
        $overlays    = array();
        $xpath       = new DOMXPath($this->_mowgli);
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
            $fragment = new Hoa_Xyl(new $streamClass($href), $this->_out);

            if('overlay' !== $fragment->getName())
                throw new Hoa_Xyl_Exception(
                    '%s must only contain <overlay> (and some <?xyl-overlay) ' .
                    'elements.', 3, $href);

            foreach($fragment->selectChildElements() as $e => $element)
                $this->_computeOverlay(
                    $this->_mowgli->documentElement,
                    $this->_mowgli->importNode($element->readDOM(), true)
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

        for($i = 0, $m = $children->length; $i < $m; ++$i) {

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
     * @access  protected
     * @return  void
     */
    protected function computeDataBinding ( ) {

        if(null === $this->_concrete)
            throw new Hoa_Xyl_Exception(
                'Cannot compute the data binding before building the ' .
                'concrete tree.', 4);

        $data = $this->getData()->toArray();

        return $this->_concrete->computeDataBinding($data);
    } 

    /**
     * Interprete XYL as…
     *
     * @access  public
     * @param   Hoa_Xyl_Interpreter  $interpreter    Interpreter.
     * @return  void
     * @throws  Hoa_Xyl_Exception
     */
    public function interpreteAs ( Hoa_Xyl_Interpreter $interpreter ) {

        $this->computeUse();
        $this->computeYielder();
        $this->computeOverlay();

        $rank = $interpreter->getRank();
        $root = $this->getStream();
        $name = strtolower($root->getName());

        if(false === array_key_exists($name, $rank))
            throw new Hoa_Xyl_Exception(
                'Cannot create the concrete tree because the root <%s> is ' .
                'unknown from the rank.', 5, $name);

        $class           = $rank[$name];
        $this->_concrete = new $class($root, $this, $rank, self::NAMESPACE_ID);

        $this->computeDataBinding();

        return;
    }

    /**
     * Run the render.
     *
     * @access  public
     * @return  string
     */
    public function render ( ) {

        if(null === $this->_concrete)
            $this->interpreteAs($this->_interpreter);

        return $this->_concrete->render($this->_out);
    }
}
