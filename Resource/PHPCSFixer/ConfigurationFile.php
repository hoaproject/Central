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

// Hoa defined fixers.
$fixers = [
    'Author',
    'ControlFlowStatement',
    'Copyright',
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
        'blankline_after_open_tag',
        'concat_with_spaces',
        'no_blank_lines_after_class_opening',
        'ordered_use',
        'phpdoc_no_access',
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
        'phpdoc_throws',
        'phpdoc_var'
    ]);
