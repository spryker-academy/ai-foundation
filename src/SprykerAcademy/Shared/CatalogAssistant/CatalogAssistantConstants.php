<?php

declare(strict_types=1);

namespace SprykerAcademy\Shared\CatalogAssistant;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface CatalogAssistantConstants
{
    /**
     * Specification:
     * - Name of the AI configuration the Ask the Catalog storefront API uses.
     * - The configuration itself is registered in config/Shared/config_ai.php.
     *
     * @api
     */
    public const string AI_CONFIGURATION_CATALOG_ASSISTANT = 'SPRYKER_ACADEMY:AI_CONFIGURATION_CATALOG_ASSISTANT';

    /**
     * Specification:
     * - Name of the tool set that gives the LLM access to the catalog.
     * - The storefront API requests it, the tool set plugin in Zed provides it.
     *
     * @api
     */
    public const string TOOL_SET_CATALOG = 'catalog_tools';
}
