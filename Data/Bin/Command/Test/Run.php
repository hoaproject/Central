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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * Hoa_Test_Praspel_Visitor_Praspel
 */
import('Test.Praspel.Visitor.Praspel');

/**
 * Hoa_File_Finder
 */
import('File.Finder');

/**
 * Hoa_Php_Io_Out
 */
import('Php.Io.Out');

/**
 * Class RunCommand.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class Out extends Hoa_Php_Io_Out {

    public $self = null;

    public function writeArray ( Array $array ) {

        $_    = $array['log'];
        $args = null;
        $res  = null;

        if(Hoa_Test_Praspel::LOG_TYPE_INVARIANT === $_['type']) {

           if(SUCCEED === $_['status'])
               return;

            $this->self->status(
                str_repeat('  ', $_['depth']) . $_['class'] . ': ' .
                $this->self->stylize($_['message'], 'info'),
                $_['status']
            );

            return;
        }

        foreach($_['arguments'] as $argument) {

            if(null !== $args)
                $args .= ', ';

            if(is_array($argument))
                $args .= 'array(…)';
            elseif(is_object($argument))
                $args .= get_class($argument);
            else
                $args .= var_export($argument, true);
        }

        if(isset($_['result']))
            $res = ' -> ' . var_export($_['result'], true);

        $this->self->status(
            str_repeat('  ', max(0, $_['depth'])) .
            $_['class'] . '::' . $_['method'] . '(' . $args . ')' .
            $res . ': ' . $this->self->stylize($_['message'], 'info'),
            $_['status']
        );

        return;
    }

    public function writeOpenIteration ( Hoa_Core_Event_Bucket $event ) {

        $data = $event->getData();

        cout($this->self->stylize('Iteration #' . $data['iteration'], 'h2'));
        cout();
        cout($this->self->stylize('Runtime', 'info'));

        return;
    }

    public function writeCloseIteration ( Hoa_Core_Event_Bucket $event ) {

        $data = $event->getData();

        cout();
        cout($this->self->stylize('Contract-covering', 'info'));
        cout('    ' . str_replace(
            "\n",
            "\n    ",
            $data['contract']->accept(new ContractCovering())
        ));

        return;
    }
}

class ContractCovering extends Hoa_Test_Praspel_Visitor_Praspel {

    public function visitDomainDisjunction ( Hoa_Visitor_Element $element,
                                             &$handle = null, $eldnah = null ) {

        if(!($element instanceof Hoa_Test_Praspel_Variable))
            return parent::visitDomainDisjunction($element, $handle, $eldnah);

        $domains = $this->formatArguments($element->getDomains());
        $domain  = $element->hasChoosenDomain()
                       ? $element->getChoosenDomain()
                       : null;
        $i       = 0;

        foreach($element->getDomains() as $d) {

            if($d === $domain)
                $domains[$i] = Hoa_Console_Interface_Style::stylize(
                    $domains[$i],
                    Hoa_Console_Interface_Style::COLOR_FOREGROUND_YELLOW
                );
            else
                $domains[$i] = $domains[$i];

            ++$i;
        }

        return $element->getName() . ': ' . implode(' or ', $domains);
    }
}

class RunCommand extends Hoa_Console_Command_Generic {

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
        array('sampler',   parent::REQUIRED_ARGUMENT, 's'),
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
        $sampler    = true;

        $test       = new Hoa_Test();
        $repos      = $test->getFormattedParameter('repository');
        $finder     = new Hoa_File_Finder(
            $repos,
            Hoa_File_Finder::LIST_DIRECTORY,
            Hoa_File_Finder::SORT_MTIME |
            Hoa_File_Finder::SORT_REVERSE
        );
        $revision   = basename($finder->getIterator()->current());
        $rev        = false;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'r':
                    $handle   = parent::parseSpecialValue(
                        $v,
                        array('HEAD' => $revision)
                    );
                    $revision = $handle[0];
                    $rev      = true;
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

        if(false === $rev) {

            cout('No revision was given; assuming ' .
                 parent::stylize('HEAD', 'info') . ', i.e ' .
                 parent::stylize($revision, 'info') . '.');
            cout();
        }

        $repository = $repos . $revision;

        if(!is_dir($repository))
            throw new Hoa_Console_Command_Exception(
                'Repository %s does not exist.', 0, $repository);

        $test->setParameter('revision', $revision . DS);

        if(null === $file)
            return $this->usage();

        $instrumented = $test->getFormattedParameter('instrumented');

        if(!file_exists($instrumented . $file))
            throw new Hoa_Console_Command_Exception(
                'File %s does not exist in repository %s.',
                1, array($file, $repository));

        require_once $instrumented . $file;

        if(false === $sampler)
            return HC_SUCCESS;

        if(   null === $class
           || null === $method)
            return $this->usage();

        $out = new Out();
        $out->self = $this; // berk…

        event('hoa://Event/Log/' . Hoa_Test_Praspel::LOG_CHANNEL)
            ->attach($out);
        event('hoa://Event/Test/Sample:open-iteration')
            ->attach($out, 'writeOpenIteration');
        event('hoa://Event/Test/Sample:close-iteration')
            ->attach($out, 'writeCloseIteration');

        import('File.Write') and load();

        $f = new Hoa_File_Write('hoa://Data/Temporary/Test.txt');
        $f->writeAll('foobar2' . "\n");
        $f->truncate(4);

        return;

        for($i = 1; $iteration > 0; --$iteration, ++$i) {

            try {

                $contractId = $class . '::' . $method;
                $test->sample($contractId, $class, $method);
                $contract   = Hoa_Test_Praspel::getInstance()->getContract(
                    $contractId
                );
            }
            catch ( Hoa_Test_Exception $e ) {

                throw new Hoa_Console_Command_Exception(
                    $e->getFormattedMessage(), $e->getCode());
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
            'r'    => 'Revision of the repository tests:' . "\n" .
                      '    [revision name] for a specified revision;' . "\n" .
                      '    HEAD            for the latest revision.',
            'f'    => 'File to test in the repository.',
            'c'    => 'Class to test in the file.',
            'm'    => 'Method to test in the class.',
            'i'    => 'Number of iterations.',
            's'    => 'Which sampler to use.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}
