<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Console\Chrome\Style
 */
-> import('Console.Chrome.Style');

}

namespace Bin\Command {

/**
 * Class Style.
 *
 * This sheet declares the main style.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 */

class Style extends \Hoa\Console\Chrome\Style {

    /**
     * Import the style.
     *
     * @access  public
     * @return  void
     */
    public function import ( ) {

        parent::addStyles(array(

            '_exception' => array(
                parent::COLOR_FOREGROUND_WHITE,
                parent::COLOR_BACKGROUND_RED
            ),

            'h1' => array(
                parent::COLOR_FOREGROUND_YELLOW,
                parent::TEXT_UNDERLINE
            ),

            'h2' => array(
                parent::COLOR_FOREGROUND_GREEN
            ),

            'info' => array(
                parent::COLOR_FOREGROUND_YELLOW
            ),

            'error' => array(
                parent::COLOR_FOREGROUND_WHITE,
                parent::COLOR_BACKGROUND_RED,
                parent::TEXT_BOLD
            ),

            'success' => array(
                parent::COLOR_FOREGROUND_GREEN
            ),

            'nosuccess' => array(
                parent::COLOR_FOREGROUND_RED
            ),

            'command' => array(
                parent::COLOR_FOREGROUND_BLUE
            ),

            'attention' => array(
                parent::COLOR_FOREGROUND_WHITE,
                parent::COLOR_BACKGROUND_RED,
                parent::TEXT_BOLD
            ),
        ));

        return;
    }
}

}
