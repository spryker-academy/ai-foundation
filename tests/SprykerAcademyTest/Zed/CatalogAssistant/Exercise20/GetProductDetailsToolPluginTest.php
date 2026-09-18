<?php

declare(strict_types=1);

namespace SprykerAcademyTest\Zed\CatalogAssistant\Exercise20;

use Codeception\Test\Unit;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolParameterInterface;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolPluginInterface;
use SprykerAcademy\Zed\CatalogAssistant\Communication\Plugin\AiFoundation\Tool\GetProductDetailsToolPlugin;

/**
 * Exercise 20, Task 1: The tool
 *
 * Verifies how GetProductDetailsToolPlugin describes itself to the LLM.
 *
 * Run: vendor/bin/codecept run -c tests/SprykerAcademyTest/Zed/CatalogAssistant/ Exercise20
 */
class GetProductDetailsToolPluginTest extends Unit
{
    public function testToolImplementsToolPluginInterface(): void
    {
        $this->assertInstanceOf(ToolPluginInterface::class, new GetProductDetailsToolPlugin(), 'GetProductDetailsToolPlugin must implement ToolPluginInterface.');
    }

    public function testToolNameIsGetProductDetails(): void
    {
        $this->assertSame('get_product_details', (new GetProductDetailsToolPlugin())->getName(), 'getName() must return "get_product_details". The system prompt refers to the tool by this name.');
    }

    public function testToolDescriptionTellsTheModelWhenToCallIt(): void
    {
        $description = (new GetProductDetailsToolPlugin())->getDescription();

        $this->assertGreaterThan(40, strlen($description), 'getDescription() must explain what the tool returns and when to call it. The model decides on this text alone.');
        $this->assertStringContainsStringIgnoringCase('sku', $description, 'Mention that the tool works by SKU.');
    }

    public function testToolDeclaresARequiredSkuParameter(): void
    {
        $parameters = (new GetProductDetailsToolPlugin())->getParameters();

        $this->assertCount(1, $parameters, 'The tool needs exactly one parameter: sku.');
        $this->assertContainsOnlyInstancesOf(ToolParameterInterface::class, $parameters, 'Parameters must be ToolParameter objects.');

        $parameter = $parameters[0];
        $this->assertSame('sku', $parameter->getName(), 'The parameter must be named "sku". The business layer reads $arguments["sku"].');
        $this->assertSame('string', $parameter->getType(), 'The "sku" parameter must be of type "string".');
        $this->assertTrue($parameter->isRequired(), 'The "sku" parameter must be required.');
    }
}
