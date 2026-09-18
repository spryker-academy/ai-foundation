<?php

declare(strict_types=1);

namespace SprykerAcademy\Zed\CatalogAssistant\Communication\Plugin\AiFoundation\Tool;

use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolParameter;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Throwable;

/**
 * The LLM sees only getName(), getDescription(), and getParameters(). AiFoundation calls execute() when the model asks for it.
 *
 * @method \SprykerAcademy\Zed\CatalogAssistant\Business\CatalogAssistantBusinessFactory getBusinessFactory()
 * @method \SprykerAcademy\Zed\CatalogAssistant\Communication\CatalogAssistantCommunicationFactory getFactory()
 */
class GetProductDetailsToolPlugin extends AbstractPlugin implements ToolPluginInterface
{
    use LoggerTrait;

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getName(): string
    {
        // TODO-1: Return the tool name 'get_product_details'.
        // Hint: snake_case, like a function name. The system prompt in config_ai.php refers to this exact name.
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getDescription(): string
    {
        // TODO-2: Describe the tool for the LLM in two or three sentences:
        //   what it returns (name, description, attributes, every variant with availability and gross price in cents),
        //   what it needs (a SKU), and when to call it (before answering ANY question about a product).
        // Hint: The model never sees your PHP code. This text is the whole contract.
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return array<\Spryker\Zed\AiFoundation\Dependency\Tools\ToolParameterInterface>
     */
    public function getParameters(): array
    {
        // TODO-3: Declare one required parameter named 'sku' of type 'string' with a description.
        // Hint-1: new ToolParameter(name: '...', type: '...', description: '...', isRequired: true)
        // Hint-2: The name is the key the business layer reads from $arguments in execute().
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function execute(...$arguments): mixed
    {
        try {
            /** @var array<string, mixed> $arguments */
            // TODO-4: Delegate to the business layer and return its JSON string.
            // Hint: $this->getBusinessFactory()->createProductDetailsReader()->getProductDetailsJson(...) with the 'sku' argument.
        } catch (Throwable $throwable) {
            $this->getLogger()->error(sprintf('get_product_details failed: %s', $throwable->getMessage()), ['exception' => $throwable]);

            return json_encode(['error' => 'Failed to read the product details.']);
        }
    }
}
