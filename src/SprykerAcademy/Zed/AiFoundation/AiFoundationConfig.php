<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace SprykerAcademy\Zed\AiFoundation;

use Pyz\Shared\AiCommerce\AiCommerceConstants;
use Spryker\Shared\AiFoundation\AiFoundationConstants;
use SprykerAcademy\Shared\HelloAi\HelloAiConstants;

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
            HelloAiConstants::AI_CONFIGURATION_HELLO_AI => [
                'provider_name' => AiFoundationConstants::PROVIDER_OPENAI,
                'provider_config' => [
                    'key' => AiFoundationConstants::CONFIGURATION_REFERENCE_PREFIX . AiCommerceConstants::CONFIGURATION_KEY_OPENAI_API_TOKEN,
                    'model' => 'gpt-4.1-mini',
                ],
                'system_prompt' => 'You are a friendly assistant for a Spryker developer training. Answer in one or two short sentences.',
            ],
        ]));
    }
}
