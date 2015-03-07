<?php

namespace Hoa\Devtools\Resource\PHPCsFixer\Fixer;

use Symfony\CS\AbstractFixer;
use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Transform `@throw` to `@throws`.
 */
class PhpdocThrows extends AbstractFixer
{
    public function fix(SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $token) {

            $docBlock    = new DocBlock($token->getContent());
            $annotations = $docBlock->getAnnotationsOfType('throw');

            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {

                $line = $docBlock->getLine($annotation->getStart());
                $line->setContent(
                    str_replace(
                        '@throw ',
                        '@throws',
                        $line->getContent()
                    )
                );

            }

            $token->setContent($docBlock->getContent());

        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'Replace `@throw` by `@throws`.';
    }

    public function getName()
    {
        return 'phpdoc_throws';
    }

    public function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }
}
