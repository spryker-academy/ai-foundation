<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Shared\AiProductCreation;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface AiProductCreationConstants
{
    /**
     * Specification:
     * - AI configuration name used by the Product Creation agent backed by OpenAI.
     *
     * @api
     */
    public const string AI_CONFIGURATION_PRODUCT_CREATION_OPENAI = 'SPRYKER_ACADEMY:AI_CONFIGURATION_PRODUCT_CREATION_OPENAI';

    /**
     * Specification:
     * - AI configuration name used by the Product Creation agent backed by AWS Bedrock.
     *
     * @api
     */
    public const string AI_CONFIGURATION_PRODUCT_CREATION_AWS = 'SPRYKER_ACADEMY:AI_CONFIGURATION_PRODUCT_CREATION_AWS';

    /**
     * Specification:
     * - AI configuration name used by the Product Creation agent backed by Anthropic.
     *
     * @api
     */
    public const string AI_CONFIGURATION_PRODUCT_CREATION_ANTHROPIC = 'SPRYKER_ACADEMY:AI_CONFIGURATION_PRODUCT_CREATION_ANTHROPIC';

    /**
     * Specification:
     * - Configuration key for the Product Creation agent enabled flag.
     *
     * @api
     */
    public const string CONFIGURATION_KEY_AGENT_IS_ENABLED = 'ai_commerce:backoffice_assistant:general:is_product_creation_agent_enabled';

    /**
     * Specification:
     * - Configuration key for the Product Creation agent system prompt.
     *
     * @api
     */
    public const string CONFIGURATION_KEY_SYSTEM_PROMPT = 'ai_commerce:backoffice_assistant:system_prompts:product_creation_system_prompt';

    /**
     * Specification:
     * - Tool set name that groups all product creation tools available to the Product Creation agent.
     *
     * @api
     */
    public const string TOOL_SET_PRODUCT_CREATION = 'product_creation_tools';

    /**
     * Specification:
     * - Environment config key for the file system service generated product images are written to.
     * - Defaults to `storefront-media` when not configured.
     *
     * @api
     */
    public const string IMAGE_FILESYSTEM_SERVICE_NAME = 'AI_PRODUCT_CREATION:IMAGE_FILESYSTEM_SERVICE_NAME';

    /**
     * Specification:
     * - Environment config key for the OpenAI model used for product image generation.
     * - Defaults to `gpt-image-1` when not configured.
     *
     * @api
     */
    public const string IMAGE_GENERATION_MODEL = 'AI_PRODUCT_CREATION:IMAGE_GENERATION_MODEL';

    /**
     * Specification:
     * - Environment config key for the public base URL of generated product images (e.g. a CDN or bucket URL).
     * - When set, it takes precedence over the file system service's own public URL support.
     *
     * @api
     */
    public const string IMAGE_PUBLIC_URL_BASE = 'AI_PRODUCT_CREATION:IMAGE_PUBLIC_URL_BASE';
}
