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
class ApproveProductToolPlugin extends AbstractPlugin implements ToolPluginInterface
{
    use LoggerTrait;

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getName(): string
    {
        return 'approve_product';
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getDescription(): string
    {
        return 'Change the approval status of a product identified by SKU (abstract or concrete). New products are created as "draft" and are not visible in the storefront until they are "approved". Use status "approved" to publish the product, or "draft"/"denied" to unpublish it.';
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
                name: 'sku',
                type: 'string',
                description: 'Abstract or concrete product SKU. Concrete SKUs are resolved to their abstract product.',
                isRequired: true,
            ),
            new ToolParameter(
                name: 'status',
                type: 'string',
                description: 'Target approval status: "approved", "draft", "waiting_for_approval", or "denied". Defaults to "approved".',
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
            return $this->getBusinessFactory()->createProductCreationWriter()->approveProduct($arguments);
        } catch (Throwable $throwable) {
            $this->getLogger()->error(sprintf('approve_product failed: %s', $throwable->getMessage()), ['exception' => $throwable]);

            return json_encode(['error' => 'Failed to change approval status.']);
        }
    }
}
