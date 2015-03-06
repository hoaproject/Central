<?php

use Hoa\Devtools\Resource\PHPCsFixer\Fixer;

// Hoa defined fixers.
$fixers = [
    'PhpdocAccess.php',
    'PhpdocThrows.php',
    'PhpdocVar.php'
];

foreach ($fixers as $fixer) {
    require
        __DIR__ . DIRECTORY_SEPARATOR .
       'Fixer' . DIRECTORY_SEPARATOR .
       $fixer;
}

return
    Symfony\CS\Config\Config::create()
    ->addCustomFixer(new Fixer\PhpdocAccess())
    ->addCustomFixer(new Fixer\PhpdocThrows())
    ->addCustomFixer(new Fixer\PhpdocVar())
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        'align_double_arrow',
        'align_equals',
        'concat_with_spaces',
        'ordered_use',
        'remove_leading_slash_use',
        'return',
        'short_array_syntax',
        'spaces_cast',
        'unused_use',

        // Hoa defined
        'phpdoc_access',
        'phpdoc_throws',
        'phpdoc_var'
    ]);
