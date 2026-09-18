<?php

declare(strict_types=1);

namespace SprykerAcademyTest\Zed\AiProductCreation\Exercise21;

use Codeception\Test\Unit;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolPluginInterface;
use SprykerAcademy\Shared\AiProductCreation\AiProductCreationConstants;
use SprykerAcademy\Zed\AiProductCreation\Communication\AiProductCreationCommunicationFactory;
use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\ProductCreationToolSetPlugin;

/**
 * Exercise 21, Task 3: The tool set
 *
 * Verifies that ProductCreationToolSetPlugin groups all product creation tools under one name.
 *
 * Run: vendor/bin/codecept run -c tests/SprykerAcademyTest/Zed/AiProductCreation/ Exercise21
 */
class ProductCreationToolSetPluginTest extends Unit
{
    protected const array EXPECTED_TOOL_NAMES = [
        'approve_product',
        'assign_product_to_category',
        'create_product',
        'generate_product_image',
        'list_categories',
        'list_tax_sets',
        'set_product_image',
        'set_product_price',
        'set_product_stock',
    ];

    public function testToolSetNameMatchesTheConstantTheAgentUses(): void
    {
        $this->assertSame(
            AiProductCreationConstants::TOOL_SET_PRODUCT_CREATION,
            $this->createToolSetPlugin()->getName(),
            'getName() must return AiProductCreationConstants::TOOL_SET_PRODUCT_CREATION. The agent requests its tools by this name.',
        );
    }

    public function testToolSetReturnsAllNineTools(): void
    {
        $tools = $this->createToolSetPlugin()->getTools();

        $this->assertContainsOnlyInstancesOf(ToolPluginInterface::class, $tools, 'getTools() must return ToolPluginInterface instances only.');

        $toolNames = array_map(static fn (ToolPluginInterface $tool): string => $tool->getName(), $tools);
        sort($toolNames);

        $this->assertSame(
            static::EXPECTED_TOOL_NAMES,
            $toolNames,
            'getTools() must return all nine tools created by AiProductCreationCommunicationFactory.',
        );
    }

    protected function createToolSetPlugin(): ProductCreationToolSetPlugin
    {
        $toolSetPlugin = new ProductCreationToolSetPlugin();
        $toolSetPlugin->setFactory(new AiProductCreationCommunicationFactory());

        return $toolSetPlugin;
    }
}
