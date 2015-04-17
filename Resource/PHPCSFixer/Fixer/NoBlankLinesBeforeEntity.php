<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Ivan Enderlin. All rights reserved.
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

use Symfony\CS\AbstractLinesBeforeNamespaceFixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Class \Hoa\Devtools\Resource\PHPCSFixer\Fixer\NoBlankLinesBeforeEntity.
 *
 * Remove blank lines before entity declarations (class, interface, trait etc.).
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2015 Ivan Enderlin.
 * @license    New BSD License
 */

class NoBlankLinesBeforeEntity extends AbstractLinesBeforeNamespaceFixer
{
    public function fix(SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_CLASS) ||
                $token->isGivenKind(T_INTERFACE) ||
                $token->isGivenKind(T_TRAIT)) {
                $docCommentIndex = $tokens->getTokenOfKindSibling(
                    $index,
                    -1,
                    [[T_DOC_COMMENT]],
                    false
                );
                $firstSignificantIndex = $tokens->getNextNonWhitespace($docCommentIndex);
                $this->fixLinesBeforeNamespace($tokens, $firstSignificantIndex, 1);
            }
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'Remove blank lines before entity declarations.';
    }

    public function getName()
    {
        return 'no_blank_lines_before_entity';
    }

    public function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }
}
