<?php

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setCacheFile(__DIR__.'/.php_cs.cache')
    ->setRules([
        '@PHP56Migration' => true,
        '@PHP70Migration' => true,
        '@PHP71Migration' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        'blank_line_after_opening_tag' => true,
        'single_line_after_imports' => false,
        'array_syntax' => ['syntax' => 'short'],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        'braces' => [
            'allow_single_line_closure' => true,
            'position_after_anonymous_constructs' => 'next',
            'position_after_control_structures' => 'next',
            'position_after_functions_and_oop_constructs' => 'next'
        ],
        'blank_line_before_statement' => [
            'statements' => ['declare', 'die', 'do', 'exit', 'for', 'foreach', 'goto', 'if', 'return', 'switch', 'throw', 'try', 'while', 'yield']
        ],
        'compact_nullable_typehint' => true,
        'cast_spaces' => ['space' => 'single'],
        'no_closing_tag' => true,
        'array_indentation' => true,
        'declare_equal_normalize' => ['space' => 'single'],
        'no_multiline_whitespace_around_double_arrow' => true,
        'not_operator_with_space' => false,
        'not_operator_with_successor_space' => true,
        'increment_style' => ['style' => 'post'],
        'no_unused_imports' => true,
        'explicit_indirect_variable' => true,
        'no_alternative_syntax' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => [
            'imports_order' => ['const', 'class', 'function']
        ],
        'single_import_per_statement' => false,
        'no_leading_import_slash' => true,
        'trailing_comma_in_multiline_array' => false,
        'line_ending' => true
    ])
;
