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
class CreateProductToolPlugin extends AbstractPlugin implements ToolPluginInterface
{
    use LoggerTrait;

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getName(): string
    {
        return 'create_product';
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getDescription(): string
    {
        return 'Create an abstract product together with one or more concrete products (variants). Provide the abstract SKU, a name, and optionally a description, attributes, a tax set name, and a list of concrete variants. Returns the created abstract and concrete SKUs and IDs. Call set_product_price and set_product_stock afterwards to make the product sellable.';
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
                description: 'Unique SKU for the abstract product, e.g. "DEMO-SHIRT".',
                isRequired: true,
            ),
            new ToolParameter(
                name: 'name',
                type: 'string',
                description: 'Product name. Used for all store locales unless localizedAttributes is provided.',
                isRequired: true,
            ),
            new ToolParameter(
                name: 'description',
                type: 'string',
                description: 'Product description.',
                isRequired: false,
            ),
            new ToolParameter(
                name: 'attributes',
                type: 'object',
                description: 'Key-value attributes for the abstract product, e.g. {"brand": "Acme", "color": "red"}.',
                isRequired: false,
            ),
            new ToolParameter(
                name: 'taxSetName',
                type: 'string',
                description: 'Name of an existing tax set to assign to the abstract product. Use list_tax_sets to see valid names.',
                isRequired: false,
            ),
            new ToolParameter(
                name: 'concretes',
                type: 'array',
                description: 'Concrete variants. Each item is an object {sku, name, attributes}. Omit to create a single default concrete named after the abstract product.',
                isRequired: false,
            ),
            new ToolParameter(
                name: 'localizedAttributes',
                type: 'array',
                description: 'Per-locale names/descriptions. Each item is an object {locale, name, description}, e.g. locale "de_DE".',
                isRequired: false,
            ),
            new ToolParameter(
                name: 'isActive',
                type: 'boolean',
                description: 'Whether the product is active. Defaults to true.',
                isRequired: false,
            ),
            new ToolParameter(
                name: 'isSearchable',
                type: 'boolean',
                description: 'Whether the product is searchable in the storefront (appears in search results and catalog). Defaults to true.',
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
            return $this->getBusinessFactory()->createProductCreationWriter()->createProduct($arguments);
        } catch (Throwable $throwable) {
            $this->getLogger()->error(sprintf('create_product failed: %s', $throwable->getMessage()), ['exception' => $throwable]);

            return json_encode(['error' => 'Failed to create product.']);
        }
    }
}
