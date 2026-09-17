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
class GenerateProductImageToolPlugin extends AbstractPlugin implements ToolPluginInterface
{
    use LoggerTrait;

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getName(): string
    {
        return 'generate_product_image';
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getDescription(): string
    {
        return 'Generate a product photo with AI from the product name and description and store it. Returns the public image URL. Show it to the user as a markdown image and get approval, then attach it with set_product_image.';
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
                description: 'Abstract product SKU the image is generated for. Used to name the stored file.',
                isRequired: true,
            ),
            new ToolParameter(
                name: 'productName',
                type: 'string',
                description: 'Product name used to describe the image.',
                isRequired: true,
            ),
            new ToolParameter(
                name: 'productDescription',
                type: 'string',
                description: 'Product description used to refine the image. Include user feedback when regenerating.',
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
            return $this->getBusinessFactory()->createProductImageGenerator()->generateImage($arguments);
        } catch (Throwable $throwable) {
            $this->getLogger()->error(sprintf('generate_product_image failed: %s', $throwable->getMessage()), ['exception' => $throwable]);

            return json_encode(['error' => 'Failed to generate product image.']);
        }
    }
}
