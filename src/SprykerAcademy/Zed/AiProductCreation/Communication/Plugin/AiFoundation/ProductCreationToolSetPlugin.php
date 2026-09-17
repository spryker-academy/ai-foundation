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
        // TODO-6: Return the tool set name.
        // Hint-1: The name of the constant to use is 'AiProductCreationConstants::TOOL_SET_PRODUCT_CREATION'
        // Hint-2: The agent asks AiFoundation for its tools by this name, so both sides must use the same constant.
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
        // TODO-7: Return all nine tool plugins of this module.
        // Hint-1: Plugins are created through the factory: $this->getFactory()->createListCategoriesToolPlugin()
        // Hint-2: Have a look at AiProductCreationCommunicationFactory. Every create*ToolPlugin() method belongs in this array.
        // Hint-3: A tool that is missing here does not exist for the AI model, even if its class is implemented.
    }
}
