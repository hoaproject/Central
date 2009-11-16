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
 * Hoa_File_Finder
 */
import('File.Finder');

/**
 * Class TreeCommand.
 *
 * This command prints contents of a specific directory in a tree-like format.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class TreeCommand extends Hoa_Console_Command_Abstract {

    /**
     * Author name.
     *
     * @var TreeCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var TreeCommand string
     */
    protected $programName = 'Tree';

    /**
     * Options description.
     *
     * @var TreeCommand array
     */
    protected $options     = array(
        array('depth',            parent::REQUIRED_ARGUMENT, 'd'),
        array('list-visible',     parent::NO_ARGUMENT,       'v'),
        array('list-hidden',      parent::NO_ARGUMENT,       'V'),
        array('list-file',        parent::NO_ARGUMENT,       'f'),
        array('list-directory',   parent::NO_ARGUMENT,       'F'),
        array('list-link',        parent::NO_ARGUMENT,       'l'),
        array('list-no-dot',      parent::NO_ARGUMENT,       'O'),
        array('list-dot',         parent::NO_ARGUMENT,       'o'),
        array('sort-atime',       parent::NO_ARGUMENT,       'a'),
        array('sort-ctime',       parent::NO_ARGUMENT,       'c'),
        array('sort-mtime',       parent::NO_ARGUMENT,       'm'),
        array('sort-group',       parent::NO_ARGUMENT,       'g'),
        array('sort-owner',       parent::NO_ARGUMENT,       'w'),
        array('sort-permissions', parent::NO_ARGUMENT,       'p'),
        array('sort-name',        parent::NO_ARGUMENT,       'N'),
        array('sort-iname',       parent::NO_ARGUMENT,       'n'),
        array('sort-reverse',     parent::NO_ARGUMENT,       'r'),
        array('sort-random',      parent::NO_ARGUMENT,       'u'),
        array('help',             parent::NO_ARGUMENT,       'h'),
        array('help',             parent::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $depth = -1;
        $list  = 0;
        $sort  = Hoa_File_Finder::SORT_INAME;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'd':
                    $depth = (int) $v;
                  break;

                case 'v':
                    $list |= Hoa_File_Finder::LIST_VISIBLE;
                  break;

                case 'V':
                    $list |= Hoa_File_Finder::LIST_HIDDEN;
                  break;

                case 'f':
                    $list |= Hoa_File_Finder::LIST_FILE;
                  break;

                case 'F':
                    $list |= Hoa_File_Finder::LIST_DIRECTORY;
                  break;

                case 'l':
                    $list |= Hoa_File_Finder::LIST_LINK;
                  break;

                case 'O':
                    $list |= Hoa_File_Finder::LIST_NO_DOT;
                  break;

                case 'o':
                    $list |= Hoa_File_Finder::LIST_DOT;
                  break;

                case 'a':
                    $sort  = Hoa_File_Finder::SORT_ATIME;
                  break;

                case 'c':
                    $sort  = Hoa_File_Finder::SORT_CTIME;
                  break;

                case 'm':
                    $sort  = Hoa_File_Finder::SORT_MTIME;
                  break;

                case 'g':
                    $sort  = Hoa_File_Finder::SORT_GROUP;
                  break;

                case 'w':
                    $sort  = Hoa_File_Finder::SORT_OWNER;
                  break;

                case 'p':
                    $sort  = Hoa_File_Finder::SORT_PERMISSIONS;
                  break;

                case 'N':
                    $sort  = Hoa_File_Finder::SORT_NAME;
                  break;

                case 'n':
                    $sort  = Hoa_File_Finder::SORT_INAME;
                  break;

                case 'r':
                    $sort |= Hoa_File_Finder::SORT_REVERSE;
                  break;

                case 'u':
                    $sort  = Hoa_File_Finder::SORT_RANDOM;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        if(0 === $list)
            $list = Hoa_File_Finder::LIST_VISIBLE |
                    Hoa_File_Finder::LIST_NO_DOT;

        parent::listInputs($path);

        if(null === $path)
            $path = getcwd();

        cout($path . DS);
        $this->find(
            new Hoa_File_Finder(
                $path,
                $list,
                $sort
            ),
            $depth,
            $list,
            $sort
        );

        return HC_SUCCESS;
    }

    /**
     * Print the tree.
     *
     * @access  protected
     * @param   Hoa_File_Finder  $finder     Finder object.
     * @param   int              $depth      Depth.
     * @param   int              $list       Combination of
     *                                       Hoa_File_Finder::LIST_* constants.
     * @param   int              $sort       Combination of
     *                                       Hoa_File_Finder::SORT_* constants.
     * @return  void
     */
    protected function find ( Hoa_File_Finder $finder, $depth, $list, $sort) {

        static $i = 0;

        if($depth === 0)
            return;

        $max = count($finder) - 1;

        foreach($finder as $key => $file) {

            cout(str_repeat('|   ', $i), Hoa_Console_Core_Io::NO_NEW_LINE);

            $basename = $file->getBasename();

            cout('|-- ' . $basename, Hoa_Console_Core_Io::NO_NEW_LINE);

            if($file->isLink())
                cout(
                    ' -> ' . $file->define()->getTargetName(),
                    Hoa_Console_Core_Io::NO_NEW_LINE
                );
            elseif($file->isDirectory()) {

                cout(DS);

                $i++;
                $this->find(
                    new Hoa_File_Finder(
                        $file->getRealPath(),
                        $list,
                        $sort
                    ),
                    $depth - 1,
                    $list,
                    $sort
                );
                $i--;
            }
            else
                cout();

            $max--;
        }
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout(
            'Usage   : main:tree <options> [path]');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'd'    => 'Max display depth in the directory tree.',
            'v'    => 'List visible entries.',
            'V'    => 'List hidden entries.',
            'f'    => 'List files.',
            'F'    => 'List directories.',
            'l'    => 'List link.',
            'O'    => 'Do not list current and parent.',
            'o'    => 'List current and parent.',
            'a'    => 'Sort by access time.',
            'c'    => 'Sort by inode change time.',
            'm'    => 'Sort by modification time.',
            'g'    => 'Sort by group.',
            'w'    => 'Sort by owner.',
            'p'    => 'Sort by permissions.',
            'N'    => 'Sort by name.',
            'n'    => 'Sort by name with an insensitive case.',
            'r'    => 'Reverse the sort.',
            'u'    => 'Random sort.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
