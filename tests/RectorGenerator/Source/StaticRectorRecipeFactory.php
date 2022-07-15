<?php

declare(strict_types=1);

namespace Rector\RectorGenerator\Tests\RectorGenerator\Source;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Node\ClassMethod;
use Rector\RectorGenerator\Exception\ShouldNotHappenException;
use Rector\RectorGenerator\ValueObject\RectorRecipe;

final class StaticRectorRecipeFactory
{
    public static function createRectorRecipe(string $setFilePath, bool $isRectorRepository): RectorRecipe
    {
        if (! file_exists($setFilePath)) {
            $message = sprintf('Set file path "%s" was not found', $setFilePath);
            throw new ShouldNotHappenException($message);
        }

        $rectorRecipe = new RectorRecipe(
            'Utils',
            'WhateverRector',
            [MethodCall::class],
            'Change $service->arg(...) to $service->call(...)',
    <<<'CODE_SAMPLE'
<?php

$result = [];
echo 'code before';
CODE_SAMPLE
            , <<<'CODE_SAMPLE'
<?php

$result = [];
echo 'code after';
CODE_SAMPLE
        );

        $rectorRecipe->setConfiguration([
            'renamedPackages' => [
                'old_package_name' => 'new_package_name'
            ]
        ]);

        $rectorRecipe->setIsRectorRepository($isRectorRepository);
        if ($isRectorRepository) {
            $rectorRecipe->setPackage('ModeratePackage');
        }

        $rectorRecipe->setSetFilePath($setFilePath);

        return $rectorRecipe;
    }
}
