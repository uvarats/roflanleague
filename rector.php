<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/migrations',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // register a single rule
    //$rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    // define sets of rules
//        $rectorConfig->sets([
//            \Rector\Set\ValueObject\SetList::CODE_QUALITY,
//            \Rector\Set\ValueObject\SetList::CODING_STYLE
//        ]);

    $rectorConfig->sets([
        \Rector\Set\ValueObject\SetList::PHP_82
    ]);
};
