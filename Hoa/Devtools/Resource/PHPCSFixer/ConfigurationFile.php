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

// Hoa defined fixers.
$fixers = [
    'Author',
    'ControlFlowStatement',
    'Copyright',
    'NoBlankLinesBeforeEntity',
    'PhpdocConstructorReturn',
    'PhpdocThrows',
    'PhpdocVar'
];

$out = PhpCsFixer\Config::create();
$out->setCacheFile(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'php_cs.cache');

foreach ($fixers as $fixer) {
    require
        __DIR__ . DIRECTORY_SEPARATOR .
       'Fixer' . DIRECTORY_SEPARATOR .
       $fixer . '.php';

    $classname = 'Hoa\Devtools\Resource\PHPCsFixer\Fixer\\' . $fixer;
    $out->registerCustomFixers([new $classname()]);
}

return
    $out->setRules([
        '@PSR2'                              => true,
        'array_syntax'                       => ['syntax' => 'short'],
        'binary_operator_spaces'             => ['align_double_arrow' => true, 'align_equals' => true],
        'blank_line_after_opening_tag'       => true,
        'cast_spaces'                        => true,
        'concat_space'                       => ['spacing' => 'one'],
        'declare_strict_types'               => true,
        'list_syntax'                        => ['syntax' => 'short'],
        'no_blank_lines_after_class_opening' => true,
        'no_leading_import_slash'            => true,
        'no_unused_imports'                  => true,
        'no_whitespace_in_blank_line'        => true,
        'ordered_imports'                    => true,
        'phpdoc_no_access'                   => true,
        'pow_to_exponentiation'              => true,
        'random_api_migration'               => true,
        'return_type_declaration'            => ['space_before' => 'none'],
        'self_accessor'                      => true,
        'ternary_to_null_coalescing'         => true,
        'visibility_required'                => ['elements' => ['const', 'property', 'method']],
        'void_return'                        => true,

        // Hoa defined
        'Hoa/author'                       => true,
        'Hoa/control_flow_statement'       => true,
        'Hoa/copyright'                    => true,
        'Hoa/no_blank_lines_before_entity' => true,
        'Hoa/phpdoc_constructor_return'    => true,
        'Hoa/phpdoc_throws'                => true,
        'Hoa/phpdoc_var'                   => true
    ]);
