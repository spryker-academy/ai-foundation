<?php

declare(strict_types=1);

namespace SprykerAcademy\Shared\HelloAi;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface HelloAiConstants
{
    /**
     * Specification:
     * - Name of the AI configuration the Hello AI storefront API uses.
     * - The configuration itself is registered in config/Shared/config_ai.php.
     *
     * @api
     */
    public const string AI_CONFIGURATION_HELLO_AI = 'SPRYKER_ACADEMY:AI_CONFIGURATION_HELLO_AI';
}
