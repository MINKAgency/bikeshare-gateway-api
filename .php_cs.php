<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in('src');

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        '-method_argument_default_value',
        '-unalign_double_arrow',
        '-unalign_equals',
        'align_equals',
        'align_double_arrow',
        'short_array_syntax',
        '-phpdoc_inline_tag',
        '-pre_increment',
        'newline_after_open_tag',
        'ordered_use',
        'phpdoc_order',
    ])
    ->finder($finder)
    ->setUsingCache(true)
;
