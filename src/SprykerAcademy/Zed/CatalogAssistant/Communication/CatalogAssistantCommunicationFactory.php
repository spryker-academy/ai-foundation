<?php

declare(strict_types=1);

namespace SprykerAcademy\Zed\CatalogAssistant\Communication;

use Spryker\Zed\AiFoundation\Dependency\Tools\ToolPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerAcademy\Zed\CatalogAssistant\Communication\Plugin\AiFoundation\Tool\GetProductDetailsToolPlugin;

/**
 * @method \SprykerAcademy\Zed\CatalogAssistant\Business\CatalogAssistantBusinessFactory getBusinessFactory()
 */
class CatalogAssistantCommunicationFactory extends AbstractCommunicationFactory
{
    public function createGetProductDetailsToolPlugin(): ToolPluginInterface
    {
        return new GetProductDetailsToolPlugin();
    }
}
