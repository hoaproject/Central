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
 * \Hoa\Xyl
 */
-> import('Xyl.~')

/**
 * \Hoa\File\Read
 */
-> import('File.Read')

/**
 * \Hoa\Php\Io\Out
 */
-> import('Php.Io.Out');

/**
 * Class RenderCommand.
 *
 * Make a render of a XYL document.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class RenderCommand extends \Hoa\Console\Command\Generic {

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
    protected $programName = 'Render';

    /**
     * Options description.
     *
     * @var RunCommand array
     */
    protected $options     = array(
        array('interpreter', parent::REQUIRED_ARGUMENT, 'i'),
        array('data',        parent::REQUIRED_ARGUMENT, 'd'),
        array('stylesheet',  parent::REQUIRED_ARGUMENT, 's'),
        array('overlay',     parent::REQUIRED_ARGUMENT, 'o'),
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

        $interpreter = 'html';
        $datafile    = null;
        $stylesheet  = array();
        $overlay     = array();

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'i':
                    $interpreter = $v;
                  break;

                case 'd':
                    $datafile = $v;
                  break;

                case 's':
                    if('default' == $v)
                        $v = 'http://hoa-project.net/Public/Css/Xyl_default.css';

                    $stylesheet[] = $v;
                  break;

                case 'o':
                    $overlay[] = $v;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        parent::listInputs($file);

        if(null == $file)
            throw new \Hoa\Console\Command\Exception(
                'Filename cannot be null.', 0);

        $xyl = new \Hoa\Xyl(
            new \Hoa\File\Read($file),
            new \Hoa\Php\Io\Out(),
            dnew('Hoa\Xyl\Interpreter\\' . ucfirst(strtolower($interpreter)))
        );

        foreach(array_reverse($stylesheet) as $s)
            $xyl->addStylesheet($s);

        foreach($overlay as $o)
            $xyl->addOverlay($o);

        if(null !== $datafile) {

            if(false === file_exists($datafile))
                throw new \Hoa\Console\Command\Exception(
                    'Data file %s does not exist.', 1, $datafile);

            $data = $xyl->getData();
            require $datafile;
        }

        $xyl->render();

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : xyl:render <options> [file]');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'i'    => 'Interpreter name.',
            'd'    => 'Data filename, that adds data to the $data variable.',
            's'    => 'Stylesheet of the document:' . "\n" .
                      '    [URI]   for a specific stylesheet;' . "\n" .
                      '    default for a default stylsheet.',
            'o'    => 'Overlay of the document.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}

}
