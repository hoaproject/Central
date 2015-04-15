<?php

namespace Hoa\Devtools\Resource\PHPCsFixer\Fixer;

use Symfony\CS\AbstractFixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Add a newline before `return`, `break` and `continue` if needed.
 * Inspired by the `ReturnFixer` class, provided with `php-cs-fixer`.
 */
class ControlFlowStatement extends AbstractFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

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

    public function getDescription()
    {
        return 'Add a newline before `return`, `break` and `continue` if needed.';
    }

    public function getName()
    {
        return 'control_flow_statement';
    }

    public function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }
}
