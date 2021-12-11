<?php

declare(strict_types=1);

use PhpParser\Node\Expr\MethodCall;
use Rector\RectorGenerator\Provider\RectorRecipeProvider;
use Rector\RectorGenerator\ValueObject\RectorRecipe;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

// run "bin/rector generate" to a new Rector basic schema + tests from this config
return static function (ContainerConfigurator $containerConfigurator): void {
    // [REQUIRED]

    $rectorRecipe = new RectorRecipe(
        // [RECTOR CORE CONTRIBUTION - REQUIRED]
        // package name, basically namespace part in `rules/<package>/src`, use PascalCase
        'Naming',

        // name, basically short class name; use PascalCase
        'RenameMethodCallRector',

        // 1+ node types to change, pick from classes here https://github.com/nikic/PHP-Parser/tree/master/lib/PhpParser/Node
        // the best practise is to have just 1 type here if possible, and make separated rule for other node types
        [MethodCall::class],

        // describe what the rule does
        '"something()" will be renamed to "somethingElse()"',

        // code before change
        // this is used for documentation and first test fixture
        <<<'CODE_SAMPLE'
<?php

class SomeClass
{
    public function run()
    {
        $this->something();
    }
}

CODE_SAMPLE
        // code after change
        , <<<'CODE_SAMPLE'
<?php

class SomeClass
{
    public function run()
    {
        $this->somethingElse();
    }
}
CODE_SAMPLE
    );

    // [OPTIONAL - UNCOMMENT TO USE]

    // links to useful websites, that explain why the change is needed
    // $rectorRecipe->setResources([
    //      'https://github.com/symfony/symfony/blob/704c648ba53be38ef2b0105c97c6497744fef8d8/UPGRADE-6.0.md'
    // ]);

    // is the rule configurable? add default configuration here
    // $rectorRecipe->setConfiguration(['SOME_CONSTANT_KEY' => ['before' => 'after']]);

    // [RECTOR CORE CONTRIBUTION - OPTIONAL]
    // set the rule belongs to; is optional, because e.g. generic rules don't need a specific set to belong to
    // $rectorRecipe->setSetFilePath(\Rector\Set\ValueObject\SetList::NAMING);

    $services = $containerConfigurator->services();

    $services->set(RectorRecipeProvider::class)
        ->arg('$rectorRecipe', ValueObjectInliner::inlineArgumentObject($rectorRecipe, $services));
};
