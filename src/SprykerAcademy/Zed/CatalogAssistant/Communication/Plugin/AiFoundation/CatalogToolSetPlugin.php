<?php

declare(strict_types=1);

namespace SprykerAcademy\Zed\CatalogAssistant\Communication\Plugin\AiFoundation;

use Spryker\Zed\AiFoundation\Dependency\Tools\ToolSetPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerAcademy\Shared\CatalogAssistant\CatalogAssistantConstants;

/**
 * Groups the catalog tools under one name. A prompt request that carries this name may call every tool in here.
 *
 * @method \SprykerAcademy\Zed\CatalogAssistant\Communication\CatalogAssistantCommunicationFactory getFactory()
 */
class CatalogToolSetPlugin extends AbstractPlugin implements ToolSetPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getName(): string
    {
        return CatalogAssistantConstants::TOOL_SET_CATALOG;
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
        return [
            $this->getFactory()->createGetProductDetailsToolPlugin(),
        ];
    }
}
