<?php

declare(strict_types=1);

namespace SprykerAcademyTest\Zed\CatalogAssistant\Exercise20;

use Codeception\Test\Unit;
use SimpleXMLElement;

/**
 * Exercise 20, Task 3: The structured answer
 *
 * Verifies the transfer definition the LLM must answer with.
 *
 * Run: vendor/bin/codecept run -c tests/SprykerAcademyTest/Zed/CatalogAssistant/ Exercise20
 */
class ProductAnswerTransferDefinitionTest extends Unit
{
    protected const string TRANSFER_PATH = __DIR__ . '/../../../../../src/SprykerAcademy/Shared/CatalogAssistant/Transfer/catalog_assistant.transfer.xml';

    /**
     * @return array<string, array<string>>
     */
    public static function propertyProvider(): array
    {
        return [
            'answer' => ['answer', 'string'],
            'isInStock' => ['isInStock', 'bool'],
            'confidence' => ['confidence', 'string'],
            'relatedSkus' => ['relatedSkus', 'string[]'],
        ];
    }

    /**
     * @dataProvider propertyProvider
     */
    public function testProductAnswerHasTypedPropertyWithDescription(string $name, string $type): void
    {
        $property = $this->findProperty($name);

        $this->assertNotNull($property, sprintf('Declare a "%s" property on the ProductAnswer transfer.', $name));
        $this->assertSame($type, (string)$property['type'], sprintf('The "%s" property must be of type "%s".', $name, $type));
        $this->assertGreaterThan(
            20,
            strlen((string)$property['description']),
            sprintf('Give "%s" a description. AiFoundation sends it to the model as the JSON schema description, it is the only instruction the model gets for this field.', $name),
        );
    }

    public function testEveryDescriptionForbidsNull(): void
    {
        foreach (['isInStock', 'relatedSkus'] as $name) {
            $description = strtolower((string)$this->findProperty($name)['description']);
            $this->assertStringContainsString('never null', $description, sprintf('Tell the model what to return for "%s" when the value is unknown, and that it is never null. AiFoundation rejects answers with null properties.', $name));
        }
    }

    protected function findProperty(string $name): ?SimpleXMLElement
    {
        $this->assertFileExists(static::TRANSFER_PATH);
        $xml = simplexml_load_file(static::TRANSFER_PATH);
        $xml->registerXPathNamespace('t', 'spryker:transfer-01');
        $matches = $xml->xpath(sprintf('//t:transfer[@name="ProductAnswer"]/t:property[@name="%s"]', $name));

        return $matches[0] ?? null;
    }
}
