<?php

namespace Hoa\Devtools\Resource\PHPCsFixer\Fixer;

use Symfony\CS\AbstractFixer;
use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Keep copyright up-to-date.
 */
class Copyright extends AbstractFixer
{
    public function fix(SplFileInfo $file, $content)
    {
        $tokens   = Tokens::fromCode($content);
        $thisYear = date('Y');

        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $token) {

            $token->setContent(
                preg_replace_callback(
                    '/Copyright © (?<firstYear>\d{4})-\d{4}, [^\.]+/',
                    function($matches) use($thisYear) {

                        return
                            'Copyright © ' .
                            $matches['firstYear'] . '-' . $thisYear . ', ' .
                            'Hoa community';
                    },
                    $token->getContent()
                )
            );

            $docBlock    = new DocBlock($token->getContent());
            $annotations = $docBlock->getAnnotationsOfType('copyright');

            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                $line = $docBlock->getLine($annotation->getStart());
                $line->setContent(
                    ' * @copyright  Copyright © 2007-' . $thisYear .
                    ' Hoa community' . "\n"
                );
            }

            $token->setContent($docBlock->getContent());

        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'Keep copyright up-to-date.';
    }

    public function getName()
    {
        return 'copyright';
    }

    public function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }
}
