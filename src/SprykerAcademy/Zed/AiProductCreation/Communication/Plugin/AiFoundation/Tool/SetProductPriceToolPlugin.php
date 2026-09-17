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
class SetProductPriceToolPlugin extends AbstractPlugin implements ToolPluginInterface
{
    use LoggerTrait;

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getName(): string
    {
        return 'set_product_price';
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getDescription(): string
    {
        return 'Set a default price for a concrete or abstract product identified by SKU. Amounts are in minor units (cents). Call this after create_product so the product can be sold.';
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
                description: 'Concrete or abstract product SKU to price.',
                isRequired: true,
            ),
            new ToolParameter(
                name: 'grossAmount',
                type: 'integer',
                description: 'Gross price in minor units (cents), e.g. 1999 for 19.99.',
                isRequired: true,
            ),
            new ToolParameter(
                name: 'netAmount',
                type: 'integer',
                description: 'Net price in minor units (cents). Defaults to grossAmount when omitted.',
                isRequired: false,
            ),
            new ToolParameter(
                name: 'currency',
                type: 'string',
                description: 'Currency ISO code, e.g. "EUR". Defaults to the store default currency.',
                isRequired: false,
            ),
            new ToolParameter(
                name: 'store',
                type: 'string',
                description: 'Store name, e.g. "DE". Defaults to the current store.',
                isRequired: false,
            ),
            new ToolParameter(
                name: 'priceType',
                type: 'string',
                description: 'Price type. Defaults to "DEFAULT".',
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
            return $this->getBusinessFactory()->createProductCreationWriter()->setPrice($arguments);
        } catch (Throwable $throwable) {
            $this->getLogger()->error(sprintf('set_product_price failed: %s', $throwable->getMessage()), ['exception' => $throwable]);

            return json_encode(['error' => 'Failed to set price.']);
        }
    }
}
