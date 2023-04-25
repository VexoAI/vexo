<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__ . '/'])
    ->exclude(__DIR__ . '/vendor');

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP82Migration' => true,
        '@PHP80Migration:risky' => true,
        'concat_space' => ['spacing' => 'one'],
        'increment_style' => false,
        'no_empty_comment' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_unused_imports' => true,
        'not_operator_with_space' => true,
        'phpdoc_indent' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_alias_tag' => true,
        'phpdoc_no_package' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_summary' => true,
        'phpdoc_to_comment' => true,
        'phpdoc_trim' => true,
        'single_blank_line_at_eof' => true,
        'single_line_after_imports' => true,
        'trailing_comma_in_multiline' => false,
        'yoda_style' => false
    ])
    ->setFinder($finder);
