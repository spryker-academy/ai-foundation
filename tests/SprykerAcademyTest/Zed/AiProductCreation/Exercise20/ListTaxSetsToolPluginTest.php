<?php

declare(strict_types=1);

namespace SprykerAcademyTest\Zed\AiProductCreation\Exercise20;

use Codeception\Test\Unit;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolPluginInterface;
use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool\ListTaxSetsToolPlugin;

/**
 * Exercise 20, Task 1: Your first AI tool
 *
 * Verifies that ListTaxSetsToolPlugin describes itself correctly to the AI model.
 *
 * Run: vendor/bin/codecept run -c tests/SprykerAcademyTest/Zed/AiProductCreation/ Exercise20
 */
class ListTaxSetsToolPluginTest extends Unit
{
    public function testToolImplementsToolPluginInterface(): void
    {
        $this->assertInstanceOf(
            ToolPluginInterface::class,
            new ListTaxSetsToolPlugin(),
            'ListTaxSetsToolPlugin must implement ToolPluginInterface.',
        );
    }

    public function testToolNameIsSnakeCaseIdentifier(): void
    {
        $this->assertSame(
            'list_tax_sets',
            (new ListTaxSetsToolPlugin())->getName(),
            'getName() must return "list_tax_sets". The system prompt refers to the tool by this exact name.',
        );
    }

    public function testToolDescriptionExplainsWhenToUseIt(): void
    {
        $description = (new ListTaxSetsToolPlugin())->getDescription();

        $this->assertGreaterThan(
            20,
            strlen($description),
            'getDescription() must return a meaningful sentence. The AI model reads it to decide when to call the tool.',
        );
        $this->assertStringContainsStringIgnoringCase(
            'tax',
            $description,
            'getDescription() should mention tax sets.',
        );
    }

    public function testToolHasNoParameters(): void
    {
        $this->assertSame(
            [],
            (new ListTaxSetsToolPlugin())->getParameters(),
            'list_tax_sets takes no input, so getParameters() must return an empty array.',
        );
    }
}
