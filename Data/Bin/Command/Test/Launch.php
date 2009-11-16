<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @category    Data
 *
 */

/**
 * Hoa_Test
 */
import('Test.~');

/**
 * Hoa_Test_Praspel
 */
import('Test.Praspel.~');

/**
 * Hoa_Stream
 */
import('Stream.~');

/**
 * Hoa_Stream_Io_Out
 */
import('Stream.Io.Out');


class My extends Hoa_Stream implements Hoa_Stream_Io_Out {

    protected $self = null;

    public function __construct ( $self ) {

        $this->self = $self;

        parent::__construct(null);
    }

    protected function &open ( $streamName, Hoa_Stream_Context $context = null ) {

        $out = null;

        return $out;
    }

    public function close ( ) {

        return true;
    }

    public function isOpened ( ) {

        return true;
    }

    public function write ( $string, $length ) {

        cout($string);

        return $length;
    }

    public function writeString ( $string ) {

        return $this->write($string, strlen($string));
    }

    public function writeCharacter ( $char ) {

        return $this->write((string) $char[0], 1);
    }

    public function writeInteger ( $integer ) {

        $integer = (string) (int) $integer;

        return $this->write($integer, strlen($integer));
    }

    public function writeFloat ( $float ) {

        $float = (string) (float) $float;

        return $this->write($float, strlen($float));
    }

    public function writeArray ( Array $array ) {

        $this->self->status(
            'foo',
            $array[1]
        );
    }

    public function writeLine ( $line ) {

        if(false === $n = strpos($line, "\n"))
            return $this->write($line, strlen($line));

        return $this->write(substr($line, 0, $n), $n);
    }

    public function writeAll ( $string ) {

        return $this->write($string, strlen($string));
    }

    public function truncate ( $size ) {

        return false;
    }
}

/**
 * Class LaunchCommand.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class LaunchCommand extends Hoa_Console_Command_Abstract {

    /**
     * Author name.
     *
     * @var LaunchCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var LaunchCommand string
     */
    protected $programName = 'Launch';

    /**
     * Options description.
     *
     * @var LaunchCommand array
     */
    protected $options     = array(
        array('help', parent::NO_ARGUMENT, 'h'),
        array('help', parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        parent::listInputs($directory);

        if(null === $directory)
            return $this->usage();

        import('File.ReadWrite');
        $my = new My($this);
        Hoa_Test::getInstance()->addOutputStreams(array(
            new Hoa_File_ReadWrite('hoa://Data/Temporary/Foo'),
            $my
        ));

        $directory    = Hoa_Framework::getProtocol()->resolve($directory);
        $oracle       = glob($directory . DS . 'Ordeal' . DS . 'Oracle' . DS . '*');
        $battleground = glob($directory . DS . 'Ordeal' . DS . 'Battleground' . DS . '*');

        foreach($oracle as $i => $file)
            require_once $file;

        foreach($battleground as $i => $file) {

            try {

                $my->writeAll($file);
                require_once $file;
            }
            catch( Exception $e ) {

                throw new Hoa_Console_Exception(
                    $e->getFormattedMessage(),
                    $e->getCode()
                );
            }
        }

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : test:launch <options> path');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
