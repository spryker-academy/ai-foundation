<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Business\ProductCreation;

use ArrayObject;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\LocalizedAttributesTransfer;
use Generated\Shared\Transfer\MoneyValueTransfer;
use Generated\Shared\Transfer\PriceProductDimensionTransfer;
use Generated\Shared\Transfer\PriceProductFilterTransfer;
use Generated\Shared\Transfer\PriceProductTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\ShipmentTypeConditionsTransfer;
use Generated\Shared\Transfer\ShipmentTypeCriteriaTransfer;
use Generated\Shared\Transfer\ShipmentTypeTransfer;
use Generated\Shared\Transfer\StockProductTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Shared\PriceProduct\PriceProductConstants;
use Spryker\Shared\ProductApproval\ProductApprovalConfig as SharedProductApprovalConfig;
use Spryker\Zed\Category\Business\CategoryFacadeInterface;
use Spryker\Zed\Currency\Business\CurrencyFacadeInterface;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\PriceProduct\Business\Exception\ProductPriceChangeException;
use Spryker\Zed\PriceProduct\Business\PriceProductFacadeInterface;
use Spryker\Zed\Product\Business\ProductFacadeInterface;
use Spryker\Zed\ProductApproval\Business\ProductApprovalFacadeInterface;
use Spryker\Zed\ProductCategory\Business\ProductCategoryFacadeInterface;
use Spryker\Zed\ProductSearch\Business\ProductSearchFacadeInterface;
use Spryker\Zed\ShipmentType\Business\ShipmentTypeFacadeInterface;
use Spryker\Zed\Stock\Business\StockFacadeInterface;
use Spryker\Zed\Store\Business\StoreFacadeInterface;
use Spryker\Zed\Tax\Business\TaxFacadeInterface;
use SprykerAcademy\Zed\AiProductCreation\AiProductCreationConfig;
use Throwable;

class ProductCreationWriter implements ProductCreationWriterInterface
{
    protected ProductFacadeInterface $productFacade;

    protected PriceProductFacadeInterface $priceProductFacade;

    protected StockFacadeInterface $stockFacade;

    protected ProductCategoryFacadeInterface $productCategoryFacade;

    protected ProductSearchFacadeInterface $productSearchFacade;

    protected CategoryFacadeInterface $categoryFacade;

    protected TaxFacadeInterface $taxFacade;

    protected LocaleFacadeInterface $localeFacade;

    protected StoreFacadeInterface $storeFacade;

    protected CurrencyFacadeInterface $currencyFacade;

    protected ShipmentTypeFacadeInterface $shipmentTypeFacade;

    protected ProductApprovalFacadeInterface $productApprovalFacade;

    protected AiProductCreationConfig $config;

    public function __construct(
        ProductFacadeInterface $productFacade,
        PriceProductFacadeInterface $priceProductFacade,
        StockFacadeInterface $stockFacade,
        ProductCategoryFacadeInterface $productCategoryFacade,
        ProductSearchFacadeInterface $productSearchFacade,
        CategoryFacadeInterface $categoryFacade,
        TaxFacadeInterface $taxFacade,
        LocaleFacadeInterface $localeFacade,
        StoreFacadeInterface $storeFacade,
        CurrencyFacadeInterface $currencyFacade,
        ShipmentTypeFacadeInterface $shipmentTypeFacade,
        ProductApprovalFacadeInterface $productApprovalFacade,
        AiProductCreationConfig $config,
    ) {
        $this->productFacade = $productFacade;
        $this->priceProductFacade = $priceProductFacade;
        $this->stockFacade = $stockFacade;
        $this->productCategoryFacade = $productCategoryFacade;
        $this->productSearchFacade = $productSearchFacade;
        $this->categoryFacade = $categoryFacade;
        $this->taxFacade = $taxFacade;
        $this->localeFacade = $localeFacade;
        $this->storeFacade = $storeFacade;
        $this->currencyFacade = $currencyFacade;
        $this->shipmentTypeFacade = $shipmentTypeFacade;
        $this->productApprovalFacade = $productApprovalFacade;
        $this->config = $config;
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function createProduct(array $arguments): array
    {
        $sku = (string)($arguments['sku'] ?? '');
        if ($sku === '') {
            return ['error' => 'A "sku" for the abstract product is required.'];
        }

        $name = (string)($arguments['name'] ?? $sku);
        $description = isset($arguments['description']) ? (string)$arguments['description'] : '';
        $attributes = (array)($arguments['attributes'] ?? []);
        $isActive = (bool)($arguments['isActive'] ?? true);
        $isSearchable = (bool)($arguments['isSearchable'] ?? true);

        $locales = $this->getStoreLocales();
        $idTaxSet = $this->resolveTaxSetId(isset($arguments['taxSetName']) ? (string)$arguments['taxSetName'] : null);

        $productAbstractTransfer = (new ProductAbstractTransfer())
            ->setSku($sku)
            ->setAttributes($attributes)
            ->setIsActive($isActive)
            ->setLocalizedAttributes(new ArrayObject(
                $this->buildLocalizedAttributes($locales, $name, $description, (array)($arguments['localizedAttributes'] ?? []), $isSearchable),
            ))
            ->setStoreRelation($this->buildStoreRelation());

        if ($idTaxSet !== null) {
            $productAbstractTransfer->setIdTaxSet($idTaxSet);
        }

        $concreteTransfers = $this->buildConcreteTransfers($arguments, $sku, $name, $description, $isActive, $isSearchable, $locales, $this->findDefaultShipmentType());

        $idProductAbstract = $this->productFacade->addProduct($productAbstractTransfer, $concreteTransfers);
        $productAbstractTransfer->setIdProductAbstract($idProductAbstract);

        // addProduct() does not publish; URL creation and concrete activation trigger the events that make the product storefront-visible.
        $this->productFacade->createProductUrl($productAbstractTransfer);

        $concretes = [];
        foreach ($concreteTransfers as $concreteTransfer) {
            $idProductConcrete = $this->productFacade->findProductConcreteIdBySku((string)$concreteTransfer->getSku());

            if ($idProductConcrete !== null) {
                $concreteTransfer->setIdProductConcrete($idProductConcrete);
                $this->productSearchFacade->persistProductSearch($concreteTransfer);
                $this->productFacade->activateProductConcrete($idProductConcrete);
            }

            $concretes[] = [
                'sku' => $concreteTransfer->getSku(),
                'idProductConcrete' => $idProductConcrete,
            ];
        }

        return [
            'idProductAbstract' => $idProductAbstract,
            'abstractSku' => $sku,
            'concretes' => $concretes,
            'message' => sprintf('Abstract product "%s" created with %d concrete(s).', $sku, count($concretes)),
        ];
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function setPrice(array $arguments): array
    {
        $sku = (string)($arguments['sku'] ?? '');
        if ($sku === '') {
            return ['error' => 'A product "sku" is required.'];
        }

        $grossAmount = (int)($arguments['grossAmount'] ?? 0);
        $netAmount = isset($arguments['netAmount']) ? (int)$arguments['netAmount'] : $grossAmount;
        $priceType = (string)($arguments['priceType'] ?? $this->priceProductFacade->getDefaultPriceTypeName());

        $storeTransfer = isset($arguments['store']) && $arguments['store'] !== ''
            ? $this->storeFacade->getStoreByName((string)$arguments['store'])
            : $this->storeFacade->getCurrentStore(true);

        $currencyCode = isset($arguments['currency']) && $arguments['currency'] !== ''
            ? (string)$arguments['currency']
            : (string)$storeTransfer->getDefaultCurrencyIsoCode();
        $currencyTransfer = $this->currencyFacade->fromIsoCode($currencyCode);

        if ($this->productFacade->hasProductConcrete($sku)) {
            $abstractSku = $this->productFacade->getAbstractSkuFromProductConcrete($sku);
            $idProductAbstract = (int)$this->productFacade->getProductAbstractIdByConcreteSku($sku);

            $concretePriceTransfer = $this->buildPriceProductTransfer($priceType, $grossAmount, $netAmount, $currencyTransfer, $storeTransfer)
                ->setIdProduct($this->productFacade->findProductConcreteIdBySku($sku))
                ->setSkuProduct($sku)
                ->setIdProductAbstract($idProductAbstract)
                ->setSkuProductAbstract($abstractSku);
            $this->priceProductFacade->createPriceForProduct($concretePriceTransfer);

            // The storefront reads the price from the product-abstract level.
            $abstractPriceSet = $this->createAbstractPriceIfMissing($abstractSku, $idProductAbstract, $priceType, $grossAmount, $netAmount, $currencyTransfer, $currencyCode, $storeTransfer);

            return [
            'message' => sprintf(
                'Price %d %s (%s) set for concrete "%s"%s.',
                $grossAmount,
                $currencyCode,
                $priceType,
                $sku,
                $abstractPriceSet ? sprintf(' and its abstract "%s"', $abstractSku) : '',
            )];
        }

        if ($this->productFacade->hasProductAbstract($sku)) {
            $this->createAbstractPriceIfMissing($sku, (int)$this->productFacade->findProductAbstractIdBySku($sku), $priceType, $grossAmount, $netAmount, $currencyTransfer, $currencyCode, $storeTransfer);

            return ['message' => sprintf('Price %d %s (%s) set for abstract "%s".', $grossAmount, $currencyCode, $priceType, $sku)];
        }

        return ['error' => sprintf('No product found for SKU "%s".', $sku)];
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function approveProduct(array $arguments): array
    {
        $sku = (string)($arguments['sku'] ?? '');
        if ($sku === '') {
            return ['error' => 'A product "sku" is required.'];
        }

        if ($this->productFacade->hasProductAbstract($sku)) {
            $idProductAbstract = (int)$this->productFacade->findProductAbstractIdBySku($sku);
        } elseif ($this->productFacade->hasProductConcrete($sku)) {
            $idProductAbstract = (int)$this->productFacade->getProductAbstractIdByConcreteSku($sku);
        } else {
            return ['error' => sprintf('No product found for SKU "%s".', $sku)];
        }

        $productAbstractTransfer = $this->productFacade->findProductAbstractById($idProductAbstract);
        if ($productAbstractTransfer === null) {
            return ['error' => sprintf('No abstract product found for SKU "%s".', $sku)];
        }

        $requestedStatus = (string)($arguments['status'] ?? SharedProductApprovalConfig::STATUS_APPROVED);
        $currentStatus = $productAbstractTransfer->getApprovalStatus() ?: SharedProductApprovalConfig::STATUS_DRAFT;

        if ($requestedStatus === $currentStatus) {
            return ['message' => sprintf('Product "%s" is already "%s".', $productAbstractTransfer->getSku(), $currentStatus)];
        }

        $applicableStatuses = $this->productApprovalFacade->getApplicableApprovalStatuses($currentStatus);
        if (!in_array($requestedStatus, $applicableStatuses, true)) {
            return [
            'error' => sprintf(
                'Cannot change approval status from "%s" to "%s". Allowed transitions: %s.',
                $currentStatus,
                $requestedStatus,
                implode(', ', $applicableStatuses),
            )];
        }

        // saveProductAbstract() re-triggers the abstract publish event, so the new status is reflected in storage and search.
        $productAbstractTransfer->setApprovalStatus($requestedStatus);
        $this->productFacade->saveProductAbstract($productAbstractTransfer);

        return [
        'message' => sprintf(
            'Approval status of "%s" changed from "%s" to "%s".',
            $productAbstractTransfer->getSku(),
            $currentStatus,
            $requestedStatus,
        )];
    }

    protected function buildPriceProductTransfer(
        string $priceType,
        int $grossAmount,
        int $netAmount,
        CurrencyTransfer $currencyTransfer,
        StoreTransfer $storeTransfer,
    ): PriceProductTransfer {
        $moneyValueTransfer = (new MoneyValueTransfer())
            ->setGrossAmount($grossAmount)
            ->setNetAmount($netAmount)
            ->setCurrency($currencyTransfer)
            ->setFkCurrency($currencyTransfer->getIdCurrency())
            ->setStore($storeTransfer)
            ->setFkStore($storeTransfer->getIdStore());

        return (new PriceProductTransfer())
            ->setPriceTypeName($priceType)
            ->setPriceDimension((new PriceProductDimensionTransfer())->setType(PriceProductConstants::PRICE_DIMENSION_DEFAULT))
            ->setMoneyValue($moneyValueTransfer);
    }

    protected function createAbstractPriceIfMissing(
        string $abstractSku,
        int $idProductAbstract,
        string $priceType,
        int $grossAmount,
        int $netAmount,
        CurrencyTransfer $currencyTransfer,
        string $currencyCode,
        StoreTransfer $storeTransfer,
    ): bool {
        $priceProductFilterTransfer = (new PriceProductFilterTransfer())
            ->setSku($abstractSku)
            ->setPriceTypeName($priceType)
            ->setCurrencyIsoCode($currencyCode)
            ->setStoreName((string)$storeTransfer->getName());

        if ($this->priceProductFacade->findPriceProductFor($priceProductFilterTransfer) !== null) {
            return false;
        }

        $abstractPriceTransfer = $this->buildPriceProductTransfer($priceType, $grossAmount, $netAmount, $currencyTransfer, $storeTransfer)
            ->setIdProductAbstract($idProductAbstract)
            ->setSkuProductAbstract($abstractSku);

        try {
            $this->priceProductFacade->createPriceForProduct($abstractPriceTransfer);
        } catch (ProductPriceChangeException $exception) {
            return false;
        }

        return true;
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function setStock(array $arguments): array
    {
        $sku = (string)($arguments['sku'] ?? '');
        if ($sku === '') {
            return ['error' => 'A concrete product "sku" is required.'];
        }

        if (!$this->productFacade->hasProductConcrete($sku)) {
            return ['error' => sprintf('No concrete product found for SKU "%s". Stock is set on concrete products.', $sku)];
        }

        $quantity = (string)($arguments['quantity'] ?? '0');
        $stockName = (string)($arguments['stockName'] ?? $this->config->getDefaultStockName());
        $isNeverOutOfStock = !empty($arguments['isNeverOutOfStock']) ? '1' : '0';

        $stockProductTransfer = (new StockProductTransfer())
            ->setSku($sku)
            ->setQuantity($quantity)
            ->setStockType($stockName)
            ->setIsNeverOutOfStock($isNeverOutOfStock);

        $productConcreteTransfer = (new ProductConcreteTransfer())
            ->setSku($sku)
            ->setIdProductConcrete($this->productFacade->findProductConcreteIdBySku($sku));
        $productConcreteTransfer->addStock($stockProductTransfer);

        $this->stockFacade->persistStockProductCollection($productConcreteTransfer);

        return ['message' => sprintf('Stock %s set for "%s" in "%s".', $quantity, $sku, $stockName)];
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function assignCategory(array $arguments): array
    {
        $abstractSku = (string)($arguments['abstractSku'] ?? $arguments['sku'] ?? '');
        $categoryKey = (string)($arguments['categoryKey'] ?? '');

        if ($abstractSku === '' || $categoryKey === '') {
            return ['error' => 'Both "abstractSku" and "categoryKey" are required.'];
        }

        $idProductAbstract = $this->productFacade->findProductAbstractIdBySku($abstractSku);
        if ($idProductAbstract === null) {
            return ['error' => sprintf('No abstract product found for SKU "%s".', $abstractSku)];
        }

        $idCategory = $this->resolveCategoryId($categoryKey);
        if ($idCategory === null) {
            return ['error' => sprintf('No category found for key or name "%s". Use list_categories first.', $categoryKey)];
        }

        $this->productCategoryFacade->createProductCategoryMappings($idCategory, [$idProductAbstract]);

        return ['message' => sprintf('Abstract product "%s" assigned to category "%s".', $abstractSku, $categoryKey)];
    }

    /**
     * @param array<string, mixed> $arguments
     * @param string $abstractSku
     * @param string $name
     * @param string $description
     * @param bool $isActive
     * @param bool $isSearchable
     * @param array<string, \Generated\Shared\Transfer\LocaleTransfer> $locales
     * @param \Generated\Shared\Transfer\ShipmentTypeTransfer|null $shipmentTypeTransfer
     *
     * @return array<\Generated\Shared\Transfer\ProductConcreteTransfer>
     */
    protected function buildConcreteTransfers(
        array $arguments,
        string $abstractSku,
        string $name,
        string $description,
        bool $isActive,
        bool $isSearchable,
        array $locales,
        ?ShipmentTypeTransfer $shipmentTypeTransfer = null,
    ): array {
        $concreteData = (array)($arguments['concretes'] ?? []);
        if ($concreteData === []) {
            $concreteData = [['sku' => $abstractSku . '-1', 'name' => $name, 'attributes' => []]];
        }

        $concreteTransfers = [];
        $index = 0;
        foreach ($concreteData as $concrete) {
            $index++;
            $concrete = (array)$concrete;
            $concreteSku = (string)($concrete['sku'] ?? ($abstractSku . '-' . $index));
            $concreteName = (string)($concrete['name'] ?? $name);
            $concreteAttributes = (array)($concrete['attributes'] ?? []);

            $concreteTransfer = (new ProductConcreteTransfer())
                ->setSku($concreteSku)
                ->setAbstractSku($abstractSku)
                ->setIsActive($isActive)
                ->setAttributes($concreteAttributes)
                ->setLocalizedAttributes(new ArrayObject(
                    $this->buildLocalizedAttributes($locales, $concreteName, $description, (array)($concrete['localizedAttributes'] ?? []), $isSearchable),
                ));

            if ($shipmentTypeTransfer !== null) {
                $concreteTransfer->addShipmentType($shipmentTypeTransfer);
            }

            $concreteTransfers[] = $concreteTransfer;
        }

        return $concreteTransfers;
    }

    protected function findDefaultShipmentType(): ?ShipmentTypeTransfer
    {
        $shipmentTypeCriteriaTransfer = (new ShipmentTypeCriteriaTransfer())
            ->setShipmentTypeConditions(
                (new ShipmentTypeConditionsTransfer())
                    ->setKeys([$this->config->getDefaultShipmentTypeKey()])
                    ->setIsActive(true),
            );

        foreach ($this->shipmentTypeFacade->getShipmentTypeCollection($shipmentTypeCriteriaTransfer)->getShipmentTypes() as $shipmentTypeTransfer) {
            return $shipmentTypeTransfer;
        }

        return null;
    }

    /**
     * @param array<string, \Generated\Shared\Transfer\LocaleTransfer> $locales
     * @param string $name
     * @param string $description
     * @param array<int, array<string, mixed>> $explicitLocalizedAttributes
     * @param bool $isSearchable
     *
     * @return array<\Generated\Shared\Transfer\LocalizedAttributesTransfer>
     */
    protected function buildLocalizedAttributes(
        array $locales,
        string $name,
        string $description,
        array $explicitLocalizedAttributes,
        bool $isSearchable = true
    ): array {
        $localizedAttributes = [];

        if ($explicitLocalizedAttributes !== []) {
            foreach ($explicitLocalizedAttributes as $entry) {
                $entry = (array)$entry;
                $localeTransfer = $this->findLocale($locales, (string)($entry['locale'] ?? ''));
                if ($localeTransfer === null) {
                    continue;
                }

                $localizedAttributes[] = (new LocalizedAttributesTransfer())
                    ->setLocale($localeTransfer)
                    ->setName((string)($entry['name'] ?? $name))
                    ->setDescription((string)($entry['description'] ?? $description))
                    ->setIsSearchable($isSearchable);
            }

            if ($localizedAttributes !== []) {
                return $localizedAttributes;
            }
        }

        foreach ($locales as $localeTransfer) {
            $localizedAttributes[] = (new LocalizedAttributesTransfer())
                ->setLocale($localeTransfer)
                ->setName($name)
                ->setDescription($description)
                ->setIsSearchable($isSearchable);
        }

        return $localizedAttributes;
    }

    /**
     * @return array<string, \Generated\Shared\Transfer\LocaleTransfer>
     */
    protected function getStoreLocales(): array
    {
        $storeTransfer = $this->storeFacade->getCurrentStore(true);
        $localeCollection = $this->localeFacade->getLocaleCollection();

        $locales = [];
        foreach ($storeTransfer->getAvailableLocaleIsoCodes() as $localeIsoCode) {
            if (!isset($localeCollection[$localeIsoCode])) {
                continue;
            }

            $locales[$localeIsoCode] = $localeCollection[$localeIsoCode];
        }

        if ($locales === []) {
            $currentLocaleTransfer = $this->localeFacade->getCurrentLocale();
            $locales[(string)$currentLocaleTransfer->getLocaleName()] = $currentLocaleTransfer;
        }

        return $locales;
    }

    /**
     * @param array<string, \Generated\Shared\Transfer\LocaleTransfer> $locales
     * @param string $localeName
     */
    protected function findLocale(array $locales, string $localeName): ?LocaleTransfer
    {
        if ($localeName === '') {
            return null;
        }

        if (isset($locales[$localeName])) {
            return $locales[$localeName];
        }

        try {
            return $this->localeFacade->getLocale($localeName);
        } catch (Throwable $throwable) {
            return null;
        }
    }

    protected function buildStoreRelation(): StoreRelationTransfer
    {
        $idStores = [];
        foreach ($this->storeFacade->getAllStores() as $storeTransfer) {
            $idStores[] = $storeTransfer->getIdStoreOrFail();
        }

        return (new StoreRelationTransfer())->setIdStores($idStores);
    }

    protected function resolveTaxSetId(?string $taxSetName): ?int
    {
        if ($taxSetName === null || $taxSetName === '') {
            return null;
        }

        $taxSetNameLower = mb_strtolower($taxSetName);
        foreach ($this->taxFacade->getTaxSets()->getTaxSets() as $taxSetTransfer) {
            if (mb_strtolower((string)$taxSetTransfer->getName()) === $taxSetNameLower) {
                return $taxSetTransfer->getIdTaxSet();
            }
        }

        return null;
    }

    protected function resolveCategoryId(string $categoryKey): ?int
    {
        $localeTransfer = $this->localeFacade->getCurrentLocale();

        $categoryKeyLower = mb_strtolower($categoryKey);
        foreach ($this->categoryFacade->getAllCategoryCollection($localeTransfer)->getCategories() as $categoryTransfer) {
            if (
                mb_strtolower((string)$categoryTransfer->getCategoryKey()) === $categoryKeyLower
                || mb_strtolower((string)$categoryTransfer->getName()) === $categoryKeyLower
            ) {
                return $categoryTransfer->getIdCategory();
            }
        }

        return null;
    }
}
