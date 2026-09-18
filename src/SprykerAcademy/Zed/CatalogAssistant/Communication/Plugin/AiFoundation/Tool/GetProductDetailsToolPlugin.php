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
        return 'get_product_details';
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getDescription(): string
    {
        return 'Returns the details of one catalog product by SKU: name, description, attributes, and every variant with its attributes, availability, and gross price in cents. Call it before answering any question about a product. Do not answer product questions from memory.';
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
        return [
            new ToolParameter(
                name: 'sku',
                type: 'string',
                description: 'Abstract or concrete SKU of the product, exactly as given by the customer.',
                isRequired: true,
            ),
        ];
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
            return $this->getBusinessFactory()->createProductDetailsReader()->getProductDetailsJson((string)($arguments['sku'] ?? ''));
        } catch (Throwable $throwable) {
            $this->getLogger()->error(sprintf('get_product_details failed: %s', $throwable->getMessage()), ['exception' => $throwable]);

            return json_encode(['error' => 'Failed to read the product details.']);
        }
    }
}
