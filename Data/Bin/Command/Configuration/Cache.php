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
 * Class CacheCommand.
 *
 * This command allows to generate caches of the package configurations.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class CacheCommand extends Hoa_Console_Command_Abstract {

    /**
     * Author name.
     *
     * @var VersionCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var VersionCommand string
     */
    protected $programName = 'Cache';

    /**
     * Options description.
     *
     * @var VersionCommand array
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

        $configuration = Hoa_Framework::getProtocol()->resolve(
            'hoa://Data/Etc/Configuration/'
        );
        $files         = glob($configuration . DS . '*.json');
        $cache         = $configuration . DS . '.Cache' . DS;

        foreach($files as $i => $file)
            parent::status(
                'Cache ' . parent::stylize(basename($file), 'info') . ' file.',
                false !== file_put_contents(
                    $cache . substr(basename($file), 0, -5) . '.php',
                    '<?php' . "\n\n" .
                    '/**' . "\n" .
                    ' * Generated the ' . date('Y-m-d\TH:i:s.000000\Z') . "\n" .
                    ' */' . "\n\n" .
                    'return ' . var_export(
                        json_decode(
                            file_get_contents($file),
                            true
                        ),
                        true
                    ) . ';'
                )
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

        cout('Usage   : configuration:cache <options>');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
