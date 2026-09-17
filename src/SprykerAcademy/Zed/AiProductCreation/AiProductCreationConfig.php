<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation;

use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerAcademy\Shared\AiProductCreation\AiProductCreationConstants;

class AiProductCreationConfig extends AbstractBundleConfig
{
    /**
     * Mirrors the project-level Back Office Assistant AI vendor setting, so this agent follows the same vendor switch as the built-in agents.
     */
    protected const string BACKOFFICE_ASSISTANT_VENDOR_CONFIGURATION_KEY = 'ai_commerce:backoffice_assistant:ai_vendor:ai_configuration';

    protected const string BACKOFFICE_ASSISTANT_AI_CONFIGURATION_AWS = 'AI_COMMERCE:AI_CONFIGURATION_BACKOFFICE_ASSISTANT_AWS';

    protected const string BACKOFFICE_ASSISTANT_AI_CONFIGURATION_ANTHROPIC = 'AI_COMMERCE:AI_CONFIGURATION_BACKOFFICE_ASSISTANT_ANTHROPIC';

    protected const string BACKOFFICE_ASSISTANT_AI_CONFIGURATION_OPENAI = 'AI_COMMERCE:AI_CONFIGURATION_BACKOFFICE_ASSISTANT_OPENAI';

    protected const string DEFAULT_STOCK_NAME = 'Warehouse1';

    protected const string DEFAULT_SHIPMENT_TYPE_KEY = 'delivery';

    /**
     * Reuses the platform-wide OpenAI vendor token, so image generation needs no extra credentials.
     */
    protected const string OPENAI_API_TOKEN_CONFIGURATION_KEY = 'ai_vendor:openai:general:api_token';

    protected const string IMAGE_FILESYSTEM_SERVICE_NAME = 'storefront-media';

    protected const string IMAGE_GENERATION_MODEL = 'gpt-image-1';

    protected const string IMAGE_PATH_PREFIX = 'ai-product-images/';

    /**
     * Specification:
     * - Returns true if the Product Creation agent is enabled.
     * - Gated by the `ai_commerce:backoffice_assistant:general:is_product_creation_agent_enabled` Configuration Management setting.
     *
     * @api
     */
    public function isProductCreationAgentEnabled(): bool
    {
        return (bool)filter_var(
            $this->getModuleConfig(AiProductCreationConstants::CONFIGURATION_KEY_AGENT_IS_ENABLED, true),
            FILTER_VALIDATE_BOOLEAN,
        );
    }

    /**
     * Specification:
     * - Returns the AI configuration name used by the Product Creation agent.
     * - Resolves the per-provider configuration from the shared Back Office Assistant vendor radio,
     *   so switching the vendor for the whole assistant also switches this agent.
     *
     * @api
     */
    public function getProductCreationAgentAiConfigurationName(): string
    {
        $vendorConfigurationName = (string)$this->getModuleConfig(
            static::BACKOFFICE_ASSISTANT_VENDOR_CONFIGURATION_KEY,
            static::BACKOFFICE_ASSISTANT_AI_CONFIGURATION_OPENAI,
        );

        return match ($vendorConfigurationName) {
            static::BACKOFFICE_ASSISTANT_AI_CONFIGURATION_AWS => AiProductCreationConstants::AI_CONFIGURATION_PRODUCT_CREATION_AWS,
            static::BACKOFFICE_ASSISTANT_AI_CONFIGURATION_ANTHROPIC => AiProductCreationConstants::AI_CONFIGURATION_PRODUCT_CREATION_ANTHROPIC,
            default => AiProductCreationConstants::AI_CONFIGURATION_PRODUCT_CREATION_OPENAI,
        };
    }

    /**
     * Specification:
     * - Returns the stock (warehouse) name used when the set_product_stock tool receives no stockName argument.
     *
     * @api
     */
    public function getDefaultStockName(): string
    {
        return static::DEFAULT_STOCK_NAME;
    }

    /**
     * Specification:
     * - Returns the shipment type key assigned to newly created concrete products so they are shippable.
     *
     * @api
     */
    public function getDefaultShipmentTypeKey(): string
    {
        return static::DEFAULT_SHIPMENT_TYPE_KEY;
    }

    /**
     * Specification:
     * - Returns the OpenAI API token used for product image generation.
     * - Read from the `ai_vendor:openai:general:api_token` Configuration Management setting; empty when not configured.
     *
     * @api
     */
    public function getOpenAiApiToken(): string
    {
        return (string)$this->getModuleConfig(static::OPENAI_API_TOKEN_CONFIGURATION_KEY, '');
    }

    /**
     * Specification:
     * - Returns the file system service name generated product images are written to.
     * - Configurable via the `AI_PRODUCT_CREATION:IMAGE_FILESYSTEM_SERVICE_NAME` environment config key.
     * - The default `storefront-media` service is S3-backed in production and a local public assets directory in development.
     *
     * @api
     */
    public function getImageFilesystemServiceName(): string
    {
        return (string)$this->get(AiProductCreationConstants::IMAGE_FILESYSTEM_SERVICE_NAME, static::IMAGE_FILESYSTEM_SERVICE_NAME);
    }

    /**
     * Specification:
     * - Returns the OpenAI model used for product image generation.
     * - Configurable via the `AI_PRODUCT_CREATION:IMAGE_GENERATION_MODEL` environment config key.
     *
     * @api
     */
    public function getImageGenerationModel(): string
    {
        return (string)$this->get(AiProductCreationConstants::IMAGE_GENERATION_MODEL, static::IMAGE_GENERATION_MODEL);
    }

    /**
     * Specification:
     * - Returns the path prefix generated product images are stored under inside the file system service.
     *
     * @api
     */
    public function getImagePathPrefix(): string
    {
        return static::IMAGE_PATH_PREFIX;
    }

    /**
     * Specification:
     * - Returns the public base URL for generated product images (e.g. a CDN or bucket URL).
     * - Configurable via the `AI_PRODUCT_CREATION:IMAGE_PUBLIC_URL_BASE` environment config key.
     * - When set, it takes precedence over the file system service's own public URL support.
     *
     * @api
     */
    public function getImagePublicUrlBase(): string
    {
        return (string)$this->get(AiProductCreationConstants::IMAGE_PUBLIC_URL_BASE, '');
    }
}
