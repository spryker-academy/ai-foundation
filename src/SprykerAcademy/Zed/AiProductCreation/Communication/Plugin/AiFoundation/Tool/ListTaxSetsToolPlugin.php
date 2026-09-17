<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Communication\Plugin\AiFoundation\Tool;

use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\AiFoundation\Dependency\Tools\ToolPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Throwable;

/**
 * @method \SprykerAcademy\Zed\AiProductCreation\Business\AiProductCreationBusinessFactory getBusinessFactory()
 * @method \SprykerAcademy\Zed\AiProductCreation\AiProductCreationConfig getConfig()
 * @method \SprykerAcademy\Zed\AiProductCreation\Communication\AiProductCreationCommunicationFactory getFactory()
 */
class ListTaxSetsToolPlugin extends AbstractPlugin implements ToolPluginInterface
{
    use LoggerTrait;

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getName(): string
    {
        // TODO-1: Return the tool name: 'list_tax_sets'
        // Hint-1: The AI model calls tools by name. The system prompt in
        //         resources/configuration/ai_product_creation.configuration.yml refers to this exact name.
        // Hint-2: Tool names are snake_case identifiers, like function names.
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getDescription(): string
    {
        // TODO-2: Return one sentence that tells the AI model what this tool does and when to use it.
        // Hint-1: The model only sees the name, the description, and the parameters. It never sees your PHP code.
        // Hint-2: Say what comes back (id and name of each tax set) and why it matters (create_product needs a valid tax set name).
        // Hint-3: Have a look at ListCategoriesToolPlugin::getDescription() for an example.
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
        // TODO-3: This tool needs no input from the AI model. Return an empty array.
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function execute(...$arguments): mixed
    {
        try {
            // TODO-4: Delegate to the business layer and return its result.
            // Hint-1: Plugins contain no business logic. Use $this->getBusinessFactory()->createProductCreationReader()
            // Hint-2: The reader method is listTaxSets($arguments). It returns a JSON string, which is what the AI model receives.
            // Hint-3: Have a look at ListCategoriesToolPlugin::execute() for the right syntax.
        } catch (Throwable $throwable) {
            $this->getLogger()->error(sprintf('list_tax_sets failed: %s', $throwable->getMessage()), ['exception' => $throwable]);

            return json_encode(['error' => 'Failed to list tax sets.']);
        }
    }
}
