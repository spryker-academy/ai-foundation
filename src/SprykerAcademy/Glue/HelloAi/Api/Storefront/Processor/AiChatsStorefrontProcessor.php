<?php

declare(strict_types=1);

namespace SprykerAcademy\Glue\HelloAi\Api\Storefront\Processor;

use Generated\Api\Storefront\AiChatsStorefrontResource;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Spryker\ApiPlatform\State\Processor\AbstractStorefrontProcessor;
use Spryker\Client\AiFoundation\AiFoundationClientInterface;
use Spryker\Shared\AiFoundation\AiFoundationConstants;
use SprykerAcademy\Shared\HelloAi\HelloAiConstants;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Handles POST /ai-chats.
 *
 * The Storefront API runs in the Glue application, which has no direct access to Zed business logic.
 * It talks to the LLM through the AiFoundation CLIENT. The client forwards the prompt to Zed,
 * where AiFoundation resolves the AI configuration, loads the conversation history, and calls the provider.
 */
class AiChatsStorefrontProcessor extends AbstractStorefrontProcessor
{
    protected const string CONVERSATION_REFERENCE_PREFIX = 'hello-ai-';

    public function __construct(
        protected AiFoundationClientInterface $aiFoundationClient,
    ) {
    }

    protected function processPost(mixed $data): mixed
    {
        return $this->chat($data);
    }

    protected function chat(AiChatsStorefrontResource $resource): AiChatsStorefrontResource
    {
        if ($resource->message === null || trim($resource->message) === '') {
            throw new BadRequestHttpException('The attribute "message" must not be empty.');
        }

        $conversationReference = $resource->conversationReference ?: uniqid(static::CONVERSATION_REFERENCE_PREFIX);

        $promptRequestTransfer = (new PromptRequestTransfer())
            ->setAiConfigurationName(HelloAiConstants::AI_CONFIGURATION_HELLO_AI)
            ->setConversationReference($conversationReference)
            ->setPromptMessage(
                (new PromptMessageTransfer())
                    ->setType(AiFoundationConstants::MESSAGE_TYPE_USER)
                    ->setContent($resource->message),
            );

        $promptResponseTransfer = $this->aiFoundationClient->prompt($promptRequestTransfer);

        if (!$promptResponseTransfer->getIsSuccessful() || $promptResponseTransfer->getMessage() === null) {
            throw new ServiceUnavailableHttpException(null, 'The AI provider did not return an answer.');
        }

        $resource->conversationReference = $conversationReference;
        $resource->answer = $promptResponseTransfer->getMessageOrFail()->getContent();

        return $resource;
    }
}
