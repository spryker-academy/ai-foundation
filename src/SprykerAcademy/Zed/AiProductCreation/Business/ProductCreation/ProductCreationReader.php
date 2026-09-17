<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Business\ProductCreation;

use Spryker\Zed\Category\Business\CategoryFacadeInterface;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\Tax\Business\TaxFacadeInterface;

class ProductCreationReader implements ProductCreationReaderInterface
{
    protected CategoryFacadeInterface $categoryFacade;

    protected TaxFacadeInterface $taxFacade;

    protected LocaleFacadeInterface $localeFacade;

    public function __construct(
        CategoryFacadeInterface $categoryFacade,
        TaxFacadeInterface $taxFacade,
        LocaleFacadeInterface $localeFacade,
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->taxFacade = $taxFacade;
        $this->localeFacade = $localeFacade;
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function listCategories(array $arguments): array
    {
        $search = isset($arguments['search']) ? (string)$arguments['search'] : null;
        $localeTransfer = $this->localeFacade->getCurrentLocale();
        $categoryCollectionTransfer = $this->categoryFacade->getAllCategoryCollection($localeTransfer);

        $categories = [];
        foreach ($categoryCollectionTransfer->getCategories() as $categoryTransfer) {
            $name = (string)$categoryTransfer->getName();
            $key = (string)$categoryTransfer->getCategoryKey();

            if ($search !== null && $search !== '' && mb_stripos($name, $search) === false && mb_stripos($key, $search) === false) {
                continue;
            }

            $categories[] = [
                'idCategory' => $categoryTransfer->getIdCategory(),
                'categoryKey' => $key,
                'name' => $name,
            ];
        }

        return ['categories' => $categories];
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     *
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function listTaxSets(array $arguments): array
    {
        $taxSetCollectionTransfer = $this->taxFacade->getTaxSets();

        $taxSets = [];
        foreach ($taxSetCollectionTransfer->getTaxSets() as $taxSetTransfer) {
            $taxSets[] = [
                'idTaxSet' => $taxSetTransfer->getIdTaxSet(),
                'name' => $taxSetTransfer->getName(),
            ];
        }

        return ['taxSets' => $taxSets];
    }
}
