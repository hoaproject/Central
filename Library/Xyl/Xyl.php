<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace {

from('Hoa')

/**
 * \Hoa\Xyl\Exception
 */
-> import('Xyl.Exception')

/**
 * \Hoa\Xyl\Element
 */
-> import('Xyl.Element.~')

/**
 * \Hoa\Xyl\Element\Basic
 */
-> import('Xyl.Element.Basic', true)

/**
 * \Hoa\Xml
 */
-> import('Xml.~')

/**
 * \Hoa\Xml\Attribute
 */
-> import('Xml.Attribute')

/**
 * \Hoa\View\Viewable
 */
-> import('View.Viewable');

}

namespace Hoa\Xyl {

/**
 * Class \Hoa\Xyl.
 *
 * 
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class          Xyl
    extends    \Hoa\Xml
    implements Element,
               \Hoa\View\Viewable,
               \Hoa\Core\Parameter\Parameterizable {

    /**
     * XYL's namespace.
     *
     * @const string
     */
    const NAMESPACE_ID    = 'http://hoa-project.net/xyl/xylophone';

    /**
     * Type: <document>
     *
     * @const int
     */
    const TYPE_DOCUMENT   = 0;

    /**
     * Type: <definition>
     *
     * @const int
     */
    const TYPE_DEFINITION = 1;

    /**
     * Type: <overlay>
     *
     * @const int
     */
    const TYPE_OVERLAY    = 2;

    /**
     * Selector type: path.
     *
     * @const int
     */
    const SELECTOR_PATH   = 0;

    /**
     * Selector type: query.
     *
     * @const int
     */
    const SELECTOR_QUERY  = 1;

    /**
     * Selector type: xpath.
     *
     * @const int
     */
    const SELECTOR_XPATH  = 2;

    /**
     * Parameters.
     *
     * @var \Hoa\Core\Parameter object
     */
    protected static $_parameters = null;

    /**
     * Data bucket.
     *
     * @var \Hoa\Xyl array
     */
    protected $_data              = null;

    /**
     * Concrete tree.
     *
     * @var \Hoa\Xyl\Element\Concrete object
     */
    protected $_concrete          = null;

    /**
     * Evaluate XPath expression.
     *
     * @var DOMXPath object
     */
    protected $_xe                = null;

    /**
     * Output stream.
     *
     * @var \Hoa\Stream\IStream\Out object
     */
    protected $_out               = null;

    /**
     * Interpreter.
     *
     * @var \Hoa\Xyl\Interpreter object
     */
    protected $_interpreter       = null;

    /**
     * Router.
     *
     * @var \Hoa\Router\Http object
     */
    protected $_router            = null;

    /**
     * Mowgli c'est le p'tit DOM (euh, p'tit homme !)
     * Well, it's the document root.
     *
     * @var DOMDocument object
     */
    protected $_mowgli            = null;

    /**
     * Type. Please, see self::TYPE_* constants.
     *
     * @var \Hoa\Xyl int
     */
    protected $_type              = null;

    /**
     * Temporize stylesheets.
     *
     * @var \Hoa\Xyl array
     */
    protected $_stylesheets       = array();

    /**
     * Get ID of the instance.
     *
     * @var \Hoa\Xyl int
     */
    private $_i                   = 0;

    /**
     * Get last ID of instances.
     *
     * @var \Hoa\Xyl int
     */
    private static $_ci           = 0;

    /**
     * Whether the document is open from the constructor or from the
     * self::open() method.
     *
     * @var \Hoa\Xyl bool
     */
    private $_innerOpen           = false;



    /**
     * Interprete a stream as XYL.
     *
     * @access  public
     * @param   \Hoa\Stream\IStream\In     $in             Stream to interprete
     *                                                     as XYL.
     * @param   \Hoa\Stream\IStream\Out    $out            Stream for rendering.
     * @param   \Hoa\Xyl\Interpreter       $interpreter    Interpreter.
     * @param   \Hoa\Router\Http           $router         Router.
     * @param   array                      $parameters     Parameters.
     * @return  void
     * @throw   \Hoa\Xml\Exception
     */
    public function __construct ( \Hoa\Stream\IStream\In  $in,
                                  \Hoa\Stream\IStream\Out $out,
                                  Interpreter             $interpreter,
                                  \Hoa\Router\Http        $router     = null,
                                  Array                   $parameters = array() ) {

        parent::__construct('\Hoa\Xyl\Element\Basic', $in);

        if(false === $this->namespaceExists(self::NAMESPACE_ID))
            throw new Exception(
                'The XYL file %s has no XYL namespace (%s) declared.',
                0, array($in->getStreamName(), self::NAMESPACE_ID));

        if(null === self::$_parameters) {

            self::$_parameters = new \Hoa\Core\Parameter(
                $this,
                array(
                    'theme' => 'classic'
                ),
                array(
                    'theme' => '(:theme:lU:)'
                )
            );
            $this->getParameters()->setParameters($parameters);
        }

        $this->_i           = self::$_ci++;
        $this->_xe          = new \DOMXPath(new \DOMDocument());
        $this->_data        = new \Hoa\Core\Data();
        $this->_out         = $out;
        $this->_interpreter = $interpreter;
        $this->_router      = $router;
        $this->_mowgli      = $this->getStream()->readDOM()->ownerDocument;

        switch(strtolower($this->getName())) {

            case 'document':
                $this->_type = self::TYPE_DOCUMENT;
              break;

            case 'definition':
                $this->_type = self::TYPE_DEFINITION;
              break;

            case 'overlay':
                $this->_type = self::TYPE_OVERLAY;
              break;

            default:
                throw new Exception(
                    'Unknown document <%s>.', 1, $this->getName());
        }

        $this->useNamespace(self::NAMESPACE_ID);
        \Hoa\Core::getInstance()
                ->getProtocol()
                ->getComponent('Library')
                ->addComponent(new _Protocol(
                    'Xyl[' . $this->_i . ']',
                    'Interpreter' . DS . $this->_interpreter->getResourcePath()
                ));

        if(null !== $router && false === $router->ruleExists('_resource'))
            $router->_all('_resource', '/(?<theme>)/(?<resource>)');

        return;
    }

    /**
     * Get parameters.
     *
     * @access  public
     * @return  \Hoa\Core\Parameter
     */
    public function getParameters ( ) {

        return self::$_parameters;
    }

    /**
     * Get data.
     *
     * @access  public
     * @return  \Hoa\Core\Data
     */
    public function getData ( ) {

        return $this->_data;
    }

    /**
     * Get output stream.
     *
     * @access  public
     * @return  \Hoa\Stream\IStream\Out
     */
    public function getOutputStream ( ) {

        return $this->_out;
    }

    /**
     * Get type.
     * Please, see the self::TYPE_* constants.
     *
     * @access  public
     * @return  int
     */
    public function getType ( ) {

        return $this->_type;
    }

    /**
     * Add a <?xyl-use?> processing-instruction (only that).
     *
     * @access  public
     * @return  void
     */
    public function addUse ( $href ) {

        $this->_mowgli->insertBefore(
            new \DOMProcessingInstruction(
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
     * @param   \DOMDocument  $ownerDocument    Document that ownes PIs.
     * @return  bool
     * @throw   \Hoa\Xml\Exception
     */
    protected function computeUse ( \DOMDocument $ownerDocument = null,
                                    \DOMDocument $receiptDocument = null ) {

        if(null === $ownerDocument)
            $ownerDocument   = $this->_mowgli;

        if(null === $receiptDocument)
            $receiptDocument = $this->_mowgli;

        $streamClass = get_class($this->getInnerStream());
        $dirname     = dirname($this->getInnerStream()->getStreamName());
        $remove      = self::TYPE_DOCUMENT == $this->getType();
        $hrefs       = array();
        $uses        = array();
        $xpath       = new \DOMXPath($ownerDocument);
        $xyl_use     = $xpath->query('/processing-instruction(\'xyl-use\')');
        unset($xpath);

        $this->computeStylesheet($ownerDocument);

        if(0 === $xyl_use->length)
            return false;

        for($i = 0, $m = $xyl_use->length; $i < $m; ++$i) {

            $item      = $xyl_use->item($i);
            $use       = $item;
            $remove and $ownerDocument->removeChild($item);
            $useParsed = new \Hoa\Xml\Attribute($use->data);

            if(false === $useParsed->attributeExists('href')) {

                unset($useParsed);

                continue;
            }

            $href = $this->computeLink($useParsed->readAttribute('href'), true);
            unset($useParsed);

            if(0 === preg_match('#^(([^:]+://)|([A-Z]:)|/)#', $href))
                $href = $dirname . DS . $href;

            if(false === file_exists($href))
                throw new Exception(
                    'File %s is not found, cannot use it.', 2, $href);

            if(true === in_array($href, $hrefs))
                continue;

            $hrefs[]  = $href;
            $fragment = new self(
                new $streamClass($href),
                $this->_out,
                $this->_interpreter,
                $this->_router
            );

            if(self::TYPE_DEFINITION != $fragment->getType())
                throw new Exception(
                    '%s must only contain <definition> of <yield> (and some ' .
                    '<?xyl-use) elements.', 3, $href);

            foreach($fragment->xpath('//__current_ns:yield[@name]') as $yield)
                $receiptDocument->documentElement->appendChild(
                    $receiptDocument->importNode($yield->readDOM(), true)
                );

            $fragment->computeUse(
                $fragment->readDOM()->ownerDocument,
                $receiptDocument
            );
            $this->_stylesheets = array_merge(
                $this->_stylesheets,
                $fragment->getStylesheets()
            );
        }

        return true;
    }

    /**
     * Compute <yield /> tags.
     *
     * @access  protected
     * @return  void
     */
    protected function computeYielder ( ) {

        $remove = self::TYPE_DOCUMENT == $this->getType();
        $stream = $this->getStream();

        foreach($stream->xpath('//__current_ns:yield[@name]') as $yield) {

            $yieldomized = $yield->readDOM();
            $name        = $yieldomized->getAttribute('name');

            if(true === $remove) {

                $yieldomized->removeAttribute('name');
                $yieldomized->removeAttribute('bind');
            }

            foreach($stream->xpath('//__current_ns:' . $name) as $ciao) {

                $placeholder = $ciao->readDOM();
                $parent      = $placeholder->parentNode;
                $handle      = $yieldomized->cloneNode(true);
                $_yield      = simplexml_import_dom($handle, '\Hoa\Xyl\Element\Basic');
                $_yield->useNamespace(self::NAMESPACE_ID);
                $ciao->useNamespace(self::NAMESPACE_ID);

                if(false === $remove) {

                    $handle->removeAttribute('name');
                    $handle->removeAttribute('bind');
                }

                if(true === $placeholder->hasAttribute('bind'))
                    $handle->setAttribute(
                        'bind',
                        $placeholder->getAttribute('bind')
                    );

                $selects = $_yield->xpath('.//__current_ns:yield[@select]');

                foreach($selects as $select) {

                    $selectdomized = $select->readDOM();
                    $_select       = $select->readAttribute('select');

                    switch(static::getSelector($_select, $matches)) {

                        case self::SELECTOR_PATH:
                            throw new Exception(
                                'Selector %s is not supported in a @select ' .
                                'attribute of the <yield /> component.',
                                4, $_select);
                          break;

                        case self::SELECTOR_QUERY:
                            $_ = \Hoa\Xml\Element\Basic::getCssToXPathInstance();
                            $_->compile(':root ' . $matches[1]);
                            $_select = $_->getXPath();
                          break;

                        case self::SELECTOR_XPATH:
                            $_select = $matches[1];
                    }

                    $xpath = $ciao->xpath('./' . $_select);

                    foreach($xpath ?: array() as $selected)
                        $selectdomized->parentNode->insertBefore(
                            $selected->readDOM(),
                            $selectdomized
                        );

                    $selectdomized->parentNode->removeChild($selectdomized);
                }

                $parent->replaceChild($handle, $placeholder);
            }

            $remove and $yieldomized->parentNode->removeChild($yieldomized);
        }

        return;
    }

    /**
     * Add a <?xyl-overlay?> processing-instruction (only that).
     *
     * @access  public
     * @param   string  $href    Overlay's path.
     * @return  void
     */
    public function addOverlay ( $href ) {

        $this->_mowgli->insertBefore(
            new \DOMProcessingInstruction(
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
     * @param   \DOMDocument  $ownerDocument    Document that ownes PIs.
     * @return  bool
     * @throw   \Hoa\Xml\Exception
     */
    protected function computeOverlay ( \DOMDocument $ownerDocument   = null,
                                        \DOMDocument $receiptDocument = null ) {

        if(null === $ownerDocument)
            $ownerDocument   = $this->_mowgli;

        if(null === $receiptDocument)
            $receiptDocument = $this->_mowgli;

        $streamClass = get_class($this->getInnerStream());
        $dirname     = dirname($this->getInnerStream()->getStreamName());
        $remove      = self::TYPE_DOCUMENT == $this->getType();
        $hrefs       = array();
        $overlays    = array();
        $xpath       = new \DOMXPath($ownerDocument);
        $xyl_overlay = $xpath->query('/processing-instruction(\'xyl-overlay\')');
        unset($xpath);

        if(0 === $xyl_overlay->length)
            return false;

        for($i = 0, $m = $xyl_overlay->length; $i < $m; ++$i) {

            $item          = $xyl_overlay->item($i);
            $overlay       = $item;
            $remove and $ownerDocument->removeChild($item);
            $overlayParsed = new \Hoa\Xml\Attribute($overlay->data);

            if(false === $overlayParsed->attributeExists('href')) {

                unset($overlayParsed);

                continue;
            }

            $href = $this->computeLink(
                $overlayParsed->readAttribute('href'),
                true
            );
            unset($overlayParsed);

            if(0 === preg_match('#^(([^:]+://)|([A-Z]:)|/)#', $href))
                $href = $dirname . DS . $href;

            if(false === file_exists($href))
                throw new Exception(
                    'File %s is not found, cannot use it.', 5, $href);

            if(true === in_array($href, $hrefs))
                continue;

            $hrefs[]  = $href;
            $fragment = new self(
                new $streamClass($href),
                $this->_out,
                $this->_interpreter,
                $this->_router
            );

            if(self::TYPE_OVERLAY != $fragment->getType())
                throw new Exception(
                    '%s must only contain <overlay> (and some <?xyl-overlay) ' .
                    'elements.', 6, $href);

            foreach($fragment->selectChildElements() as $e => $element)
                $this->_computeOverlay(
                    $receiptDocument->documentElement,
                    $receiptDocument->importNode($element->readDOM(), true)
                );

            $fod = $fragment->readDOM()->ownerDocument;

            $this->computeUse    ($fod, $receiptDocument);
            $this->computeOverlay($fod, $receiptDocument);
        }

        return true;
    }

    /**
     * Next step for computing overlay.
     *
     * @access  private
     * @param   \DOMElement  $from    Receiver fragment.
     * @param   \DOMElement  $to      Overlay fragment.
     * @return  void
     */
    private function _computeOverlay ( \DOMElement $from, \DOMElement $to ) {

        if(false === $to->hasAttribute('id'))
            return $this->_computeOverlayPosition($from, $to);

        $xpath = new \DOMXPath($from->ownerDocument);
        $query = $xpath->query('//*[@id="' . $to->getAttribute('id') . '"]');

        if(0 === $query->length)
            if($from->parentNode == $this->_mowgli) // reference component
                return null;
            else
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
     * @param   \DOMElement  $from    Receiver fragment.
     * @param   \DOMElement  $to      Overlay fragment.
     * @return  void
     */
    private function _computeOverlayPosition ( \DOMElement $from,
                                               \DOMElement $to ) {

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
                $replace[] = $e;
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
     * Add a <?xyl-stylesheet?> processing-instruction (only that).
     *
     * @access  public
     * @param   string  $href    Stylesheet's path.
     * @return  void
     */
    public function addStylesheet ( $href ) {

        $this->_mowgli->insertBefore(
            new \DOMProcessingInstruction(
                'xyl-stylesheet',
                'href="' . str_replace('"', '\"', $href) . '"'
            ),
            $this->_mowgli->firstChild
        );

        return;
    }

    /**
     * Compute <?xyl-stylesheet?> processing-instruction.
     *
     * @access  protected
     * @param   \DOMDocument  $ownerDocument    Document that ownes PIs.
     * @return  void
     */
    protected function computeStylesheet ( \DOMDocument $ownerDocument ) {

        $xpath     = new \DOMXPath($ownerDocument);
        $xyl_style = $xpath->query('/processing-instruction(\'xyl-stylesheet\')');
        unset($xpath);

        if(0 === $xyl_style->length)
            return;

        for($i = 0, $m = $xyl_style->length; $i < $m; ++$i) {

            $item        = $xyl_style->item($i);
            $styleParsed = new \Hoa\Xml\Attribute($item->data);

            if(true === $styleParsed->attributeExists('href')) {

                $href = $this->computeLink(
                    $styleParsed->readAttribute('href'),
                    true
                );

                if(true === $styleParsed->attributeExists('position')) {

                    $position = max(0, (int) $this->_xe->evaluate(str_replace(
                        'last()',
                        ($k = key($this->_stylesheets)) ? $k + 1 : 0,
                        $styleParsed->readAttribute('position')
                    )));

                    if(isset($this->_stylesheets[$position])) {

                        $handle = array();

                        foreach($this->_stylesheets as $i => $foo)
                            if($position > $i) {

                                $handle[$i] = $foo;
                                unset($this->_stylesheets[$i]);
                            }
                            else
                                break;

                        $handle[$position] = $href;

                        foreach($this->_stylesheets as $i => $foo) {

                            if($i === $position) {

                                $handle[$position = $i + 1] = $foo;
                                unset($this->_stylesheets[$i]);
                            }
                            else
                                break;
                        }

                        $this->_stylesheets = $handle + $this->_stylesheets;
                    }
                    else {

                        $this->_stylesheets[$position] = $href;
                        ksort($this->_stylesheets, SORT_NUMERIC);
                    }
                }
                else
                    $this->_stylesheets[] = $href;
            }

            $ownerDocument->removeChild($item);
            unset($styleParsed);
        }

        return;
    }

    protected function computeConcrete ( Interpreter $interpreter = null ) {

        if(null !== $this->_concrete)
            return;

        if(null === $interpreter)
            $interpreter = $this->_interpreter;

        $rank = $interpreter->getRank();
        $root = $this->getStream();
        $name = strtolower($root->getName());

        if(false === array_key_exists($name, $rank))
            throw new Exception(
                'Cannot create the concrete tree because the root <%s> is ' .
                'unknown from the rank.', 7, $name);

        $class           = $rank[$name];
        $this->_concrete = new $class($root, $this, $rank, self::NAMESPACE_ID);

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

        if(true === $this->isInnerOpened())
            return;

        if(null === $this->_concrete)
            throw new Exception(
                'Cannot compute the data binding before building the ' .
                'concrete tree.', 8);

        $data = $this->getData()->toArray();

        return $this->_concrete->computeDataBinding($data);
    }

    /**
     * Compute link.
     *
     * @access  public
     * @param   string  $link    Link.
     * @param   bool    $late    If hoa:// resolving is postponed.
     * @return  string
     */
    public function computeLink ( $link, $late = false ) {

        // Router.
        if(0 != preg_match('#^@(?:(?:([^:]+):(.*))|([^$]+))$#', $link, $matches)) {

            $router = $this->getRouter();

            if(null === $router)
                return $link;

            if(isset($matches[3]))
                return $router->unroute($matches[3]);

            $id = $matches[1];
            $kv = array();

            foreach(explode('&', $matches[2]) as $value) {

                $handle                    = explode('=', $value);
                $kv[urldecode($handle[0])] = urldecode($handle[1]);
            }

            return $router->unroute($id, $kv);
        }

        // hoa://.
        if('hoa://' == substr($link, 0, 6))
            return $this->resolve($link, $late);

        return $link;
    }

    /**
     * Interprete XYL as…
     *
     * @access  public
     * @param   \Hoa\Xyl\Interpreter  $interpreter    Interpreter.
     * @return  \Hoa\Xyl
     * @throws  \Hoa\Xyl\Exception
     */
    public function interprete ( Interpreter $interpreter = null ) {

        $this->computeUse();
        $this->computeOverlay();
        $this->computeYielder();
        $this->computeConcrete($interpreter);
        $this->computeDataBinding();

        return $this;
    }

    /**
     * Run the render.
     *
     * @access  public
     * @return  string
     */
    public function render ( ) {

        if(null === $this->_concrete)
            $this->interprete();

        return $this->_concrete->render($this->_out);
    }

    /**
     * Open a document with the same context as this one.
     *
     * @access  public
     * @param   string  $streamName    Stream name.
     * @return  \Hoa\Xyl
     * @throw   \Hoa\Xyl\Exception
     */
    public function open ( $streamName ) {

        $in              = get_class($this->getInnerStream());
        $new             = new self(
            new $in($streamName),
            $this->getStream(),
            $this->_interpreter,
            $this->getRouter()
        );
        $new->_innerOpen = true;

        return $new->interprete();
    }

    /**
     * Get the concrete tree.
     *
     * @access  public
     * @return  \Hoa\Xyl\Element\Concrete
     */
    public function getConcrete ( ) {

        return $this->_concrete;
    }

    /**
     * Set theme.
     *
     * @access  public
     * @param   string  $theme    Theme.
     * @return  string
     */
    public function setTheme ( $theme ) {

        $old = $this->getTheme();
        $this->getParameters()->setKeyword('theme', $theme);

        return $old;
    }

    /**
     * Get theme.
     *
     * @access  public
     * @return  string
     */
    public function getTheme ( ) {

        return $this->getParameters()->getKeyword('theme');
    }

    /**
     * Get all stylesheets in <?xyl-stylesheet?>
     *
     * @access  public
     * @return  array
     */
    public function getStylesheets ( ) {

        return $this->_stylesheets;
    }

    /**
     * Set router.
     *
     * @access  public
     * @param   \Hoa\Router\Http  $router    Router.
     * @return  \Hoa\Router\Http
     */
    public function setRouter ( \Hoa\Router\Http $router ) {

        $old           = $this->_router;
        $this->_router = $router;

        return $old;
    }

    /**
     * Get router.
     *
     * @access  public
     * @return  \Hoa\Router\Http
     */
    public function getRouter ( ) {

        return $this->_router;
    }

    /**
     * Whether the document is open from the constructor or from the
     * self::open() method.
     *
     * @access  public
     * @return  bool
     */
    public function isInnerOpened ( ) {

        return $this->_innerOpen;
    }

    /**
     * Resolve some hoa:// pathes:
     *     * hoa://Library/Xyl/ to hoa://Library/Xyl[i];
     *     * hoa://Application/Public/ to hoa://Application/Public/<theme>/.
     *
     * @access  public
     * @param   string  $hoa    hoa:// path.
     * @param   bool    $late   If hoa:// real resolving is postponed.
     * @return  string
     */
    public function resolve ( $hoa, $late = false ) {

        if(0 !== preg_match('#^hoa://Library/Xyl(/.*|$)#', $hoa, $matches))
            $hoa ='hoa://Library/Xyl[' . $this->_i . ']' . $matches[1];

        if(0 !== preg_match('#^hoa://Application/Public(/.*)#', $hoa, $matches))
            $hoa = 'hoa://Application/Public/' .
                   $this->getParameters()->getFormattedParameter('theme') .
                   $matches[1];

        if(true === $late)
            return $hoa;

        return resolve($hoa);
    }

    /**
     * Get selector type.
     *
     * @access  public
     * @param   string  $selector    Selector.
     * @return  int
     * @throw   \Hoa\Xyl\Exception
     */
    public static function getSelector ( $selector, &$matches = false ) {

        // ?a/b/c
        // ?p:a/b/c
        // ?path:a/b/c
        if(0 !== preg_match('#^\?(?:p(?:ath)?:)?([^:]+)$#i', $selector, $matches))
            return self::SELECTOR_PATH;

        // ?q:a b c
        // ?query:a b c
        elseif(0 !== preg_match('#^\?q(?:uery)?:(.*)?$#i', $selector, $matches))
            return self::SELECTOR_QUERY;

        // ?x:a/b/c
        // ?xpath:a/b/c
        elseif(0 !== preg_match('#^\?x(?:path)?:(.*)?$#i', $selector, $matches))
            return self::SELECTOR_XPATH;

        throw new Exception(
            'Selector %s is not a valid selector.', 9, $selector);
    }

    /**
     * Destruct XYL object.
     *
     * @access  public
     * @return  void
     */
    public function __destruct ( ) {

        \Hoa\Core::getInstance()
            ->getProtocol()
            ->getComponent('Library')
            ->removeComponent('Xyl[' . $this->_i . ']');

        return;
    }
}

/**
 * Class \Hoa\Xyl\_Protocol.
 *
 * hoa://Library/Xyl component.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class _Protocol extends \Hoa\Core\Protocol {

    /**
     * Fragment to insert in the path.
     *
     * @var \Hoa\Xyl\_Protocol string
     */
    protected $_fragment = null;



    /**
     * Construct a hoa://Library/Xyl component.
     *
     * @access  public
     * @param   string  $name        Component name (normally, Xyl[i]).
     * @param   string  $fragment    Fragment to insert in the path (normally,
     *                               the resource path).
     * @return  void
     */
    public function __construct ( $name, $fragment ) {

        parent::__construct($name);
        $this->_fragment = $fragment;

        return;
    }

    /**
     * Queue of the component.
     *
     * @access  public
     * @param   string  $queue    Queue of the component.
     * @return  string
     */
    public function reach ( $queue ) {

        return __DIR__ . DS . $this->_fragment . $queue;
    }
}

}

namespace {

from('Hoa') -> import('Xyl.Interpreter.Common.Debug');
event('hoa://Event/Exception')
    ->attach('\Hoa\Xyl\Interpreter\Common\Debug', 'receiveException');

}
