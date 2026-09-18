<?php

declare(strict_types=1);

namespace SprykerAcademyTest\Zed\CatalogAssistant\Exercise20;

use Codeception\Test\Unit;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolPluginInterface;
use SprykerAcademy\Shared\CatalogAssistant\CatalogAssistantConstants;
use SprykerAcademy\Zed\CatalogAssistant\Communication\CatalogAssistantCommunicationFactory;
use SprykerAcademy\Zed\CatalogAssistant\Communication\Plugin\AiFoundation\CatalogToolSetPlugin;

/**
 * Exercise 20, Task 2: The tool set
 *
 * Run: vendor/bin/codecept run -c tests/SprykerAcademyTest/Zed/CatalogAssistant/ Exercise20
 */
class CatalogToolSetPluginTest extends Unit
{
    public function testToolSetNameMatchesTheConstantTheApiRequests(): void
    {
        $this->assertSame(
            CatalogAssistantConstants::TOOL_SET_CATALOG,
            $this->createToolSetPlugin()->getName(),
            'getName() must return CatalogAssistantConstants::TOOL_SET_CATALOG. The storefront processor requests the tools by this name.',
        );
    }

    public function testToolSetContainsTheProductDetailsTool(): void
    {
        $tools = $this->createToolSetPlugin()->getTools();

        $this->assertContainsOnlyInstancesOf(ToolPluginInterface::class, $tools, 'getTools() must return ToolPluginInterface instances only.');
        $this->assertContains(
            'get_product_details',
            array_map(static fn (ToolPluginInterface $tool): string => $tool->getName(), $tools),
            'getTools() must contain the tool created by CatalogAssistantCommunicationFactory::createGetProductDetailsToolPlugin().',
        );
    }

    protected function createToolSetPlugin(): CatalogToolSetPlugin
    {
        $toolSetPlugin = new CatalogToolSetPlugin();
        $toolSetPlugin->setFactory(new CatalogAssistantCommunicationFactory());

        return $toolSetPlugin;
    }
}
