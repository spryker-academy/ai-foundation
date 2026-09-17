<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Communication;

use Spryker\Zed\AiFoundation\Business\AiFoundationFacadeInterface;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerAcademy\Zed\AiProductCreation\AiProductCreationDependencyProvider;
use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool\ApproveProductToolPlugin;
use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool\AssignProductToCategoryToolPlugin;
use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool\CreateProductToolPlugin;
use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool\GenerateProductImageToolPlugin;
use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool\ListCategoriesToolPlugin;
use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool\ListTaxSetsToolPlugin;
use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool\SetProductImageToolPlugin;
use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool\SetProductPriceToolPlugin;
use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool\SetProductStockToolPlugin;

/**
 * @method \SprykerAcademy\Zed\AiProductCreation\AiProductCreationConfig getConfig()
 * @method \SprykerAcademy\Zed\AiProductCreation\Business\AiProductCreationBusinessFactory getBusinessFactory()
 */
class AiProductCreationCommunicationFactory extends AbstractCommunicationFactory
{
    public function getAiFoundationFacade(): AiFoundationFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_AI_FOUNDATION);
    }

    public function createListCategoriesToolPlugin(): ToolPluginInterface
    {
        return new ListCategoriesToolPlugin();
    }

    public function createListTaxSetsToolPlugin(): ToolPluginInterface
    {
        return new ListTaxSetsToolPlugin();
    }

    public function createCreateProductToolPlugin(): ToolPluginInterface
    {
        return new CreateProductToolPlugin();
    }

    public function createSetProductPriceToolPlugin(): ToolPluginInterface
    {
        return new SetProductPriceToolPlugin();
    }

    public function createSetProductStockToolPlugin(): ToolPluginInterface
    {
        return new SetProductStockToolPlugin();
    }

    public function createAssignProductToCategoryToolPlugin(): ToolPluginInterface
    {
        return new AssignProductToCategoryToolPlugin();
    }

    public function createApproveProductToolPlugin(): ToolPluginInterface
    {
        return new ApproveProductToolPlugin();
    }

    public function createSetProductImageToolPlugin(): ToolPluginInterface
    {
        return new SetProductImageToolPlugin();
    }

    public function createGenerateProductImageToolPlugin(): ToolPluginInterface
    {
        return new GenerateProductImageToolPlugin();
    }
}
