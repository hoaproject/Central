<?php

// Hoa defined fixers.
$fixers = [
    'Author',
    'ControlFlowStatement',
    'Copyright',
    'OpeningTag',
    'PhpdocAccess',
    'PhpdocThrows',
    'PhpdocVar'
];

$out = Symfony\CS\Config\Config::create();
$out->level(Symfony\CS\FixerInterface::PSR2_LEVEL);

foreach ($fixers as $fixer) {
    require
        __DIR__ . DIRECTORY_SEPARATOR .
       'Fixer' . DIRECTORY_SEPARATOR .
       $fixer . '.php';

    $classname = 'Hoa\Devtools\Resource\PHPCsFixer\Fixer\\' . $fixer;
    $out->addCustomFixer(new $classname());
}

return
    $out->fixers([
        'align_double_arrow',
        'align_equals',
        'concat_with_spaces',
        'no_blank_lines_after_class_opening',
        'ordered_use',
        'remove_leading_slash_use',
        'remove_leading_slash_uses',
        'self_accessor',
        'short_array_syntax',
        'spaces_cast',
        'unused_use',
        'whitespacy_lines',

        // Hoa defined
        'author',
        'control_flow_statement',
        'copyright',
        'opening_tag',
        'phpdoc_access',
        'phpdoc_throws',
        'phpdoc_var'
    ]);
