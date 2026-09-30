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
        // TODO: Add the AI configuration of this exercise on top of the project's ones:
        //       CatalogAssistantConstants::AI_CONFIGURATION_CATALOG_ASSISTANT => [ provider OpenAI, the API token of the AI Commerce settings, model gpt-4.1-mini and a guardrail system prompt (see the guide) ]
        //       See the guide for the array. Wrap it in $this->resolveConfigurationReferences([...]) so the
        //       "configuration::" references are replaced by the values of the Back Office settings.
        // Hint: return array_merge(parent::getAiConfigurations(), $this->resolveConfigurationReferences([...]));

        return parent::getAiConfigurations();
    }
}
