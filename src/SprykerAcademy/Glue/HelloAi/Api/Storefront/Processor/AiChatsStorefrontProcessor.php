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

    // TODO-5: Add a constructor that receives the AiFoundation CLIENT and stores it in a protected property.
    // Hint-1: The type is \Spryker\Client\AiFoundation\AiFoundationClientInterface. Symfony autowires it for you.
    // Hint-2: Glue has no access to Zed facades. Never use the NeuronAI library or the AiFoundation facade here.

    protected function processPost(mixed $data): mixed
    {
        return $this->chat($data);
    }

    protected function chat(AiChatsStorefrontResource $resource): AiChatsStorefrontResource
    {
        if ($resource->message === null || trim($resource->message) === '') {
            throw new BadRequestHttpException('The attribute "message" must not be empty.');
        }

        // TODO-6: Decide the conversation reference.
        // Reuse $resource->conversationReference when the client sent one. Otherwise generate a new unique one,
        // for example uniqid(static::CONVERSATION_REFERENCE_PREFIX). The reference is the memory of the LLM:
        // AiFoundation stores every message under it and replays the history on the next prompt.

        // TODO-7: Build the PromptRequestTransfer and send it with $this->aiFoundationClient->prompt().
        // It needs three things:
        //   1. setAiConfigurationName(HelloAiConstants::AI_CONFIGURATION_HELLO_AI)  -> provider, model, and system prompt from config_ai.php
        //   2. setConversationReference($conversationReference)
        //   3. setPromptMessage(): a PromptMessageTransfer with type AiFoundationConstants::MESSAGE_TYPE_USER and $resource->message as content

        // TODO-8: Handle the answer.
        // If the response is not successful or has no message, throw new ServiceUnavailableHttpException(null, 'The AI provider did not return an answer.').
        // Otherwise write the conversation reference and the message content of the response into $resource->conversationReference and $resource->answer.

        return $resource;
    }
}
