<?php

declare(strict_types=1);

namespace SprykerAcademyTest\Zed\CatalogAssistant\Exercise20;

use ApiPlatform\Metadata\Post;
use Codeception\Test\Unit;
use Generated\Api\Storefront\ProductQuestionsStorefrontResource;
use Generated\Shared\Transfer\ProductAnswerTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use Spryker\Client\AiFoundation\AiFoundationClientInterface;
use SprykerAcademy\Glue\CatalogAssistant\Api\Storefront\Processor\ProductQuestionsStorefrontProcessor;
use SprykerAcademy\Shared\CatalogAssistant\CatalogAssistantConstants;

/**
 * Exercise 20, Task 4: The processor
 *
 * Verifies that the processor asks for the catalog tools and a structured answer, and maps the typed answer.
 * The AiFoundation client is mocked, so no AI call is made.
 *
 * Run: vendor/bin/codecept run -c tests/SprykerAcademyTest/Zed/CatalogAssistant/ Exercise20
 */
class ProductQuestionsStorefrontProcessorTest extends Unit
{
    protected ?PromptRequestTransfer $capturedPromptRequest = null;

    public function testProcessorRequestsTheCatalogToolSet(): void
    {
        $this->process();

        $this->assertNotNull($this->capturedPromptRequest, 'processPost() must call AiFoundationClientInterface::prompt().');
        $this->assertContains(
            CatalogAssistantConstants::TOOL_SET_CATALOG,
            $this->capturedPromptRequest->getToolSetNames(),
            'Add CatalogAssistantConstants::TOOL_SET_CATALOG with addToolSetName(). Without it the LLM cannot call get_product_details.',
        );
    }

    public function testProcessorRequestsAStructuredAnswer(): void
    {
        $this->process();

        $this->assertInstanceOf(
            ProductAnswerTransfer::class,
            $this->capturedPromptRequest?->getStructuredMessage(),
            'Set a ProductAnswerTransfer as structured message. AiFoundation turns it into the JSON schema the model must follow.',
        );
    }

    public function testProcessorSendsSkuAndQuestionAndUsesTheCatalogConfiguration(): void
    {
        $this->process();

        $promptRequest = $this->capturedPromptRequest;
        $this->assertSame(CatalogAssistantConstants::AI_CONFIGURATION_CATALOG_ASSISTANT, $promptRequest?->getAiConfigurationName(), 'Use CatalogAssistantConstants::AI_CONFIGURATION_CATALOG_ASSISTANT as AI configuration name.');

        $content = (string)$promptRequest?->getPromptMessage()?->getContent();
        $this->assertStringContainsString('M1000785', $content, 'The prompt message must contain the SKU, otherwise the model cannot call the tool with it.');
        $this->assertStringContainsString('Is it in stock?', $content, 'The prompt message must contain the question.');
    }

    public function testProcessorMapsTheTypedAnswerToTheResource(): void
    {
        $resource = $this->process();

        $this->assertSame('Yes, 20 pieces are available.', $resource->answer, 'Map ProductAnswerTransfer::getAnswer() to "answer".');
        $this->assertTrue($resource->isInStock, 'Map ProductAnswerTransfer::getIsInStock() to "isInStock".');
        $this->assertSame('high', $resource->confidence, 'Map ProductAnswerTransfer::getConfidence() to "confidence".');
        $this->assertSame(['212427'], $resource->relatedSkus, 'Map ProductAnswerTransfer::getRelatedSkus() to "relatedSkus".');
        $this->assertNotEmpty($resource->conversationReference, 'Return the conversation reference so the client can ask a follow-up question.');
    }

    protected function process(): ProductQuestionsStorefrontResource
    {
        $clientMock = $this->createMock(AiFoundationClientInterface::class);
        $clientMock->method('prompt')->willReturnCallback(function (PromptRequestTransfer $promptRequest): PromptResponseTransfer {
            $this->capturedPromptRequest = $promptRequest;

            return (new PromptResponseTransfer())
                ->setIsSuccessful(true)
                ->setStructuredMessage(
                    (new ProductAnswerTransfer())
                        ->setAnswer('Yes, 20 pieces are available.')
                        ->setIsInStock(true)
                        ->setConfidence('high')
                        ->setRelatedSkus(['212427']),
                );
        });

        $resource = new ProductQuestionsStorefrontResource();
        $resource->sku = 'M1000785';
        $resource->question = 'Is it in stock?';

        $result = (new ProductQuestionsStorefrontProcessor($clientMock))->process($resource, new Post());
        $this->assertInstanceOf(ProductQuestionsStorefrontResource::class, $result, 'processPost() must return the resource.');

        return $result;
    }
}
