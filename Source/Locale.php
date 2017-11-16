<?php

declare(strict_types=1);

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

use Hoa\Consistency;

/**
 * Class \Hoa\Locale.
 *
 * Deduce, extract and format locales from different localizers.
 */
class Locale
{
    /**
     * Type: langtag (the real one).
     */
    public const TYPE_LANGTAG       = 0;

    /**
     * Type: private use.
     */
    public const TYPE_PRIVATEUSE    = 1;

    /**
     * Type: grandfathered.
     */
    public const TYPE_GRANDFATHERED = 2;

    /**
     * Default locale.
     */
    protected static $_default = null;

    /**
     * Localizer.
     */
    protected $_localizer      = null;

    /**
     * Type of locale. Please, see self::TYPE_* constants.
     */
    protected $_type           = 0;

    /**
     * Language.
     */
    protected $_language       = null;

    /**
     * Script.
     */
    protected $_script         = null;

    /**
     * Region.
     */
    protected $_region         = null;

    /**
     * Variants.
     */
    protected $_variant        = null;

    /**
     * Extensions.
     */
    protected $_extension      = null;

    /**
     * Private use.
     */
    protected $_privateuse     = null;

    /**
     * Grandfathered.
     */
    protected $_grandfathered  = null;



    /**
     * Compute the locale from a localizer.
     */
    public function __construct($localizer = null)
    {
        if (!is_object($localizer)) {
            $localizer = new Localizer\Coerce($localizer);
        }

        $this->setLocalizer($localizer);

        return;
    }

    /**
     * Set default locale.
     */
    public static function setDefault(string $locale): ?string
    {
        $old              = static::$_default;
        static::$_default = $locale;

        return $old;
    }

    /**
     * Get default locale.
     */
    public static function getDefault(): ?string
    {
        return static::$_default;
    }

    /**
     * Set localizer.
     */
    public function setLocalizer(Localizer $localizer): ?Localizer
    {
        $this->reset();

        $old              = $this->_localizer;
        $this->_localizer = $localizer;

        $this->computeLocale();

        return $old;
    }

    /**
     * Get localizer.
     */
    public function getLocalizer(): ?Localizer
    {
        return $this->_localizer;
    }

    /**
     * Compute locale.
     */
    protected function computeLocale(): void
    {
        $locale = $this->getLocalizer()->getLocale() ?: static::getDefault();

        if (empty($locale)) {
            throw new Exception('No locale was found.', 0);
        }

        $parsed = static::parse($locale);

        if (empty($parsed)) {
            throw new Exception('Locale %s is not well-formed.', 1, $locale);
        }

        if (isset($parsed['grandfathered'])) {
            $this->_type          = static::TYPE_GRANDFATHERED;
            $this->_grandfathered = $parsed['grandfathered'];
        } elseif (isset($parsed['privateuse'])) {
            $this->_type       = static::TYPE_PRIVATEUSE;
            $this->_privateuse = $parsed['privateuse'];
        } else {
            $this->_type = static::TYPE_LANGTAG;
            [
                'language'   => $this->_language,
                'script'     => $this->_script,
                'region'     => $this->_region,
                'variant'    => $this->_variant,
                'extension'  => $this->_extension,
                'privateuse' => $this->_privateuse
            ] = $parsed['langtag'];
        }
    }

    /**
     * Parse a local.
     * Please, see RFC4646, 2.1 Syntax.
     */
    public static function parse(string $locale): array
    {
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

        if (0 === $match) {
            return [];
        }

        if (isset($matches['r_grandfathered'])) {
            return [
                'grandfathered' => $matches['r_grandfathered']
            ];
        }

        if (isset($matches['r_privateuse'])) {
            return [
                'privateuse' => substr($matches['r_privateuse'], 2)
            ];
        }

        $out = [
            'language'   => $matches['language'],
            'script'     => null,
            'region'     => null,
            'variant'    => [],
            'extension'  => [],
            'privateuse' => null
        ];

        if (!empty($matches['script'])) {
            $out['script'] = substr($matches['script'], 1);
        }

        if (!empty($matches['region'])) {
            $out['region'] = substr($matches['region'], 1);
        }

        if (!empty($matches['variant'])) {
            $out['variant'] = explode('-', substr($matches['variant'], 1));
        }

        if (!empty($matches['extension'])) {
            $handle = preg_split(
                '/-(?=.-)/',
                $matches['extension'],
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            foreach ($handle as $value) {
                [$extensionName, $extensionValue]     = explode('-', $value);
                $out['extension'][$extensionName]     = $extensionValue;
            }
        }

        if (!empty($matches['privateuse'])) {
            $out['privateuse'] = substr($matches['privateuse'], 3);
        }

        return ['langtag' => $out];
    }

    /**
     * Get type. Please, see static::TYPE_* constants.
     */
    public function getType(): int
    {
        return $this->_type;
    }

    /**
     * Get language.
     */
    public function getLanguage(): ?string
    {
        return $this->_language;
    }

    /**
     * Get script.
     */
    public function getScript(): ?string
    {
        return $this->_script;
    }

    /**
     * Get region.
     */
    public function getRegion(): ?string
    {
        return $this->_region;
    }

    /**
     * Get all variants.
     */
    public function getVariants(): ?array
    {
        return $this->_variant;
    }

    /**
     * Get extensions.
     */
    public function getExtensions(): ?array
    {
        return $this->_extension;
    }

    /**
     * Get private use.
     */
    public function getPrivateUse()
    {
        return $this->_privateuse;
    }

    /**
     * Get grand-fathered value.
     */
    public function getGrandfathered(): ?string
    {
        return $this->_grandfathered;
    }

    /**
     * Reset the object.
     */
    protected function reset(): void
    {
        $class             = new \ReflectionClass(get_class($this));
        $object            = new \ReflectionObject($this);
        $defaultProperties = $class->getDefaultProperties();
        $properties        = $object->getProperties();

        foreach ($properties as $property) {
            $name = $property->getName();

            if ('_default' === $name) {
                continue;
            }

            $property->setAccessible(true);
            $property->setValue(
                $this,
                array_key_exists($name, $defaultProperties)
                    ? $defaultProperties[$name]
                    : null
            );
        }
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity(Locale::class);
