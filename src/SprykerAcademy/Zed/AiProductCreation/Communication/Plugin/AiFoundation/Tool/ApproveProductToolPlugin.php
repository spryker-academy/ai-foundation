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
        // TODO-5: Declare the two parameters the AI model must or may provide. Return an array of ToolParameter objects.
        // Parameter 1: name 'sku', type 'string', required.
        //              Description: abstract or concrete product SKU. Concrete SKUs are resolved to their abstract product.
        // Parameter 2: name 'status', type 'string', optional.
        //              Description: target approval status "approved", "draft", "waiting_for_approval", or "denied". Defaults to "approved".
        // Hint-1: Use named arguments: new ToolParameter(name: '...', type: '...', description: '...', isRequired: true)
        // Hint-2: The parameter names are the keys the business layer reads from $arguments.
        //         See ProductCreationWriter::approveProduct().
        // Hint-3: Have a look at SetProductStockToolPlugin::getParameters() for an example.
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
