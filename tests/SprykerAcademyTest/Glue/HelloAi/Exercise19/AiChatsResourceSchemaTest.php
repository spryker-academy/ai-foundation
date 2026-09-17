<?php

declare(strict_types=1);

namespace SprykerAcademyTest\Glue\HelloAi\Exercise19;

use Codeception\Test\Unit;
use Symfony\Component\Yaml\Yaml;

/**
 * Exercise 19, Task 1: The resource schema
 *
 * Verifies the API Platform schema of the ai-chats storefront resource.
 *
 * Run: vendor/bin/codecept run -c tests/SprykerAcademyTest/Glue/HelloAi/ Exercise19
 */
class AiChatsResourceSchemaTest extends Unit
{
    protected const string SCHEMA_PATH = __DIR__ . '/../../../../../src/SprykerAcademy/Glue/HelloAi/resources/api/storefront/ai-chats.resource.yml';

    protected const string PROCESSOR_CLASS = 'SprykerAcademy\Glue\HelloAi\Api\Storefront\Processor\AiChatsStorefrontProcessor';

    public function testSchemaDeclaresThePostOperation(): void
    {
        $operations = $this->loadResource()['operations'] ?? [];
        $types = array_column($operations, 'type');

        $this->assertContains('Post', $types, 'The resource needs an operation of type "Post". The client sends a message, it does not read a collection.');
    }

    public function testSchemaPointsToTheProcessor(): void
    {
        $this->assertSame(
            static::PROCESSOR_CLASS,
            $this->loadResource()['processor'] ?? null,
            'Set "processor" to the fully qualified class name of AiChatsStorefrontProcessor. API Platform calls it for every POST.',
        );
    }

    public function testMessageIsWritableAndRequired(): void
    {
        $property = $this->loadResource()['properties']['message'] ?? null;

        $this->assertNotNull($property, 'Declare a "message" property.');
        $this->assertTrue($property['writable'] ?? false, 'The "message" property must be writable, the client sends it.');
        $this->assertTrue($property['required'] ?? false, 'The "message" property must be required.');
    }

    public function testConversationReferenceIsWritableAndReadable(): void
    {
        $property = $this->loadResource()['properties']['conversationReference'] ?? null;

        $this->assertNotNull($property, 'Declare a "conversationReference" property.');
        $this->assertTrue($property['writable'] ?? false, 'The client sends "conversationReference" to continue a conversation, so it must be writable.');
        $this->assertTrue($property['readable'] ?? false, 'The response returns "conversationReference" so the client can send it back, so it must be readable.');
        $this->assertFalse($property['required'] ?? false, 'The first message of a conversation has no reference yet, so it must not be required.');
    }

    public function testAnswerIsReadOnly(): void
    {
        $property = $this->loadResource()['properties']['answer'] ?? null;

        $this->assertNotNull($property, 'Declare an "answer" property.');
        $this->assertTrue($property['readable'] ?? false, 'The "answer" property must be readable.');
        $this->assertFalse($property['writable'] ?? true, 'The "answer" comes from the LLM. The client must not be able to write it.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function loadResource(): array
    {
        $this->assertFileExists(static::SCHEMA_PATH, 'The schema file ai-chats.resource.yml is missing.');

        $schema = Yaml::parseFile(static::SCHEMA_PATH);

        return $schema['resource'] ?? [];
    }
}
