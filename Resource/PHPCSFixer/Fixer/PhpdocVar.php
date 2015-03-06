<?php

namespace Hoa\Devtools\Resource\PHPCsFixer\Fixer;

use Symfony\CS\AbstractFixer;
use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Transform the old `@var` syntax to the standard one, i.e.:
 *
 *     @var $classOwner $type
 *     @var $classType  object
 *
 * becomes:
 *
 *     @var type
 *     @var $classType
 */
class PhpdocVar extends AbstractFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $token) {

            $doc         = new DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType('var');

            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {

                $line        = $doc->getLine($annotation->getStart());
                $lineContent = $line->getContent();

                if (0 !== preg_match('/^(?<before>.*?@var )(?<one>[^\s]+) (?<two>\w+)/', $lineContent, $matches)) {

                    if ('object' === $matches['two']) {
                        $line->setContent($matches['before'] . $matches['one'] . "\n");
                    } else {
                        $line->setContent($matches['before'] . $matches['two'] . "\n");
                    }

                }

            }

            $token->setContent($doc->getContent());
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return '@var must contain one element.';
    }

    public function getName()
    {
        return 'phpdoc_var';
    }

    public function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }
}
