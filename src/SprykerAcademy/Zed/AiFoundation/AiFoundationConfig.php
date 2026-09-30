<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace SprykerAcademy\Zed\AiFoundation;

use Pyz\Shared\AiCommerce\AiCommerceConstants;
use Spryker\Shared\AiFoundation\AiFoundationConstants;
use SprykerAcademy\Shared\CatalogAssistant\CatalogAssistantConstants;

// the project's AiFoundation config when it has one, the core one otherwise
if (!class_exists(ProjectAiFoundationConfig::class, false)) {
    class_alias(
        class_exists(\Pyz\Zed\AiFoundation\AiFoundationConfig::class) ? \Pyz\Zed\AiFoundation\AiFoundationConfig::class : \Spryker\Zed\AiFoundation\AiFoundationConfig::class,
        ProjectAiFoundationConfig::class,
    );
}

/**
 * The AI configuration of this exercise.
 *
 * Projects usually register AI configurations in config/Shared/config_ai.php
 * (AiFoundationConstants::AI_CONFIGURATIONS). The exercise keeps it in its own code instead: this config
 * class extends the project's one and, as SprykerAcademy is resolved before Pyz, is the one Spryker uses.
 */
class AiFoundationConfig extends ProjectAiFoundationConfig
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function getAiConfigurations(): array
    {
        return array_merge(parent::getAiConfigurations(), $this->resolveConfigurationReferences([
            CatalogAssistantConstants::AI_CONFIGURATION_CATALOG_ASSISTANT => [
                'provider_name' => AiFoundationConstants::PROVIDER_OPENAI,
                'provider_config' => [
                    'key' => AiFoundationConstants::CONFIGURATION_REFERENCE_PREFIX . AiCommerceConstants::CONFIGURATION_KEY_OPENAI_API_TOKEN,
                    'model' => 'gpt-4.1-mini',
                ],
                'system_prompt' => 'You are a product advisor for an online shop. You answer questions about one product at a time. ALWAYS call get_product_details with the given SKU before answering, even for follow-up questions. Answer only with facts from the tool result. If the tool returns an error or the details do not cover the question, say so and set confidence to low. Prices in the tool result are gross amounts in cents; show them in major units. Keep the answer under 80 words.',
            ],
        ]));
    }
}
