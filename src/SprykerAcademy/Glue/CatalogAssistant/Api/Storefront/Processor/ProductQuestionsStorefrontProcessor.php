<?php

declare(strict_types=1);

namespace SprykerAcademy\Glue\CatalogAssistant\Api\Storefront\Processor;

use Generated\Api\Storefront\ProductQuestionsStorefrontResource;
use Generated\Shared\Transfer\ProductAnswerTransfer;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Spryker\ApiPlatform\State\Processor\AbstractStorefrontProcessor;
use Spryker\Client\AiFoundation\AiFoundationClientInterface;
use Spryker\Shared\AiFoundation\AiFoundationConstants;
use SprykerAcademy\Shared\CatalogAssistant\CatalogAssistantConstants;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Handles POST /product-questions.
 *
 * Compared to the Hello AI processor, the prompt request carries two more things:
 * a tool set name, so the LLM can read the product through a tool in Zed, and a structured message,
 * so the LLM answers in the shape of ProductAnswerTransfer instead of free text.
 * AiFoundation validates the structured answer and retries the prompt up to maxRetries times when it does not fit.
 */
class ProductQuestionsStorefrontProcessor extends AbstractStorefrontProcessor
{
    protected const string CONVERSATION_REFERENCE_PREFIX = 'catalog-';

    public function __construct(
        protected AiFoundationClientInterface $aiFoundationClient,
    ) {
    }

    protected function processPost(mixed $data): mixed
    {
        return $this->answer($data);
    }

    protected function answer(ProductQuestionsStorefrontResource $resource): ProductQuestionsStorefrontResource
    {
        if ($resource->sku === null || trim($resource->sku) === '' || $resource->question === null || trim($resource->question) === '') {
            throw new BadRequestHttpException('The attributes "sku" and "question" must not be empty.');
        }

        $conversationReference = $resource->conversationReference ?: uniqid(static::CONVERSATION_REFERENCE_PREFIX);

        $promptRequestTransfer = (new PromptRequestTransfer())
            ->setAiConfigurationName(CatalogAssistantConstants::AI_CONFIGURATION_CATALOG_ASSISTANT)
            ->setConversationReference($conversationReference)
            ->addToolSetName(CatalogAssistantConstants::TOOL_SET_CATALOG)
            ->setStructuredMessage(new ProductAnswerTransfer())
            ->setMaxRetries(2)
            ->setPromptMessage(
                (new PromptMessageTransfer())
                    ->setType(AiFoundationConstants::MESSAGE_TYPE_USER)
                    ->setContent(sprintf('Product SKU: %s. Question: %s', trim($resource->sku), trim($resource->question))),
            );

        $promptResponseTransfer = $this->aiFoundationClient->prompt($promptRequestTransfer);
        $productAnswerTransfer = $promptResponseTransfer->getStructuredMessage();

        if (!$promptResponseTransfer->getIsSuccessful() || !$productAnswerTransfer instanceof ProductAnswerTransfer) {
            throw new ServiceUnavailableHttpException(null, 'The AI provider did not return a structured answer.');
        }

        $resource->conversationReference = $conversationReference;
        $resource->answer = $productAnswerTransfer->getAnswer();
        $resource->isInStock = $productAnswerTransfer->getIsInStock();
        $resource->confidence = $productAnswerTransfer->getConfidence();
        $resource->relatedSkus = $productAnswerTransfer->getRelatedSkus();

        return $resource;
    }
}
