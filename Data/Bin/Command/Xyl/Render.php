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
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
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
                        $v = 'http://hoa-project.net/Css/Xyl_default.css';

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
