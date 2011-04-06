<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Xyl\Element\Executable
 */
-> import('Xyl.Element.Executable')

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
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class          Xyl
    extends    \Hoa\Xml
    implements Element,
               \Hoa\View\Viewable,
               \Hoa\Core\Parameterizable {

    /**
     * XYL's namespace.
     *
     * @const string
     */
    const NAMESPACE_ID = 'http://hoa-project.net/xyl/xylophone';

    /**
     * The \Hoa\Controller\Dispatcher parameters.
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
     * @var \Hoa\Controller\Router object
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
     * Interprete a stream as XYL.
     *
     * @access  public
     * @param   \Hoa\Stream\IStream\In     $in             Stream to interprete
     *                                                     as XYL.
     * @param   \Hoa\Stream\IStream\Out    $out            Stream for rendering.
     * @param   \Hoa\Xyl\Interpreter       $interpreter    Interpreter.
     * @param   \Hoa\Controller\Router     $router         Router.
     * @param   array                      $parameters     Parameters.
     * @return  void
     * @throw   \Hoa\Xml\Exception
     */
    public function __construct ( \Hoa\Stream\IStream\In  $in,
                                  \Hoa\Stream\IStream\Out $out,
                                  Interpreter             $interpreter,
                                  \Hoa\Controller\Router  $router = null,
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
                    'theme'            => '(:theme:lU:)',
                    'html5.css'        => 'hoa://Application/Public/(:%theme:)/Css/',
                    'html5.font'       => 'hoa://Application/Public/(:%theme:)/Font/',
                    'html5.image'      => 'hoa://Application/Public/(:%theme:)/Image/',
                    'html5.javascript' => 'hoa://Application/Public/(:%theme:)/Javascript/',
                    'html5.video'      => 'hoa://Application/Public/(:%theme:)/Video/'
                )
            );
            $this->setParameters($parameters);
        }

        $this->_i           = self::$_ci++;
        $this->_xe          = new \DOMXPath(new \DOMDocument());
        $this->_data        = new \Hoa\Core\Data();
        $this->_out         = $out;
        $this->_interpreter = $interpreter;
        $this->_router      = $router;
        $this->_mowgli      = $this->getStream()->readDOM()->ownerDocument;

        $this->useNamespace(self::NAMESPACE_ID);
        \Hoa\Core::getInstance()
                ->getProtocol()
                ->getComponent('Library')
                ->addComponent(new _Protocol(
                    'Xyl[' . $this->_i . ']',
                    'Interpreter' . DS .$this->_interpreter->getResourcePath()
                ));

        if(1 === self::$_ci) {

            if(null !== $router && false === $router->ruleExists('_css'))
                $router->addPrivateRule(
                    '_css',
                    'Public/Css/(?<theme>.*)/(?<sheet>.*)'
                );

            from('Hoa') -> import('Xyl.Interpreter.Common.Debug');
            event('hoa://Event/Exception')
                ->attach('\Hoa\Xyl\Interpreter\Common\Debug', 'receiveException');
        }

        return;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in    Parameters to set.
     * @return  void
     * @throw   \Hoa\Core\Exception
     */
    public function setParameters ( Array $in ) {

        return self::$_parameters->setParameters($this, $in);
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Core\Exception
     */
    public function getParameters ( ) {

        return self::$_parameters->getParameters($this);
    }

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function setParameter ( $key, $value ) {

        return self::$_parameters->setParameter($this, $key, $value);
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function getParameter ( $key ) {

        return self::$_parameters->getParameter($this, $key);
    }

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function getFormattedParameter ( $key ) {

        return self::$_parameters->getFormattedParameter($this, $key);
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
            $ownerDocument->removeChild($item);
            $useParsed = new \Hoa\Xml\Attribute($use->data);

            if(false === $useParsed->attributeExists('href')) {

                unset($useParsed);

                continue;
            }

            $href = $useParsed->readAttribute('href');
            unset($useParsed);

            if(0 === preg_match('#^([^:]+://)|([A-Z]:)|/#', $href))
                $href = $dirname . DS . $href;

            if(false === file_exists($href))
                throw new Exception(
                    'File %s is not found, cannot use it.', 0, $href);

            if(true === in_array($href, $hrefs))
                continue;

            $hrefs[]  = $href;
            $fragment = new self(
                new $streamClass($href),
                $this->_out,
                $this->_interpreter,
                $this->_router
            );

            if('definition' !== $fragment->getName())
                throw new Exception(
                    '%s must only contain <definition> of <yield> (and some ' .
                    '<?xyl-use) elements.', 1, $href);

            foreach($fragment->xpath('//__current_ns:yield[@name]') as $yield)
                $receiptDocument->documentElement->appendChild(
                    $receiptDocument->importNode($yield->readDOM(), true)
                );

            $fragment->computeUse(
                $fragment->readDOM()->ownerDocument,
                $receiptDocument
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
    protected function computeOverlay ( \DOMDocument $ownerDocument = null,
                                        \DOMDocument $receiptDocument = null ) {

        if(null === $ownerDocument)
            $ownerDocument   = $this->_mowgli;

        if(null === $receiptDocument)
            $receiptDocument = $this->_mowgli;

        $streamClass = get_class($this->getInnerStream());
        $dirname     = dirname($this->getInnerStream()->getStreamName());
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
            $ownerDocument->removeChild($item);
            $overlayParsed = new \Hoa\Xml\Attribute($overlay->data);

            if(false === $overlayParsed->attributeExists('href')) {

                unset($overlayParsed);

                continue;
            }

            $href = $overlayParsed->readAttribute('href');
            unset($overlayParsed);

            if(0 === preg_match('#^([^:]+://)|([A-Z]:)|/#', $href))
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

            if('overlay' !== $fragment->getName())
                throw new Exception(
                    '%s must only contain <overlay> (and some <?xyl-overlay) ' .
                    'elements.', 3, $href);

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

            if(true === $styleParsed->attributeExists('href'))
                $this->_stylesheets[] = $styleParsed->readAttribute('href');

            $ownerDocument->removeChild($item);
            unset($styleParsed);
        }

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
            throw new Exception(
                'Cannot compute the data binding before building the ' .
                'concrete tree.', 4);

        $data = $this->getData()->toArray();

        return $this->_concrete->computeDataBinding($data);
    } 

    /**
     * Interprete XYL as…
     *
     * @access  public
     * @param   \Hoa\Xyl\Interpreter  $interpreter    Interpreter.
     * @return  void
     * @throws  \Hoa\Xyl\Exception
     */
    public function interprete ( Interpreter $interpreter = null ) {

        if(null === $interpreter)
            $interpreter = $this->_interpreter;

        $this->computeUse();
        $this->computeOverlay();
        $this->computeYielder();

        $rank = $interpreter->getRank();
        $root = $this->getStream();
        $name = strtolower($root->getName());

        if(false === array_key_exists($name, $rank))
            throw new Exception(
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
            $this->interprete();

        return $this->_concrete->render($this->_out);
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
        self::$_parameters->setKeyword($this, 'theme', $theme);

        return $old;
    }

    /**
     * Get theme.
     *
     * @access  public
     * @return  string
     */
    public function getTheme ( ) {
        
        return self::$_parameters->getKeyword($this, 'theme');
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
     * @param   \Hoa\Controller\Router  $router    Router.
     * @return  \Hoa\Controller\Router
     */
    public function setRouter ( \Hoa\Controller\Router $router ) {

        $old           = $this->_router;
        $this->_router = $router;

        return $old;
    }

    /**
     * Get router.
     *
     * @access  public
     * @return  \Hoa\Controller\Router
     */
    public function getRouter ( ) {

        return $this->_router;
    }

    /**
     * Resolve some hoa:// pathes:
     *     * hoa://Library/Xyl/ to hoa://Library/Xyl[i];
     *     * hoa://Application/Public/ to hoa://Application/Public/<theme>/.
     *
     * @access  public
     * @param   string  $hoa    hoa:// path.
     * @return  string
     */
    public function resolve ( $hoa ) {

        if(0 !== preg_match('#^hoa://Library/Xyl(/.*|$)#', $hoa, $matches))
            return resolve(
                'hoa://Library/Xyl[' . $this->_i . ']' . $matches[1]
            );

        if(0 !== preg_match('#^hoa://Application/Public(/.*)#', $hoa, $matches))
            return resolve(
                'hoa://Application/Public/' .
                $this->getFormattedParameter('theme') . $matches[1]
            );

        return resolve($hoa);
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
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
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
