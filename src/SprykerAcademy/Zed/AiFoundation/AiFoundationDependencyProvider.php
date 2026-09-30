<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace SprykerAcademy\Zed\AiFoundation;

use SprykerAcademy\Zed\CatalogAssistant\Communication\Plugin\AiFoundation\CatalogToolSetPlugin;

// the provider the Back Office Assistant setup created when there is one, the core one otherwise
if (!class_exists(ProjectAiFoundationDependencyProvider::class, false)) {
    class_alias(
        class_exists(\Pyz\Zed\AiFoundation\AiFoundationDependencyProvider::class) ? \Pyz\Zed\AiFoundation\AiFoundationDependencyProvider::class : \Spryker\Zed\AiFoundation\AiFoundationDependencyProvider::class,
        ProjectAiFoundationDependencyProvider::class,
    );
}

/**
 * Registers the tool set of this exercise. Extends the project's provider (as SprykerAcademy is resolved
 * before Pyz, this is the one Spryker uses), so the project's own tool sets stay registered.
 */
class AiFoundationDependencyProvider extends ProjectAiFoundationDependencyProvider
{
    /**
     * @return array<\Spryker\Zed\AiFoundation\Dependency\Tools\ToolSetPluginInterface>
     */
    protected function getAiToolSetPlugins(): array
    {
        // TODO: Register the tool set of this exercise on top of the project's tool sets.
        // Hint: return array_merge(parent::getAiToolSetPlugins(), [new CatalogToolSetPlugin()]);

        return parent::getAiToolSetPlugins();
    }
}
