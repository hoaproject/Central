<?php

namespace Hoa\Devtools\Resource\PHPCsFixer\Fixer;

use Symfony\CS\AbstractFixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Tokenizer\Token;
use SplFileInfo;

/**
 * Ensure there is a new line after the <php.
 */
class OpeningTag extends AbstractFixer
{
    public function fix(SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if (!$tokens->isMonolithicPhp()) {
            return $content;
        }

        if (!$tokens[0]->isGivenKind(T_OPEN_TAG)) {
            return $content;
        }

        if (!$tokens[1]->isWhitespace()) {
            $tokens[0]->setContent('<?php');
            $tokens->insertAt(1, new Token([T_WHITESPACE, "\n\n"]));
        } else {
            $tokens[1]->setContent("\n");
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'Ensure there is a new line after the opening tag.';
    }

    public function getName()
    {
        return 'opening_tag';
    }

    public function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }
}
