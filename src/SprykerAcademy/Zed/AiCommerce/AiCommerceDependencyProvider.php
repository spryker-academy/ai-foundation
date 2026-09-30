<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace SprykerAcademy\Zed\AiCommerce;

use SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\Agent\ProductCreationAgentPlugin;

// the provider the Back Office Assistant setup created, the feature's one otherwise
if (!class_exists(ProjectAiCommerceDependencyProvider::class, false)) {
    class_alias(
        class_exists(\Pyz\Zed\AiCommerce\AiCommerceDependencyProvider::class) ? \Pyz\Zed\AiCommerce\AiCommerceDependencyProvider::class : \SprykerFeature\Zed\AiCommerce\AiCommerceDependencyProvider::class,
        ProjectAiCommerceDependencyProvider::class,
    );
}

/**
 * Registers the product creation agent with the Back Office Assistant.
 */
class AiCommerceDependencyProvider extends ProjectAiCommerceDependencyProvider
{
    /**
     * @return array<\SprykerFeature\Zed\AiCommerce\Dependency\Plugin\BackofficeAssistantAgentPluginInterface>
     */
    protected function getBackofficeAssistantAgentPlugins(): array
    {
        return array_merge(parent::getBackofficeAssistantAgentPlugins(), [
            new ProductCreationAgentPlugin(),
        ]);
    }
}
