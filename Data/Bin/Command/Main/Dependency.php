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
 * Class DependencyCommand.
 *
 * Manipule the changelog.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class DependencyCommand extends Hoa_Console_Command_Abstract {

    /**
     * Author name.
     *
     * @var DependencyCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var DependencyCommand string
     */
    protected $programName = 'Dependency';

    /**
     * Options description.
     *
     * @var DependencyCommand array
     */
    protected $options     = array(
        array('package',     parent::REQUIRED_ARGUMENT, 'p'),
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

        $packages   = array();
        $textPlain  = false;
        $withColour = false;

        if(!file_exists(HOA_DATA_ETC . DS . 'DEPENDENCY.xml'))
            throw new Hoa_Console_Command_Exception(
                'File %s does not exist.', 0, HOA_DATA_ETC . DS . 'DEPENDENCY.xml');

        $xml = simplexml_load_file(HOA_DATA_ETC . DS . 'DEPENDENCY.xml');

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'p':
                    $packages = array_merge($packages, parent::parseSpecialValue($v));
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

        sort($packages);

        $out = array();
        $n = 0;

        if(empty($packages))
            foreach($xml->xpath('package') as $package) {

                $out[] = $package;
                strlen($package['name']) > $n and $n = strlen($package['name']);
            }
        else
            foreach($packages as $foo => $package)
                if(false !== $handle = @$xml->xpath('package[@name="' . trim($package) . '"]'))
                    foreach($handle as $oof => $node) {

                        $out[] = $node;
                        strlen($node['name']) > $n and $n = strlen($node['name']);
                    }

        if(empty($out))
            throw new Hoa_Console_Command_Exception(
                'No package was found, given : %s.',
                3, implode(',', $out));

        if(false === $textPlain) {

            cout('<?xml version="1.0" encoding="utf-8"?>');
            cout('<dependency>');
            foreach($out as $foo => $o)
                cout('  ' . $o->asXML());
            cout('</dependency>');

            return HC_SUCCESS;
        }

        $text = null;
        $wrap = parent::getEnvironment('window.columns') - 18;
        foreach($out as $foo => $package) {

            $text .= '\e[33m' . $package['name'] . '\e[0m' . "\n";

            if(empty($package->dependent['on']))
                $text .= str_repeat(' ', $n + 2) . '\e[31m(No dependency)\e[0m' . "\n";
             else
                foreach($package->dependent as $foo => $dependency)
                    $text .= str_repeat(' ', $n + 2) . $dependency['on'] . "\n";
        }

        if(false === $withColour)
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

        cout('Usage   : main:dependency [-p] [-t] [-c]');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'p'    => 'List of packages (separated by comma).',
            't'    => 'Do not print the dependency as an XML document, but in ' .
                      'plain text.',
            'c'    => 'Print the plain text with colour (only on tty terminal).',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
