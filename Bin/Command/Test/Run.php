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
 * \Hoa\Test
 */
-> import('Test.~')

/**
 * \Hoa\Test\Praspel
 */
-> import('Test.Praspel.~')

/**
 * \Hoa\Test\Praspel\Visitor\Praspel
 */
-> import('Test.Praspel.Visitor.Praspel')

/**
 * \Hoa\File\Finder
 */
-> import('File.Finder')

/**
 * \Hoa\File\Write
 */
-> import('File.Write');

}

namespace Bin\Command\Test {

/**
 * Class \Bin\Command\Test\Out.
 *
 * Run test stream.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Out extends \Hoa\File\Write {

    public $self = null;

    public function writeArray ( Array $array ) {

        $_    = $array['log'];
        $args = null;
        $res  = null;

        if(\Hoa\Test\Praspel::LOG_TYPE_INVARIANT === $_['type']) {

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

    public function writeOpenIteration ( \Hoa\Core\Event\Bucket $event ) {

        $data = $event->getData();

        cout($this->self->stylize('Iteration #' . $data['iteration'], 'h2'));
        cout();
        cout($this->self->stylize('Runtime', 'info'));

        return;
    }

    public function writeCloseIteration ( \Hoa\Core\Event\Bucket $event ) {

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

class ContractCovering extends \Hoa\Test\Praspel\Visitor\Praspel {

    public function visitDomainDisjunction ( \Hoa\Visitor\Element $element,
                                             &$handle = null, $eldnah = null ) {

        if(!($element instanceof \Hoa\Test\Praspel\Variable))
            return parent::visitDomainDisjunction($element, $handle, $eldnah);

        $domains = $this->formatArguments($element->getDomains());
        $domain  = $element->hasChoosenDomain()
                       ? $element->getChoosenDomain()
                       : null;
        $i       = 0;

        foreach($element->getDomains() as $d) {

            if($d === $domain)
                $domains[$i] = \Hoa\Console\Chrome\Style::stylize(
                    $domains[$i],
                    \Hoa\Console\Chrome\Style::COLOR_FOREGROUND_YELLOW
                );
            else
                $domains[$i] = $domains[$i];

            ++$i;
        }

        return $element->getName() . ': ' . implode(' or ', $domains);
    }
}

class Run extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var \Bin\Command\Test\Run array
     */
    protected $options = array(
        array('revision',  \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'r'),
        array('file',      \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'f'),
        array('class',     \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'c'),
        array('method',    \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'm'),
        array('iteration', \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'i'),
        array('sampler',   \Hoa\Console\GetOption::REQUIRED_ARGUMENT, 's'),
        array('help',      \Hoa\Console\GetOption::NO_ARGUMENT,       'h'),
        array('help',      \Hoa\Console\GetOption::NO_ARGUMENT,       '?')
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

        $test       = new \Hoa\Test();
        $repos      = $test->getParameters()->getFormattedParameter('repository');
        $finder     = new \Hoa\File\Finder(
            $repos,
            \Hoa\File\Finder::LIST_DIRECTORY,
            \Hoa\File\Finder::SORT_MTIME |
            \Hoa\File\Finder::SORT_REVERSE
        );
        $revision   = basename($finder->getIterator()->current());
        $rev        = false;

        while(false !== $c = parent::getOption($v)) switch($c) {

            case 'r':
                $handle   = $this->parse->parseSpecialValue(
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

        if(false === $rev) {

            cout('No revision was given; assuming ' .
                 $this->stylize('HEAD', 'info') . ', i.e ' .
                 $this->stylize($revision, 'info') . '.');
            cout();
        }

        $repository = $repos . $revision;

        if(!is_dir($repository))
            throw new \Hoa\Console\Exception(
                'Repository %s does not exist.', 0, $repository);

        $test->getParameters()->setParameter('revision', $revision . DS);

        if(null === $file)
            return $this->usage();

        $instrumented = $test->getParameters()->getFormattedParameter('instrumented');

        if(!file_exists($instrumented . $file))
            throw new \Hoa\Console\Exception(
                'File %s does not exist in repository %s.',
                1, array($file, $repository));

        require_once $instrumented . $file;

        if(false === $sampler)
            return;

        if(   null === $class
           || null === $method)
            return $this->usage();

        $out = new Out();
        $out->self = $this; // berk…

        event('hoa://Event/Log/' . \Hoa\Test\Praspel::LOG_CHANNEL)
            ->attach($out);
        event('hoa://Event/Test/Sample:open-iteration')
            ->attach($out, 'writeOpenIteration');
        event('hoa://Event/Test/Sample:close-iteration')
            ->attach($out, 'writeCloseIteration');

        for($i = 1; $iteration > 0; --$iteration, ++$i) {

            try {

                $contractId = $class . '::' . $method;
                $test->sample($contractId, $class, $method);
                $contract   = \Hoa\Test\Praspel::getInstance()->getContract(
                    $contractId
                );
            }
            catch ( \Hoa\Test\Exception $e ) {

                throw new \Hoa\Console\Exception(
                    $e->getFormattedMessage(), $e->getCode());
            }

            cout();
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

        cout('Usage   : test:run <options>');
        cout('Options :');
        cout($this->makeUsageOptionsList(array(
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

        return;
    }
}

}
