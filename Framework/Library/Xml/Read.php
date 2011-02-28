<?php

/**
 * Hoa
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
 * \Hoa\Xml\Exception
 */
-> import('Xml.Exception')

/**
 * \Hoa\Xml
 */
-> import('Xml.~')

/**
 * \Hoa\Stream\IStream\In
 */
-> import('Stream.I~.In')

/**
 * \Hoa\Xml\Element\Read
 */
-> import('Xml.Element.Read', true);

}

namespace Hoa\Xml {

/**
 * Class \Hoa\Xml\Read.
 *
 * Read a XML element.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Read extends Xml implements \Hoa\Stream\IStream\In {

    /**
     * Start the stream reader as if it is a XML document.
     *
     * @access  public
     * @param   \Hoa\Stream\IStream\In  $stream    Stream to read.
     * @return  void
     */
    public function __construct ( \Hoa\Stream\IStream\In $stream ) {

        parent::__construct('\Hoa\Xml\Element\Read', $stream);

        return;
    }

    /**
     * Test for end-of-file.
     *
     * @access  public
     * @return  bool
     */
    public function eof ( ) {

        return $this->getStream()->eof();
    }

    /**
     * Read n characters.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  string
     * @throw   \Hoa\Xml\Exception
     */
    public function read ( $length ) {

        return $this->getStream()->read($length);
    }

    /**
     * Alias of $this->read().
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  string
     */
    public function readString ( $length ) {

        return $this->getStream()->readString($length);
    }

    /**
     * Read a character.
     *
     * @access  public
     * @return  string
     */
    public function readCharacter ( ) {

        return $this->getStream()->readCharacter();
    }

    /**
     * Read a boolean.
     *
     * @access  public
     * @return  bool
     */
    public function readBoolean ( ) {

        return $this->getStream()->readBoolean();
    }

    /**
     * Read an integer.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  int
     */
    public function readInteger ( $length = 1 ) {

        return $this->getStream()->readInteger($length);
    }

    /**
     * Read a float.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  float
     */
    public function readFloat ( $length = 1 ) {

        return $this->getStream()->readFloat($length);
    }

    /**
     * Read the XML tree as an array.
     *
     * @access  public
     * @param   string  $argument    Not use here.
     * @return  array
     */
    public function readArray ( $argument = null ) {

        return $this->getStream()->readArray($argument);
    }

    /**
     * Read a line.
     *
     * @access  public
     * @return  string
     */
    public function readLine ( ) {

        return $this->getStream()->readLine();
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @access  public
     * @return  string
     */
    public function readAll ( ) {

        return $this->getStream()->readAll();
    }

    /**
     * Parse input from a stream according to a format.
     *
     * @access  public
     * @param   string  $format    Format (see printf's formats).
     * @return  array
     */
    public function scanf ( $format ) {

        return $this->getStream()->scanf($format);
    }

    /**
     * Read content as a DOM tree.
     *
     * @access  public
     * @return  \DOMElement
     */
    public function readDOM ( ) {

        return $this->getStream()->readDOM();
    }
}

}
