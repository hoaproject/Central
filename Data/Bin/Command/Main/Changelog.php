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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * Hoa_Version
 */
import('Version.~');

/**
 * Class ChangelogCommand.
 *
 * Manipule the changelog.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class ChangelogCommand extends Hoa_Console_Command_Abstract {

    /**
     * Author name.
     *
     * @var ChangelogCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var ChangelogCommand string
     */
    protected $programName = 'Changelog';

    /**
     * Options description.
     *
     * @var ChangelogCommand array
     */
    protected $options     = array(
        array('revision',    parent::REQUIRED_ARGUMENT, 'r'),
        array('text-plain',  parent::NO_ARGUMENT,       't'),
        array('with-colour', parent::NO_ARGUMENT,       'c'),
        array('help',        parent::NO_ARGUMENT,       'h'),
        array('help',        parent::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $revisions  = array();
        $textPlain  = false;
        $withColour = false;
        $file       = 'hoa://Data/Etc/CHANGELOG.xml';

        if(!file_exists($file))
            throw new Hoa_Console_Command_Exception(
                'File %s does not exist.', 0, $file);

        $xml = simplexml_load_file($file);

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'r':
                    $revisions = array_merge(
                        $revisions,
                        parent::parseSpecialValue(
                            $v,
                            array(
                                'HEAD' => $xml->logentry[0]['revision'] . '',
                                'PREV' => Hoa_Version::getPreviousRevision()
                            )
                        )
                    );
                  break;

                case 't':
                    $textPlain  = true;
                  break;

                case 'c':
                    $withColour = true;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        $out = array();

        if(empty($revisions))
            $out = $xml->xpath('logentry');

        $revisions = array_unique($revisions);
        rsort($revisions);

        foreach($revisions as $foo => $revision)
            if(trim($revision) != '')
                if(false !== $handle = @$xml->xpath('logentry[@revision=' . trim($revision) . ']'))
                    foreach($handle as $oof => $node)
                        $out[] = $node;

        if(empty($out))
            throw new Hoa_Console_Command_Exception(
                'No revision was found, given : %s.',
                3, implode(',', $revisions));

        if(false === $textPlain) {

            cout('<?xml version="1.0" encoding="utf-8"?>');
            cout('<log>');
            foreach($out as $foo => $o)
                cout('  ' . $o->asXML());
            cout('</log>');

            return HC_SUCCESS;
        }

        $sht  = array(
            ''  => '          ',
            'A' => '\e[32mAdded\e[0m     ',
            'D' => '\e[31mDeleted\e[0m   ',
            'M' => '\e[34mModified\e[0m  ',
            'R' => '\e[33mReplaced\e[0m  ',
            'C' => '\e[21mConflict\e[0m  ',
            'X' => 'External  ',
            'I' => 'Ignored   ',
            '?' => 'NotUnderV ',
            '!' => 'ItMissing ',
            'U' => '\e[36mUpdated\e[0m   '
        );
        $text = null;
        $wrap = parent::getEnvironment('window.columns') - 18;
        foreach($out as $foo => $entry) {

            $msg   = $entry->msg;
            $msg   = str_replace('&lt;', '<', $msg);
            $msg   = str_replace('&gt;', '>', $msg);

            $text .= '\e[33mRev. ' . str_pad($entry['revision'], 13) . '\e[0m' .
                     str_replace("\n", "\n                  ",
                                 '\e[34m' . wordwrap($msg, $wrap, "\n") . '\e[0m') . "\n" .
                     '    At            ' . $entry->date . "\n" .
                     '    By            ' . $entry->author . "\n";

            $hndl  = null;
            foreach($entry->paths->path as $path)
                $hndl .= '    ' . $sht[$path['action'] . ''] .
                         '    ' . $path . "\n";

            $text .= $hndl . "\n";
        }

        if(false === $withColour || OS_WIN || !function_exists('posix_isatty'))
            $text = preg_replace('#\\\e\[[0-9]+m#', '',      $text);
        else
            $text = preg_replace('#\\\e\[#',        "\033[", $text);

        cout(
            $text,
            Hoa_Console_Core_Io::NO_NEW_LINE,
            Hoa_Console_Core_Io::NO_WORDWRAP
        );

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : main:changelog [-r] [-t] [-c]');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'r'    => 'Specify the revision (can be a range min:max of ' .
                      'revisions) :' . "\n" .
                      '    [number] for a specified revision number ;' . "\n" .
                      '    HEAD     for the latest revision number ;' . "\n" .
                      '    PREV     for the previous revision number.' . "\n" .
                      'Revisions can be separated by a comma.' . "\n" .
                      'And finally, negative numbers should be given.',
            't'    => 'Do not print the changelog as an XML document, but in ' .
                      'plain text.',
            'c'    => 'Print the plain text with colour (only on tty terminal).',
            'help' => 'This help.'
        )));

        cout('Example with revisions');
        cout(parent::columnize(array(
            array(
                '    -r 1,3,7',
                'Select revisions 1, 3 and 7.'
            ),
            array(
                '    -r 1:7',
                'Select revisions 1 to 7 include.'
            ),
            array(
                '    -r HEAD:-10',
                'Select the eleven latest revisions.'
            ),
            array(
                '    -r HEAD:PREV',
                'Select revisions for this migration.'
            ),
            array(
                '    -r 1:7,19,HEAD:-2',
                'Select revisions 1 to 7, 19, and the two latest.'
            )
        )));

        return HC_SUCCESS;
    }
}
