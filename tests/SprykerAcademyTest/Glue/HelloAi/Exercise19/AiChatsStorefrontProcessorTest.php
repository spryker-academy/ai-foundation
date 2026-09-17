<?php

declare(strict_types=1);

namespace SprykerAcademyTest\Glue\HelloAi\Exercise19;

use ApiPlatform\Metadata\Post;
use Codeception\Test\Unit;
use Generated\Api\Storefront\AiChatsStorefrontResource;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use ReflectionClass;
use Spryker\Client\AiFoundation\AiFoundationClientInterface;
use Spryker\Shared\AiFoundation\AiFoundationConstants;
use SprykerAcademy\Glue\HelloAi\Api\Storefront\Processor\AiChatsStorefrontProcessor;
use SprykerAcademy\Shared\HelloAi\HelloAiConstants;

/**
 * Exercise 19, Task 2: The processor
 *
 * Verifies that the processor talks to the LLM through the AiFoundation client and keeps the conversation.
 * The client is mocked, so no AI call is made and no token is spent.
 *
 * Run: vendor/bin/codecept run -c tests/SprykerAcademyTest/Glue/HelloAi/ Exercise19
 */
class AiChatsStorefrontProcessorTest extends Unit
{
    protected const string USER_MESSAGE = 'Hello world! My name is Hidran.';

    protected const string LLM_ANSWER = 'Hello Hidran!';

    protected ?PromptRequestTransfer $capturedPromptRequest = null;

    public function testProcessorDependsOnTheAiFoundationClientOnly(): void
    {
        $constructor = (new ReflectionClass(AiChatsStorefrontProcessor::class))->getConstructor();
        $this->assertNotNull($constructor, 'The processor needs a constructor that receives the AiFoundation client.');

        $types = array_map(static fn ($parameter): string => (string)$parameter->getType(), $constructor->getParameters());

        $this->assertContains(
            AiFoundationClientInterface::class,
            $types,
            'Inject Spryker\Client\AiFoundation\AiFoundationClientInterface. The Glue application talks to Zed through clients.',
        );
    }

    public function testProcessorDoesNotUseNeuronAiDirectly(): void
    {
        $source = (string)file_get_contents((new ReflectionClass(AiChatsStorefrontProcessor::class))->getFileName());

        $this->assertStringNotContainsStringIgnoringCase(
            'NeuronAI',
            $source,
            'Do not use the NeuronAI library directly. AiFoundation wraps the provider, so the provider can be switched by configuration.',
        );
    }

    public function testProcessorSendsTheMessageWithTheHelloAiConfiguration(): void
    {
        $this->process(static::USER_MESSAGE, null);

        $promptRequest = $this->capturedPromptRequest;
        $this->assertNotNull($promptRequest, 'processPost() must call AiFoundationClientInterface::prompt() with a PromptRequestTransfer.');
        $this->assertSame(
            HelloAiConstants::AI_CONFIGURATION_HELLO_AI,
            $promptRequest->getAiConfigurationName(),
            'Set the AI configuration name to HelloAiConstants::AI_CONFIGURATION_HELLO_AI.',
        );

        $promptMessage = $promptRequest->getPromptMessage();
        $this->assertNotNull($promptMessage, 'Set a PromptMessageTransfer on the request.');
        $this->assertSame(static::USER_MESSAGE, $promptMessage->getContent(), 'The prompt message content must be the message from the request.');
        $this->assertSame(AiFoundationConstants::MESSAGE_TYPE_USER, $promptMessage->getType(), 'The prompt message type must be AiFoundationConstants::MESSAGE_TYPE_USER.');
    }

    public function testProcessorStartsANewConversationWhenNoReferenceIsGiven(): void
    {
        $resource = $this->process(static::USER_MESSAGE, null);

        $reference = $this->capturedPromptRequest?->getConversationReference();
        $this->assertNotEmpty($reference, 'Generate a conversation reference when the client sends none. Without it the LLM has no memory.');
        $this->assertSame($reference, $resource->conversationReference, 'Return the generated conversation reference so the client can continue the conversation.');
    }

    public function testProcessorContinuesAnExistingConversation(): void
    {
        $resource = $this->process('What is my name?', 'hello-ai-existing');

        $this->assertSame('hello-ai-existing', $this->capturedPromptRequest?->getConversationReference(), 'Pass the conversation reference from the request on to AiFoundation.');
        $this->assertSame('hello-ai-existing', $resource->conversationReference, 'Return the same conversation reference.');
    }

    public function testProcessorReturnsTheAnswerOfTheLlm(): void
    {
        $resource = $this->process(static::USER_MESSAGE, null);

        $this->assertSame(static::LLM_ANSWER, $resource->answer, 'Copy the content of the response message into the "answer" property.');
    }

    protected function process(string $message, ?string $conversationReference): AiChatsStorefrontResource
    {
        $clientMock = $this->createMock(AiFoundationClientInterface::class);
        $clientMock->method('prompt')->willReturnCallback(function (PromptRequestTransfer $promptRequest): PromptResponseTransfer {
            $this->capturedPromptRequest = $promptRequest;

            return (new PromptResponseTransfer())
                ->setIsSuccessful(true)
                ->setMessage((new PromptMessageTransfer())->setType(AiFoundationConstants::MESSAGE_TYPE_ASSISTANT)->setContent(static::LLM_ANSWER));
        });

        $resource = new AiChatsStorefrontResource();
        $resource->message = $message;
        $resource->conversationReference = $conversationReference;

        $result = (new AiChatsStorefrontProcessor($clientMock))->process($resource, new Post());
        $this->assertInstanceOf(AiChatsStorefrontResource::class, $result, 'processPost() must return the resource.');

        return $result;
    }
}
