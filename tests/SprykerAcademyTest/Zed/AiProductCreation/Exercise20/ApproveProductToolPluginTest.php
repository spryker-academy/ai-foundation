<?php

declare(strict_types=1);

namespace SprykerAcademyTest\Zed\AiProductCreation\Exercise20;

use Codeception\Test\Unit;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolParameterInterface;
use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool\ApproveProductToolPlugin;

/**
 * Exercise 20, Task 2: Tool parameters
 *
 * Verifies that ApproveProductToolPlugin declares the parameters the AI model must provide.
 *
 * Run: vendor/bin/codecept run -c tests/SprykerAcademyTest/Zed/AiProductCreation/ Exercise20
 */
class ApproveProductToolPluginTest extends Unit
{
    public function testToolDeclaresTwoParameters(): void
    {
        $parameters = (new ApproveProductToolPlugin())->getParameters();

        $this->assertCount(2, $parameters, 'approve_product must declare exactly two parameters: sku and status.');
        $this->assertContainsOnlyInstancesOf(
            ToolParameterInterface::class,
            $parameters,
            'Every parameter must be a ToolParameter object.',
        );
    }

    public function testSkuParameterIsARequiredString(): void
    {
        $parameter = $this->findParameter('sku');

        $this->assertSame('string', $parameter->getType(), 'The "sku" parameter must be of type "string".');
        $this->assertTrue($parameter->isRequired(), 'The "sku" parameter must be required.');
        $this->assertNotSame('', $parameter->getDescription(), 'The "sku" parameter needs a description for the AI model.');
    }

    public function testStatusParameterIsAnOptionalString(): void
    {
        $parameter = $this->findParameter('status');

        $this->assertSame('string', $parameter->getType(), 'The "status" parameter must be of type "string".');
        $this->assertFalse($parameter->isRequired(), 'The "status" parameter must be optional. The business logic defaults to "approved".');
    }

    protected function findParameter(string $name): ToolParameterInterface
    {
        foreach ((new ApproveProductToolPlugin())->getParameters() as $parameter) {
            if ($parameter->getName() === $name) {
                return $parameter;
            }
        }

        $this->fail(sprintf('approve_product must declare a parameter named "%s".', $name));
    }
}
