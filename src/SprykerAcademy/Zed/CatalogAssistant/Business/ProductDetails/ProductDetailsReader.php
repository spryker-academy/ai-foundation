<?php

declare(strict_types=1);

namespace SprykerAcademy\Zed\CatalogAssistant\Business\ProductDetails;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Zed\Availability\Business\AvailabilityFacadeInterface;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\PriceProduct\Business\PriceProductFacadeInterface;
use Spryker\Zed\Product\Business\ProductFacadeInterface;
use Spryker\Zed\Store\Business\StoreFacadeInterface;

/**
 * Collects everything the LLM may need to answer a question about one product.
 * Only facts from the database go in here. The LLM must not invent product data.
 */
class ProductDetailsReader implements ProductDetailsReaderInterface
{
    public function __construct(
        protected ProductFacadeInterface $productFacade,
        protected AvailabilityFacadeInterface $availabilityFacade,
        protected PriceProductFacadeInterface $priceProductFacade,
        protected StoreFacadeInterface $storeFacade,
        protected LocaleFacadeInterface $localeFacade,
    ) {
    }

    public function getProductDetailsJson(string $sku): string
    {
        $sku = trim($sku);
        $idProductAbstract = $this->productFacade->findProductAbstractIdBySku($sku);

        if ($idProductAbstract === null) {
            $idProductConcrete = $this->productFacade->findProductConcreteIdBySku($sku);
            if ($idProductConcrete !== null) {
                $idProductAbstract = $this->productFacade->findProductConcreteById($idProductConcrete)?->getFkProductAbstract();
            }
        }

        $productAbstractTransfer = $idProductAbstract !== null ? $this->productFacade->findProductAbstractById($idProductAbstract) : null;

        if ($productAbstractTransfer === null) {
            return (string)json_encode(['error' => sprintf('No product found for SKU "%s".', $sku)]);
        }

        $storeTransfer = $this->storeFacade->getCurrentStore(true);
        $localeTransfer = $this->localeFacade->getCurrentLocale();

        $variants = [];
        foreach ($this->productFacade->getConcreteProductsByAbstractProductId($productAbstractTransfer->getIdProductAbstractOrFail()) as $productConcreteTransfer) {
            $variants[] = $this->mapVariant($productConcreteTransfer, $storeTransfer);
        }

        $localizedName = $this->productFacade->getLocalizedProductAbstractName($productAbstractTransfer, $localeTransfer);

        return (string)json_encode([
            'abstractSku' => $productAbstractTransfer->getSku(),
            'name' => $localizedName,
            'description' => $this->findLocalizedDescription($productAbstractTransfer, $localeTransfer->getLocaleName()),
            'attributes' => $productAbstractTransfer->getAttributes(),
            'store' => $storeTransfer->getName(),
            'variants' => $variants,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapVariant(ProductConcreteTransfer $productConcreteTransfer, StoreTransfer $storeTransfer): array
    {
        $sku = $productConcreteTransfer->getSkuOrFail();
        $availability = $this->availabilityFacade->calculateAvailabilityForProductWithStore($sku, $storeTransfer);
        $priceInCents = $this->priceProductFacade->findPriceBySku($sku);

        return [
            'sku' => $sku,
            'isActive' => (bool)$productConcreteTransfer->getIsActive(),
            'attributes' => $productConcreteTransfer->getAttributes(),
            'availableQuantity' => $availability->toString(),
            'isAvailable' => $availability->greaterThan(0),
            'grossPriceInCents' => $priceInCents,
        ];
    }

    protected function findLocalizedDescription(ProductAbstractTransfer $productAbstractTransfer, ?string $localeName): ?string
    {
        foreach ($productAbstractTransfer->getLocalizedAttributes() as $localizedAttributesTransfer) {
            if ($localizedAttributesTransfer->getLocale()?->getLocaleName() === $localeName) {
                return $localizedAttributesTransfer->getDescription();
            }
        }

        return null;
    }
}
