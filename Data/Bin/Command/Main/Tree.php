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
 * \Hoa\File\Finder
 */
-> import('File.Finder');

/**
 * Class TreeCommand.
 *
 * This command prints contents of a specific directory in a tree-like format.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 */

class TreeCommand extends \Hoa\Console\Command\Generic {

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
        $sort  = \Hoa\File\Finder::SORT_INAME;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'd':
                    $depth = (int) $v;
                  break;

                case 'v':
                    $list |= \Hoa\File\Finder::LIST_VISIBLE;
                  break;

                case 'V':
                    $list |= \Hoa\File\Finder::LIST_HIDDEN;
                  break;

                case 'f':
                    $list |= \Hoa\File\Finder::LIST_FILE;
                  break;

                case 'F':
                    $list |= \Hoa\File\Finder::LIST_DIRECTORY;
                  break;

                case 'l':
                    $list |= \Hoa\File\Finder::LIST_LINK;
                  break;

                case 'O':
                    $list |= \Hoa\File\Finder::LIST_NO_DOT;
                  break;

                case 'o':
                    $list |= \Hoa\File\Finder::LIST_DOT;
                  break;

                case 'a':
                    $sort  = \Hoa\File\Finder::SORT_ATIME;
                  break;

                case 'c':
                    $sort  = \Hoa\File\Finder::SORT_CTIME;
                  break;

                case 'm':
                    $sort  = \Hoa\File\Finder::SORT_MTIME;
                  break;

                case 'g':
                    $sort  = \Hoa\File\Finder::SORT_GROUP;
                  break;

                case 'w':
                    $sort  = \Hoa\File\Finder::SORT_OWNER;
                  break;

                case 'p':
                    $sort  = \Hoa\File\Finder::SORT_PERMISSIONS;
                  break;

                case 'N':
                    $sort  = \Hoa\File\Finder::SORT_NAME;
                  break;

                case 'n':
                    $sort  = \Hoa\File\Finder::SORT_INAME;
                  break;

                case 'r':
                    $sort |= \Hoa\File\Finder::SORT_REVERSE;
                  break;

                case 'u':
                    $sort  = \Hoa\File\Finder::SORT_RANDOM;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        if(0 === $list)
            $list = \Hoa\File\Finder::LIST_VISIBLE |
                    \Hoa\File\Finder::LIST_NO_DOT;

        parent::listInputs($path);

        if(null === $path)
            $path = getcwd();

        cout(trim($path, DS) . DS);
        $this->find(
            new \Hoa\File\Finder(
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
     * @param   \Hoa\File\Finder  $finder     Finder object.
     * @param   int              $depth      Depth.
     * @param   int              $list       Combination of
     *                                       \Hoa\File\Finder::LIST_* constants.
     * @param   int              $sort       Combination of
     *                                       \Hoa\File\Finder::SORT_* constants.
     * @return  void
     */
    protected function find ( \Hoa\File\Finder $finder, $depth, $list, $sort) {

        static $i = 0;

        if($depth === 0)
            return;

        $max = count($finder) - 1;

        foreach($finder as $key => $file) {

            cout(str_repeat('|   ', $i), \Hoa\Console\Core\Io::NO_NEW_LINE);

            $basename = $file->getBasename();

            cout('|-- ' . $basename, \Hoa\Console\Core\Io::NO_NEW_LINE);

            if($file->isLink())
                cout(
                    ' -> ' . $file->define()->getTargetName(),
                    \Hoa\Console\Core\Io::NO_NEW_LINE
                );
            elseif($file->isDirectory()) {

                cout(DS);

                $i++;
                $this->find(
                    new \Hoa\File\Finder(
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

        return;
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
            'l'    => 'List links.',
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

}
