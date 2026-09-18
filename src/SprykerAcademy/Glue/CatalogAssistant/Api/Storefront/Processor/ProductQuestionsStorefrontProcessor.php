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
            // TODO-8: Give the LLM its tools and its answer format:
            //   addToolSetName(CatalogAssistantConstants::TOOL_SET_CATALOG)   -> the model may call get_product_details
            //   setStructuredMessage(new ProductAnswerTransfer())             -> the model must answer in that shape
            //   setMaxRetries(2)                                               -> AiFoundation retries when the answer does not fit the shape
            ->setPromptMessage(
                (new PromptMessageTransfer())
                    ->setType(AiFoundationConstants::MESSAGE_TYPE_USER)
                    ->setContent(sprintf('Product SKU: %s. Question: %s', trim($resource->sku), trim($resource->question))),
            );

        $promptResponseTransfer = $this->aiFoundationClient->prompt($promptRequestTransfer);
        $productAnswerTransfer = $promptResponseTransfer->getStructuredMessage();

        if (!$promptResponseTransfer->getIsSuccessful() || !$productAnswerTransfer instanceof ProductAnswerTransfer) {
            // The AiFoundation errors explain what went wrong, for example which property of the structured answer was missing.
            $reasons = array_map(static fn ($errorTransfer): string => (string)$errorTransfer->getMessage(), $promptResponseTransfer->getErrors()->getArrayCopy());

            throw new ServiceUnavailableHttpException(null, 'The AI provider did not return a structured answer. ' . implode(' | ', $reasons));
        }

        $resource->conversationReference = $conversationReference;
        // TODO-9: Copy the typed answer into the resource: answer, isInStock, confidence, and relatedSkus.
        // Hint: $productAnswerTransfer is a ProductAnswerTransfer with a getter per property. No JSON parsing needed.

        return $resource;
    }
}
