<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\Agent;

use Exception;
use Generated\Shared\Transfer\BackofficeAssistantPromptRequestTransfer;
use Generated\Shared\Transfer\BackofficeAssistantPromptResponseTransfer;
use Generated\Shared\Transfer\ProductCreationAgentResponseTransfer;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Spryker\Shared\AiFoundation\AiFoundationConstants;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerAcademy\Shared\AiProductCreation\AiProductCreationConstants;
use SprykerFeature\Zed\AiCommerce\Dependency\BackofficeAssistant\BackofficeAssistantAgentPluginInterface;

/**
 * @method \SprykerAcademy\Zed\AiProductCreation\Communication\AiProductCreationCommunicationFactory getFactory()
 * @method \SprykerAcademy\Zed\AiProductCreation\AiProductCreationConfig getConfig()
 */
class ProductCreationAgentPlugin extends AbstractPlugin implements BackofficeAssistantAgentPluginInterface
{
    use LoggerTrait;

    protected const string NAME = 'Product Creation';

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getDescription(): string
    {
        return 'Creates products in the catalog from a natural-language description. Use when the user wants to add, create, or set up a new product — including the abstract product and its concrete variants, price, stock, category assignment, and tax set.';
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function isApplicable(
        BackofficeAssistantPromptRequestTransfer $backofficeAssistantPromptRequest,
    ): bool {
        return $this->getConfig()->isProductCreationAgentEnabled();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function executeAgent(
        BackofficeAssistantPromptRequestTransfer $backofficeAssistantPromptRequest,
    ): BackofficeAssistantPromptResponseTransfer {
        // TODO-8: Build the PromptRequestTransfer that is sent to AiFoundation.
        // It needs five things:
        //   1. setAiConfigurationName(): which provider, model and system prompt to use.
        //      Hint: $this->getConfig()->getProductCreationAgentAiConfigurationName()
        //   2. setConversationReference(): lets the AI remember earlier messages of this chat.
        //      Hint: take it from $backofficeAssistantPromptRequest
        //   3. setStructuredMessage(): the transfer the AI must answer with.
        //      Hint: new ProductCreationAgentResponseTransfer(), defined in ai_product_creation.transfer.xml
        //   4. addToolSetName(): the tools the AI may call.
        //      Hint: AiProductCreationConstants::TOOL_SET_PRODUCT_CREATION
        //   5. setPromptMessage(): a PromptMessageTransfer with
        //      type AiFoundationConstants::MESSAGE_TYPE_USER, the user's prompt as content, and the request's attachments.
        $promptRequest = new PromptRequestTransfer();

        $backofficeAssistantPromptResponse = new BackofficeAssistantPromptResponseTransfer();

        try {
            $promptResponse = $this->getFactory()->getAiFoundationFacade()->prompt($promptRequest);
        } catch (Exception $e) {
            $this->getLogger()->error(sprintf('ProductCreationAgent prompt failed: %s', $e->getMessage()), ['exception' => $e]);

            return $backofficeAssistantPromptResponse;
        }

        if (!$promptResponse->getIsSuccessful()) {
            $this->getLogger()->error(sprintf(
                'ProductCreationAgent prompt response is not successful: %s',
                implode(', ', array_map(static fn ($error) => $error->getMessage(), $promptResponse->getErrors()->getArrayCopy())),
            ));

            return $backofficeAssistantPromptResponse;
        }

        /** @var \Generated\Shared\Transfer\ProductCreationAgentResponseTransfer $productCreationAgentResponse */
        $productCreationAgentResponse = $promptResponse->getStructuredMessage();

        // TODO-9: Map the structured AI answer to the Back Office Assistant response.
        // Hint-1: Copy three values from $productCreationAgentResponse: agent, message, and reasoningMessage.
        // Hint-2: The chat widget shows "message". The intent router uses "agent" to detect a Guardrail answer.

        return $backofficeAssistantPromptResponse;
    }
}
