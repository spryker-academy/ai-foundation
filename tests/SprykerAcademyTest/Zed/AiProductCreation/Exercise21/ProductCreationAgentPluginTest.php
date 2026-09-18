<?php

declare(strict_types=1);

namespace SprykerAcademyTest\Zed\AiProductCreation\Exercise21;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\BackofficeAssistantPromptRequestTransfer;
use Generated\Shared\Transfer\ProductCreationAgentResponseTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use Spryker\Shared\AiFoundation\AiFoundationConstants;
use Spryker\Zed\AiFoundation\Business\AiFoundationFacadeInterface;
use SprykerAcademy\Shared\AiProductCreation\AiProductCreationConstants;
use SprykerAcademy\Zed\AiProductCreation\AiProductCreationConfig;
use SprykerAcademy\Zed\AiProductCreation\Communication\AiProductCreationCommunicationFactory;
use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\Agent\ProductCreationAgentPlugin;

/**
 * Exercise 21, Task 4: The agent
 *
 * Verifies that ProductCreationAgentPlugin builds the right prompt request for AiFoundation
 * and maps the structured AI answer back to the Back Office Assistant.
 * No real AI call is made: the AiFoundation facade is mocked.
 *
 * Run: vendor/bin/codecept run -c tests/SprykerAcademyTest/Zed/AiProductCreation/ Exercise21
 */
class ProductCreationAgentPluginTest extends Unit
{
    protected const string AI_CONFIGURATION_NAME = AiProductCreationConstants::AI_CONFIGURATION_PRODUCT_CREATION_OPENAI;

    protected const string USER_PROMPT = 'Create a red t-shirt for 19.99 EUR';

    protected const string CONVERSATION_REFERENCE = 'conversation-123';

    protected ?PromptRequestTransfer $capturedPromptRequest = null;

    public function testAgentSendsPromptRequestWithConfigurationToolSetAndStructuredMessage(): void
    {
        $this->executeAgent();

        $promptRequest = $this->capturedPromptRequest;
        $this->assertNotNull($promptRequest, 'executeAgent() must call AiFoundationFacade::prompt() with a PromptRequestTransfer.');

        $this->assertSame(
            static::AI_CONFIGURATION_NAME,
            $promptRequest->getAiConfigurationName(),
            'Set the AI configuration name from AiProductCreationConfig::getProductCreationAgentAiConfigurationName().',
        );
        $this->assertSame(
            static::CONVERSATION_REFERENCE,
            $promptRequest->getConversationReference(),
            'Pass on the conversation reference so the agent remembers earlier messages.',
        );
        $this->assertContains(
            AiProductCreationConstants::TOOL_SET_PRODUCT_CREATION,
            $promptRequest->getToolSetNames(),
            'Add the product creation tool set name so the AI model can call the tools.',
        );
        $this->assertInstanceOf(
            ProductCreationAgentResponseTransfer::class,
            $promptRequest->getStructuredMessage(),
            'Set a ProductCreationAgentResponseTransfer as structured message so the AI answers in that shape.',
        );
    }

    public function testAgentSendsTheUserPromptAsUserMessage(): void
    {
        $this->executeAgent();

        $promptMessage = $this->capturedPromptRequest?->getPromptMessage();
        $this->assertNotNull($promptMessage, 'The prompt request needs a PromptMessageTransfer.');
        $this->assertSame(static::USER_PROMPT, $promptMessage->getContent(), 'The prompt message content must be the user prompt.');
        $this->assertSame(AiFoundationConstants::MESSAGE_TYPE_USER, $promptMessage->getType(), 'The prompt message type must be MESSAGE_TYPE_USER.');
    }

    public function testAgentMapsStructuredAnswerToAssistantResponse(): void
    {
        $response = $this->executeAgent();

        $this->assertSame('Product created.', $response->getMessage(), 'Map the structured message "message" to the assistant response.');
        $this->assertSame('Product Creation', $response->getAgent(), 'Map the structured message "agent" to the assistant response.');
    }

    protected function executeAgent()
    {
        $facadeMock = $this->createMock(AiFoundationFacadeInterface::class);
        $facadeMock->method('prompt')->willReturnCallback(function (PromptRequestTransfer $promptRequest): PromptResponseTransfer {
            $this->capturedPromptRequest = $promptRequest;

            return (new PromptResponseTransfer())
                ->setIsSuccessful(true)
                ->setStructuredMessage(
                    (new ProductCreationAgentResponseTransfer())
                        ->setMessage('Product created.')
                        ->setAgent('Product Creation'),
                );
        });

        $factoryMock = $this->createMock(AiProductCreationCommunicationFactory::class);
        $factoryMock->method('getAiFoundationFacade')->willReturn($facadeMock);

        $configMock = $this->createMock(AiProductCreationConfig::class);
        $configMock->method('getProductCreationAgentAiConfigurationName')->willReturn(static::AI_CONFIGURATION_NAME);
        $configMock->method('isProductCreationAgentEnabled')->willReturn(true);

        $agentPlugin = new ProductCreationAgentPlugin();
        $agentPlugin->setFactory($factoryMock);
        $agentPlugin->setConfig($configMock);

        return $agentPlugin->executeAgent(
            (new BackofficeAssistantPromptRequestTransfer())
                ->setPrompt(static::USER_PROMPT)
                ->setConversationReference(static::CONVERSATION_REFERENCE),
        );
    }
}
