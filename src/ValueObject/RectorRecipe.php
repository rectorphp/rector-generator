<?php

declare(strict_types=1);

namespace Rector\RectorGenerator\ValueObject;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use Rector\RectorGenerator\Exception\ConfigurationException;
use Rector\RectorGenerator\Exception\ShouldNotHappenException;
use Webmozart\Assert\Assert;

final class RectorRecipe
{
    /**
     * @var string
     */
    private const PACKAGE_UTILS = 'Utils';

    private ?string $name = null;

    private ?string $codeBefore = null;

    private ?string $codeAfter = null;

    private ?string $category = null;

    /**
     * @var string[]
     */
    private array $nodeTypes = [];

    /**
     * Use default package name, if not overriden manually
     */
    private string $package = self::PACKAGE_UTILS;

    /**
     * @param string[] $nodeTypes
     */
    public function __construct(
        string $package,
        string $name,
        array $nodeTypes,
        private string $description,
        string $codeBefore,
        string $codeAfter
    ) {
        $this->setPackage($package);
        $this->setName($name);
        $this->setNodeTypes($nodeTypes);

        if ($codeBefore === $codeAfter) {
            throw new ConfigurationException('Code before and after are identical. They have to be different');
        }

        $this->setCodeBefore($codeBefore);
        $this->setCodeAfter($codeAfter);

        $this->resolveCategory($nodeTypes);
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return $this->nodeTypes;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCodeBefore(): string
    {
        return $this->codeBefore;
    }

    public function getCodeAfter(): string
    {
        return $this->codeAfter;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * For tests
     *
     * @api
     */
    public function setPackage(string $package): void
    {
        if (is_file($package)) {
            $message = sprintf(
                'The "%s()" method only accepts package name, file path "%s" given',
                __METHOD__,
                $package
            );

            throw new ShouldNotHappenException($message);
        }

        $this->package = $package;
    }

    private function setName(string $name): void
    {
        if (! \str_ends_with($name, 'Rector')) {
            $message = sprintf('Rector name "%s" must end with "Rector"', $name);
            throw new ConfigurationException($message);
        }

        $this->name = $name;
    }

    /**
     * @param string[] $nodeTypes
     */
    private function setNodeTypes(array $nodeTypes): void
    {
        foreach ($nodeTypes as $nodeType) {
            // avoid phpstan class method that is never traversed
            Assert::isNotA($nodeType, 'PHPStan\Node\ClassMethod');

            if (is_a($nodeType, Node::class, true)) {
                continue;
            }

            $message = sprintf(
                'Node type "%s" does not exist, implement "%s" interface or is not imported in "rector-recipe.php"',
                $nodeType,
                Node::class
            );
            throw new ShouldNotHappenException($message);
        }

        if (count($nodeTypes) < 1) {
            $message = sprintf('"$nodeTypes" argument requires at least one item, e.g. "%s"', FuncCall::class);
            throw new ConfigurationException($message);
        }

        $this->nodeTypes = $nodeTypes;
    }

    private function setCodeBefore(string $codeBefore): void
    {
        $this->codeBefore = $this->normalizeCode($codeBefore);
    }

    private function setCodeAfter(string $codeAfter): void
    {
        $this->codeAfter = $this->normalizeCode($codeAfter);
    }

    /**
     * @param string[] $nodeTypes
     */
    private function resolveCategory(array $nodeTypes): void
    {
        $this->category = (string) Strings::after($nodeTypes[0], '\\', -1);
    }

    private function normalizeCode(string $code): string
    {
        if (\str_starts_with($code, '<?php')) {
            $code = ltrim($code, '<?php');
        }

        return trim($code);
    }
}
