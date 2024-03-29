<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(\Rector\ModeratePackage\Rector\MethodCall\WhateverRector::class);
};
