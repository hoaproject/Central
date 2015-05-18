<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Hoa community. All rights reserved.
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

namespace Hoa\Mime;

use Hoa\Core;
use Hoa\Stream;

/**
 * Class \Hoa\Mime.
 *
 * Get informations relative to MIME (media, type, extension, other extensions)
 * about a stream.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Mime implements Core\Parameter\Parameterizable
{
    /**
     * Structure: media/type part.
     *
     * @const int
     */
    const STRUCTURE_MEDIA_TYPE = 0;

    /**
     * Structure: extension part.
     *
     * @const int
     */
    const STRUCTURE_EXTENSION  = 1;

    /**
     * Parse: media index.
     *
     * @const int
     */
    const MIME_MEDIA           = 0;

    /**
     * Parse: type index.
     *
     * @const int
     */
    const MIME_TYPE            = 1;

    /**
     * Parameters.
     *
     * @var \Hoa\Core\Parameter
     */
    private $_parameters        = null;

    /**
     * Computed magic file.
     * Structure:
     *     array(
     *         structure_media_type => array(
     *             media => array(
     *                 type => array(extensions)
     *             )
     *         ),
     *         structure_extension  => array(
     *             extension => media/type
     *         )
     *     )
     *
     * @var array
     */
    protected static $_computed = null;

    /**
     * Extension.
     *
     * @var string
     */
    protected $_extension       = null;

    /**
     * MIME (media/type).
     *
     * @var string
     */
    protected $_mime            = null;

    /**
     * Media.
     *
     * @var string
     */
    protected $_media           = null;

    /**
     * Type.
     *
     * @var string
     */
    protected $_type            = null;



    /**
     * Get informations relative to MIME about a stream.
     *
     * @param   \Hoa\Stream  $stream        Stream to study.
     * @param   array        $parameters    Parameters.
     * @return  void
     * @throws  \Hoa\Mime\Exception
     */
    public function __construct(Stream $stream, Array $parameters = [])
    {
        $this->_parameters = new Core\Parameter(
            $this,
            [],
            [
                'magic' => null
            ]
        );
        $this->_parameters->setParameters($parameters);
        $magic = $this->_parameters->getParameter('magic');

        if (null === $magic) {
            $this->_parameters->setParameter(
                'magic',
                'hoa://Library/Mime/Mime.types'
            );
            $magic = $this->_parameters->getParameter('magic');
        }

        if (null === static::$_computed) {
            static::compute($magic);
        }

        $this->_find($stream);

        return;
    }

    /**
     * Get parameters.
     *
     * @return  \Hoa\Core\Parameter
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Compute a magic file.
     *
     * @param   string  $magic    Magic file to compute.
     * @return  void
     * @throws  \Hoa\Mime\Exception
     */
    public static function compute($magic = null)
    {
        if (null === $magic) {
            $magic = 'hoa://Library/Mime/Mime.types';
        }

        if (!file_exists($magic)) {
            throw new Exception(
                'Magic file %s does not exist.',
                0,
                $magic
            );
        }

        static::$_computed = [
            self::STRUCTURE_MEDIA_TYPE => [],
            self::STRUCTURE_EXTENSION  => []
        ];
        $s_media_type = &static::$_computed[self::STRUCTURE_MEDIA_TYPE];
        $s_extension  = &static::$_computed[self::STRUCTURE_EXTENSION];
        $splashed     = file($magic);
        $max          = count($splashed) - 1;

        foreach ($splashed as $i => $line) {
            $line = trim($line);

            if (empty($line) || '#' == $line[0]) {
                continue;
            }

            if (false === strpos($line, "\t")) {
                $mime       = $line;
                $extensions = null;
            } else {
                list($mime, $extensions) = preg_split('#' . "\t" . '+#', $line);
            }

            try {
                list($media, $type) = static::parseMime($mime);
            } catch (Exception $exception) {
                $message = null;
                $m       = min($max, $i + 3);
                $foo     = strlen((string) $m);

                for (
                    $e = max(0, $i - 3);
                    $e <= $m;
                    ++$e
                ) {
                    $message .= "\n" . sprintf('%' . $foo . 'd', $e) . '. ';

                    if ($i == $e) {
                        $message .= '➜  ';
                    } else {
                        $message .= '   ';
                    }

                    $message .= trim($splashed[$e]);
                }

                throw new Exception(
                    'Magic file %s seems to be corrupted (at line %d). ' .
                    'You should take a look at this piece of code:' .
                    $message,
                    1,
                    [$magic, $i],
                    $exception
                );
            }

            if (!isset($s_media_type[$media])) {
                $s_media_type[$media] = [];
            }

            if (null == $extensions) {
                $s_media_type[$media][$type] = null;

                continue;
            }

            $extensions                  = explode(' ', $extensions);
            $s_media_type[$media][$type] = $extensions;

            foreach ($extensions as $extension) {
                $s_extension[$extension] = $mime;
            }
        }

        return;
    }

    /**
     * Check if extension exists in the magic file.
     *
     * @param   string  $extension    Extension to check.
     * @return  bool
     */
    public static function extensionExists($extension)
    {
        if (null === static::$_computed) {
            static::compute();
        }

        return isset(static::$_computed[self::STRUCTURE_EXTENSION][$extension]);
    }

    /**
     * Get extensions from a MIME (media/type).
     *
     * @param   string  $mime    MIME.
     * @return  array
     * @throws  \Hoa\Mime\Exception\MimeIsNotFound
     */
    public static function getExtensionsFromMime($mime)
    {
        if (null === static::$_computed) {
            static::compute();
        }

        list($media, $type) = static::parseMime($mime);

        if (!isset(static::$_computed[self::STRUCTURE_MEDIA_TYPE][$media][$type])) {
            throw new Exception\MimeIsNotFound(
                'MIME type %s does not exist.',
                2,
                $mime
            );
        }

        return static::$_computed[self::STRUCTURE_MEDIA_TYPE][$media][$type];
    }

    /**
     * Get MIME (media/type) from extension.
     *
     * @param   string  $extension    Extension to considere.
     * @return  string
     */
    public static function getMimeFromExtension($extension)
    {
        if (false === static::extensionExists($extension)) {
            return null;
        }

        return static::$_computed[self::STRUCTURE_EXTENSION][$extension];
    }

    /**
     * Parse MIME, i.e. extract media and type. Please, see self::MIME_*
     * constants as result array index.
     *
     * @param   string  $mime    MIME to parse.
     * @return  array
     * @throws  \Hoa\Mime\Exception
     */
    public static function parseMime($mime)
    {
        if (false === strpos($mime, '/')) {
            throw new Exception(
                'MIME %s is not well-formed (media/type).',
                3,
                $mime
            );
        }

        return explode('/', $mime);
    }

    /**
     * Find informations about stream.
     *
     * @param   \Hoa\Stream  $stream    Stream to study.
     * @return  void
     * @throws  \Hoa\Mime\Exception\MimeIsNotFound
     */
    protected function _find(Stream $stream)
    {
        $name = $stream->getStreamName();

        if ($stream instanceof Stream\IStream\Pathable) {
            $based = $stream->getBasename();
        } else {
            $based = basename($name);
        }

        if (false === $poos = strrpos($based, '.')) {
            throw new Exception\MimeIsNotFound(
                'Cannot find MIME type of %s, because extension is not found.',
                4,
                $name
            );
        }

        $this->_extension = substr($based, $poos + 1);
        $this->_mime      = static::getMimeFromExtension($this->_extension);

        if (null === $this->_mime) {
            throw new Exception\MimeIsNotFound(
                'No MIME type associated to the %s extension.',
                5,
                $this->_extension
            );
        }

        list($this->_media, $this->_type) = static::parseMime($this->_mime);

        return;
    }

    /**
     * Get extension.
     *
     * @return  string
     */
    public function getExtension()
    {
        return $this->_extension;
    }

    /**
     * Get other extensions (associated to the same MIME).
     *
     * @return  array
     */
    public function getOtherExtensions()
    {
        $out     = [];
        $current = $this->getExtension();
        $others  =
            self::$_computed
                [self::STRUCTURE_MEDIA_TYPE]
                [$this->getMedia()]
                [$this->getType()];

        foreach ($others as $other) {
            if ($other !== $current) {
                $out[] = $other;
            }
        }

        return $out;
    }

    /**
     * Get MIME (media/type).
     *
     * @return  string
     */
    public function getMime()
    {
        return $this->_mime;
    }

    /**
     * Get media.
     *
     * @return  string
     */
    public function getMedia()
    {
        return $this->_media;
    }

    /**
     * Get type.
     *
     * @return  string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Check if the MIME is experimental or not.
     *
     * @return  bool
     */
    public function isExperimental()
    {
        return 'x-' === substr($this->getType(), 0, 2);
    }

    /**
     * Check if the MIME is a vendor's one or not.
     *
     * @return  bool
     */
    public function isVendor()
    {
        return 'vnd.' === substr($this->getType(), 0, 4);
    }
}

/**
 * Flex entity.
 */
Core\Consistency::flexEntity('Hoa\Mime\Mime');
