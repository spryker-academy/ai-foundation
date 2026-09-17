<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation;

use Spryker\Zed\AiFoundation\Dependency\Tools\ToolSetPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerAcademy\Shared\AiProductCreation\AiProductCreationConstants;

/**
 * @method \SprykerAcademy\Zed\AiProductCreation\Communication\AiProductCreationCommunicationFactory getFactory()
 * @method \SprykerAcademy\Zed\AiProductCreation\AiProductCreationConfig getConfig()
 */
class ProductCreationToolSetPlugin extends AbstractPlugin implements ToolSetPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getName(): string
    {
        return AiProductCreationConstants::TOOL_SET_PRODUCT_CREATION;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return array<\Spryker\Zed\AiFoundation\Dependency\Tools\ToolPluginInterface>
     */
    public function getTools(): array
    {
        $factory = $this->getFactory();

        return [
            $factory->createListCategoriesToolPlugin(),
            $factory->createListTaxSetsToolPlugin(),
            $factory->createCreateProductToolPlugin(),
            $factory->createSetProductPriceToolPlugin(),
            $factory->createSetProductStockToolPlugin(),
            $factory->createAssignProductToCategoryToolPlugin(),
            $factory->createApproveProductToolPlugin(),
            $factory->createSetProductImageToolPlugin(),
            $factory->createGenerateProductImageToolPlugin(),
        ];
    }
}
