<?php

namespace Hoa\Devtools\Resource\PHPCsFixer\Fixer;

use Symfony\CS\AbstractFixer;
use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Remove `@access`.
 */
class PhpdocAccess extends AbstractFixer
{
    public function fix(SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $token) {

            $docBlock    = new DocBlock($token->getContent());
            $annotations = $docBlock->getAnnotationsOfType('access');

            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                $annotation->remove();
            }

            $token->setContent($docBlock->getContent());
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'Remove `@access`.';
    }

    public function getName()
    {
        return 'phpdoc_access';
    }

    public function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }
}
