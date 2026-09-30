<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace SprykerAcademy\Zed\AiCommerce;

use SprykerAcademy\Shared\AiProductCreation\AiProductCreationConstants;

// the project's AiCommerce config, the feature's one otherwise
if (!class_exists(ProjectAiCommerceConfig::class, false)) {
    class_alias(
        class_exists(\Pyz\Zed\AiCommerce\AiCommerceConfig::class) ? \Pyz\Zed\AiCommerce\AiCommerceConfig::class : \SprykerFeature\Zed\AiCommerce\AiCommerceConfig::class,
        ProjectAiCommerceConfig::class,
    );
}

/**
 * Streams the agent's tool call progress in the Back Office Assistant.
 */
class AiCommerceConfig extends ProjectAiCommerceConfig
{
    /**
     * @return array<string>
     */
    public function getBackofficeAssistantSseAiConfigurationNames(): array
    {
        return array_values(array_filter([
            ...parent::getBackofficeAssistantSseAiConfigurationNames(),
            AiProductCreationConstants::AI_CONFIGURATION_PRODUCT_CREATION_OPENAI,
        ]));
    }
}
