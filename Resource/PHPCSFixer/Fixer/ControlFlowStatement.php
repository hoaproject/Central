<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Devtools\Resource\PHPCSFixer\Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Class \Hoa\Devtools\Resource\PHPCSFixer\Fixer\ControlFlowStatement.
 *
 * Add a newline before `return`, `break` and `continue` if needed.
 * Inspired by the `ReturnFixer` class, provided with `php-cs-fixer`.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class ControlFlowStatement extends AbstractFixer
{
    protected function applyfix(SplFileInfo $file, Tokens $tokens)
    {
        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind([T_RETURN, T_BREAK, T_CONTINUE])) {
                continue;
            }

            $prevNonWhitespaceToken = $tokens[$tokens->getPrevNonWhitespace($index)];

            if (!$prevNonWhitespaceToken->equalsAny([';', '}'])) {
                continue;
            }

            $prevToken = $tokens[$index - 1];

            if ($prevToken->isWhitespace()) {
                $parts      = explode("\n", $prevToken->getContent());
                $countParts = count($parts);

                if (1 === $countParts) {
                    $prevToken->setContent(rtrim($prevToken->getContent(), " \t") . "\n\n");
                } elseif (count($parts) <= 2) {
                    $prevToken->setContent("\n" . $prevToken->getContent());
                }
            } else {
                $tokens->insertAt($index, new Token([T_WHITESPACE, "\n\n"]));

                ++$index;
                ++$limit;
            }
        }

        return $tokens->generateCode();
    }

    public function getDefinition()
    {
        return new FixerDefinition(
            'Add a newline before `return`, `break` and `continue` if needed.',
            [new CodeSample("<?php\nswitch (…) {\n    case …:\n        …\n\n        break;")]
        );
    }

    public function isCandidate(Tokens $tokens)
    {
        return
            $tokens->isTokenKindFound(T_RETURN) ||
            $tokens->isTokenKindFound(T_BREAK) ||
            $tokens->isTokenKindFound(T_CONTINUE);
    }

    public function getName()
    {
        return 'Hoa/control_flow_statement';
    }
}
