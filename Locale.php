<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
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

namespace Hoa\Locale;

use Hoa\Core;

/**
 * Class \Hoa\Locale.
 *
 * Deduce, extract and format locales from different localizers.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Locale {

    /**
     * Type: langtag (the real one).
     *
     * @const int
     */
    const TYPE_LANGTAG       = 0;

    /**
     * Type: private use.
     *
     * @const int
     */
    const TYPE_PRIVATEUSE    = 1;

    /**
     * Type: grandfathered.
     *
     * @const int
     */
    const TYPE_GRANDFATHERED = 2;

    /**
     * Default locale.
     *
     * @var \Hoa\Locale string
     */
    protected static $_default = null;

    /**
     * Localizer.
     *
     * @var \Hoa\Locale\Localizer object
     */
    protected $_localizer      = null;

    /**
     * Type of locale. Please, see self::TYPE_* constants.
     *
     * @var \Hoa\Locale int
     */
    protected $_type           = 0;

    /**
     * Language.
     *
     * @var \Hoa\Locale string
     */
    protected $_language       = null;

    /**
     * Script.
     *
     * @var \Hoa\Locale string
     */
    protected $_script         = null;

    /**
     * Region.
     *
     * @var \Hoa\Locale string
     */
    protected $_region         = null;

    /**
     * Variants.
     *
     * @var \Hoa\Locale array
     */
    protected $_variant        = null;

    /**
     * Extensions.
     *
     * @var \Hoa\Locale array
     */
    protected $_extension      = null;

    /**
     * Private use.
     *
     * @var \Hoa\Locale mixed
     */
    protected $_privateuse     = null;

    /**
     * Grandfathered.
     *
     * @var \Hoa\Locale string
     */
    protected $_grandfathered  = null;



    /**
     * Compute the locale from a localizer.
     *
     * @access  public
     * @param   mixed  $localizer    Localizer or locale.
     * @return  void
     */
    public function __construct ( $localizer = null ) {

        if(!is_object($localizer))
            $localizer = new Localizer\Coerce($localizer);

        $this->setLocalizer($localizer);

        return;
    }

    /**
     * Set default locale.
     *
     * @access  public
     * @param   string  $locale    Locale.
     * @return  string
     */
    public static function setDefault ( $locale ) {

        $old              = static::$_default;
        static::$_default = $locale;

        return $old;
    }

    /**
     * Get default locale.
     *
     * @access  public
     * @return  string
     */
    public static function getDefault ( ) {

        return static::$_default;
    }

    /**
     * Set localizer.
     *
     * @access  public
     * @param   \Hoa\Locale\Localizer  $localizer    Localizer.
     * @return  \Hoa\Locale\Localizer
     */
    public function setLocalizer ( Localizer $localizer ) {

        $this->reset();

        $old              = $this->_localizer;
        $this->_localizer = $localizer;

        $this->computeLocale();

        return $old;
    }

    /**
     * Get localizer.
     *
     * @access  public
     * @return  \Hoa\Locale\Localizer
     */
    public function getLocalizer ( ) {

        return $this->_localizer;
    }

    /**
     * Compute locale.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Locale\Exception
     */
    protected function computeLocale ( ) {

        $locale = $this->getLocalizer()->getLocale() ?: static::getDefault();

        if(empty($locale))
            throw new Exception(
                'No locale was found.', 0);

        $parsed = static::parse($locale);

        if(false === $parsed)
            throw new Exception(
                'Locale %s is not well-formed.', 1, $locale);

        if(isset($parsed['grandfathered'])) {

            $this->_type          = static::TYPE_GRANDFATHERED;
            $this->_grandfathered = $parsed['grandfathered'];
        }
        elseif(isset($parsed['privateuse'])) {

            $this->_type       = static::TYPE_PRIVATEUSE;
            $this->_privateuse = $parsed['privateuse'];
        }
        else {

            $this->_type = static::TYPE_LANGTAG;
            list(
                $this->_language,
                $this->_script,
                $this->_region,
                $this->_variant,
                $this->_extension,
                $this->_privateuse
            ) = array_values($parsed['langtag']);
        }

        return;
    }

    /**
     * Parse a local.
     * Please, see RFC4646, 2.1 Syntax.
     *
     * @access  public
     * @param   string  $locale    Locale.
     * @return  array
     */
    public static function parse ( $locale ) {

        // RFC4646
        $match = preg_match(
            '#^
             (
               (?<r_langtag>
                 (?<language>[a-z]{2,3})
                 (?<script>\-[a-z]{4})?
                 (?<region>\-(?:[a-z]{2}|[0-9]{4}))?
                 (?<variant>(?:\-(?:[a-z]{2}|[0-9]{3}))+)?
                 (?<extension>(?:\-(?:[a-wy-z]|\d)\-[a-z0-9]{2,8})+)?
                 (?<privateuse>\-x\-[a-z0-9]{1,8})?
               )
             | (?<r_privateuse>x\-[a-z0-9]{1,8})
             | (?<r_grandfathered>[a-z]{1,3}(\-[a-z0-9]{2,8}){1,2})
             )
             $#ix',
            $locale,
            $matches
        );

        if(0 === $match)
            return false;

        if(isset($matches['r_grandfathered']))
            return [
                'grandfathered' => $matches['r_grandfathered']
            ];

        if(isset($matches['r_privateuse']))
            return [
                'privateuse' => substr($matches['r_privateuse'], 2)
            ];

        $out = [
            'language'   => $matches['language'],
            'script'     => null,
            'region'     => null,
            'variant'    => [],
            'extension'  => [],
            'privateuse' => null
        ];

        if(!empty($matches['script']))
            $out['script'] = substr($matches['script'], 1);

        if(!empty($matches['region']))
            $out['region'] = substr($matches['region'], 1);

        if(!empty($matches['variant']))
            $out['variant'] = explode('-', substr($matches['variant'], 1));

        if(!empty($matches['extension'])) {

            $handle = preg_split(
                '/-(?=.-)/',
                $matches['extension'],
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            foreach($handle as $value) {

                list($extensionName, $extensionValue) = explode('-', $value);
                $out['extension'][$extensionName]     = $extensionValue;
            }
        }

        if(!empty($matches['privateuse']))
            $out['privateuse'] = substr($matches['privateuse'], 3);

        return ['langtag' => $out];
    }

    /**
     * Get type. Please, see static::TYPE_* constants.
     *
     * @access  public
     * @return  int
     */
    public function getType ( ) {

        return $this->_type;
    }

    /**
     * Get language.
     *
     * @access  public
     * @return  string
     */
    public function getLanguage ( ) {

        return $this->_language;
    }

    /**
     * Get script.
     *
     * @access  public
     * @return  string
     */
    public function getScript ( ) {

        return $this->_script;
    }

    /**
     * Get region.
     *
     * @access  public
     * @return  string
     */
    public function getRegion ( ) {

        return $this->_region;
    }

    /**
     * Get all variants.
     *
     * @access  public
     * @return  array
     */
    public function getVariants ( ) {

        return $this->_variant;
    }

    /**
     * Get extensions.
     *
     * @access  public
     * @return  array
     */
    public function getExtensions ( ) {

        return $this->_extension;
    }

    /**
     * Get private use.
     *
     * @access  public
     * @return  mixed
     */
    public function getPrivateUse ( ) {

        return $this->_privateuse;
    }

    /**
     * Get grand-fathered value.
     *
     * @access  public
     * @return  string
     */
    public function getGrandfathered ( ) {

        return $this->_grandfathered;
    }

    /**
     * Reset the object.
     *
     * @access  protected
     * @return  void
     */
    protected function reset ( ) {

        $class             = new \ReflectionClass(get_class($this));
        $object            = new \ReflectionObject($this);
        $defaultProperties = $class->getDefaultProperties();
        $properties        = $object->getProperties();

        foreach($properties as $property) {

            $name = $property->getName();

            if('_default' === $name)
                continue;

            $property->setAccessible(true);
            $property->setValue(
                $this,
                array_key_exists($name, $defaultProperties)
                    ? $defaultProperties[$name]
                    : null
            );
        }

        return;
    }
}

/**
 * Flex entity.
 */
Core\Consistency::flexEntity('Hoa\Locale\Locale');
