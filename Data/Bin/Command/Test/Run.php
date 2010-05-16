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
 * Hoa_Log
 */
import('Log.~');

/**
 * Hoa_Stream
 */
import('Stream.~');

/**
 * Hoa_Stream_Io_Out
 */
import('Stream.Io.Out');

/**
 * Hoa_File_Finder
 */
import('File.Finder');

/**
 * Class RunCommand.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class Trivial extends Hoa_Stream implements Hoa_Stream_Io_Out {

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
            $array['class'] . '::' . $array['method'] . '(' .
            implode(', ', $array['arguments']) . ') -> ' . $array['result'],
            $array['status']
        );
        cout('    ' . $array['message']);
        cout('    ' . $array['file'] . ' from ' . $array['startLine'] .
             ' to ' . $array['endLine'] . '.');

        cout();
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

class RunCommand extends Hoa_Console_Command_Abstract {

    /**
     * Author name.
     *
     * @var RunCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var RunCommand string
     */
    protected $programName = 'Run';

    /**
     * Options description.
     *
     * @var RunCommand array
     */
    protected $options     = array(
        array('revision',  parent::REQUIRED_ARGUMENT, 'r'),
        array('file',      parent::REQUIRED_ARGUMENT, 'f'),
        array('class',     parent::REQUIRED_ARGUMENT, 'c'),
        array('method',    parent::REQUIRED_ARGUMENT, 'm'),
        array('iteration', parent::REQUIRED_ARGUMENT, 'i'),
        array('help',      parent::NO_ARGUMENT,       'h'),
        array('help',      parent::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $repository = null;
        $file       = null;
        $class      = null;
        $method     = null;
        $iteration  = 1;

        $path = 'hoa://Data/Etc/Configuration/.Cache/HoaTest.php';

        if(!file_exists($path))
            throw new Hoa_Console_Command_Exception(
                'Configuration cache file %s does not exists.', 0, $path);

        $configurations = require $path;
        $repos          = Hoa_Framework_Parameter::zFormat(
            $configurations['parameters']['repository'],
            $configurations['keywords'],
            $configurations['parameters']
        );
        $finder         = new Hoa_File_Finder(
            $repos,
            Hoa_File_Finder::LIST_DIRECTORY,
            Hoa_File_Finder::SORT_MTIME |
            Hoa_File_Finder::SORT_REVERSE
        );
        $revision       = basename($finder->getIterator()->current());

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'r':
                    $handle   = parent::parseSpecialValue(
                        $v,
                        array('HEAD' => $revision)
                    );
                    $revision = $handle[0];
                  break;

                case 'f':
                    $file = $v;
                  break;

                case 'c':
                    $class = $v;
                  break;

                case 'm':
                    $method = $v;
                  break;

                case 'i':
                    $iteration = (int) $v;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        $repository = $repos . $revision;

        if(!is_dir($repository))
            throw new Hoa_Console_Command_Exception(
                'Repository %s does not exist.', 1, $repository);

        // Yup, berk.
        $configurations['parameters']['revision'] = $revision . '/';

        if(null === $file)
            return $this->usage();

        Hoa_Log::getChannel(
            Hoa_Test_Praspel::LOG_CHANNEL
        )->addOutputStream(new Trivial($this));

        $oracle       = Hoa_Framework_Parameter::zFormat(
            $configurations['parameters']['ordeal.oracle'],
            $configurations['keywords'],
            $configurations['parameters']
        );
        $battleground = Hoa_Framework_Parameter::zFormat(
            $configurations['parameters']['ordeal.battleground'],
            $configurations['keywords'],
            $configurations['parameters']
        );

        if(!file_exists($oracle . $file))
            throw new Hoa_Console_Command_Exception(
                'File %s does not exist in repository %s.',
                2, array($file, $repository));

        require_once $oracle . $file;
        require_once $battleground . $file;

        if(null !== $class) {

            $exportTests = array_intersect_key(
                $exportTests,
                array($class => 0)
            );

            if(null !== $method) {

                foreach($exportTests as $c => &$methods)
                    $methods = array_intersect(
                        $methods,
                        array(0 => $method)
                    );
            }
        }

        for($i = $iteration; $i > 0; $i--) {

            cout(parent::underline('Iteration ' . ($iteration - $i + 1)));
            cout();

            foreach($exportTests as $classname => $methods) {

                $classname = 'Hoatest_' . $classname;
                $class = new $classname();

                foreach($methods as $j => $method)
                    $class->{'__test_' . $method}();
            }

            cout();
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

        cout('Usage   : test:run <options>');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'r'    => 'Revision of the repository tests.',
            'f'    => 'File to test in the repository.',
            'c'    => 'Class to test in the file.',
            'm'    => 'Method to test in the class.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
