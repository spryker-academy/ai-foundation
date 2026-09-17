<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool;

use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolParameter;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Throwable;

/**
 * @method \SprykerAcademy\Zed\AiProductCreation\Business\AiProductCreationBusinessFactory getBusinessFactory()
 * @method \SprykerAcademy\Zed\AiProductCreation\AiProductCreationConfig getConfig()
 * @method \SprykerAcademy\Zed\AiProductCreation\Communication\AiProductCreationCommunicationFactory getFactory()
 */
class SetProductImageToolPlugin extends AbstractPlugin implements ToolPluginInterface
{
    use LoggerTrait;

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getName(): string
    {
        return 'set_product_image';
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getDescription(): string
    {
        return 'Attach an image to an abstract product by URL. Accepts any public http(s) image URL, including URLs returned by generate_product_image. The image becomes visible on the storefront product page.';
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return array<\Spryker\Zed\AiFoundation\Dependency\Tools\ToolParameterInterface>
     */
    public function getParameters(): array
    {
        return [
            new ToolParameter(
                name: 'abstractSku',
                type: 'string',
                description: 'Abstract product SKU to attach the image to.',
                isRequired: true,
            ),
            new ToolParameter(
                name: 'imageUrl',
                type: 'string',
                description: 'Public http(s) URL of the image.',
                isRequired: true,
            ),
            new ToolParameter(
                name: 'altText',
                type: 'string',
                description: 'Alternative text for the image. Defaults to the product name.',
                isRequired: false,
            ),
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function execute(...$arguments): mixed
    {
        try {
            /** @var array<string, mixed> $arguments */
            return $this->getBusinessFactory()->createProductImageAttacher()->attachImage($arguments);
        } catch (Throwable $throwable) {
            $this->getLogger()->error(sprintf('set_product_image failed: %s', $throwable->getMessage()), ['exception' => $throwable]);

            return json_encode(['error' => 'Failed to set product image.']);
        }
    }
}
