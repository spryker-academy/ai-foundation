<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool;

use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolParameter;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Throwable;

/**
 * @method \SprykerAcademy\Zed\AiProductCreation\Business\AiProductCreationBusinessFactory getBusinessFactory()
 * @method \SprykerAcademy\Zed\AiProductCreation\AiProductCreationConfig getConfig()
 * @method \SprykerAcademy\Zed\AiProductCreation\Communication\AiProductCreationCommunicationFactory getFactory()
 */
class SetProductStockToolPlugin extends AbstractPlugin implements ToolPluginInterface
{
    use LoggerTrait;

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getName(): string
    {
        return 'set_product_stock';
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getDescription(): string
    {
        return 'Set the stock quantity for a concrete product identified by SKU, so it becomes available to buy. Call this after create_product.';
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
                description: 'Concrete product SKU.',
                isRequired: true,
            ),
            new ToolParameter(
                name: 'quantity',
                type: 'number',
                description: 'Stock quantity to set, e.g. 100.',
                isRequired: true,
            ),
            new ToolParameter(
                name: 'stockName',
                type: 'string',
                description: 'Warehouse/stock name. Defaults to "Warehouse1".',
                isRequired: false,
            ),
            new ToolParameter(
                name: 'isNeverOutOfStock',
                type: 'boolean',
                description: 'If true, the product is always available regardless of quantity. Defaults to false.',
                isRequired: false,
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
            return $this->getBusinessFactory()->createProductCreationWriter()->setStock($arguments);
        } catch (Throwable $throwable) {
            $this->getLogger()->error(sprintf('set_product_stock failed: %s', $throwable->getMessage()), ['exception' => $throwable]);

            return json_encode(['error' => 'Failed to set stock.']);
        }
    }
}
