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
        // TODO-5: Return the tool set name.
        // Hint: CatalogAssistantConstants::TOOL_SET_CATALOG. The storefront processor asks for the tools by this name.
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
        // TODO-6: Return the tools of this set. There is one: $this->getFactory()->createGetProductDetailsToolPlugin()
    }
}
