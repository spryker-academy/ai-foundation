<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Business;

use Spryker\Service\FileSystem\FileSystemServiceInterface;
use Spryker\Zed\Category\Business\CategoryFacadeInterface;
use Spryker\Zed\Currency\Business\CurrencyFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\PriceProduct\Business\PriceProductFacadeInterface;
use Spryker\Zed\Product\Business\ProductFacadeInterface;
use Spryker\Zed\ProductApproval\Business\ProductApprovalFacadeInterface;
use Spryker\Zed\ProductCategory\Business\ProductCategoryFacadeInterface;
use Spryker\Zed\ProductImage\Business\ProductImageFacadeInterface;
use Spryker\Zed\ProductSearch\Business\ProductSearchFacadeInterface;
use Spryker\Zed\ShipmentType\Business\ShipmentTypeFacadeInterface;
use Spryker\Zed\Stock\Business\StockFacadeInterface;
use Spryker\Zed\Store\Business\StoreFacadeInterface;
use Spryker\Zed\Tax\Business\TaxFacadeInterface;
use SprykerAcademy\Zed\AiProductCreation\AiProductCreationDependencyProvider;
use SprykerAcademy\Zed\AiProductCreation\Business\ProductCreation\ProductCreationReader;
use SprykerAcademy\Zed\AiProductCreation\Business\ProductCreation\ProductCreationReaderInterface;
use SprykerAcademy\Zed\AiProductCreation\Business\ProductCreation\ProductCreationWriter;
use SprykerAcademy\Zed\AiProductCreation\Business\ProductCreation\ProductCreationWriterInterface;
use SprykerAcademy\Zed\AiProductCreation\Business\ProductImage\ProductImageAttacher;
use SprykerAcademy\Zed\AiProductCreation\Business\ProductImage\ProductImageAttacherInterface;
use SprykerAcademy\Zed\AiProductCreation\Business\ProductImage\ProductImageGenerator;
use SprykerAcademy\Zed\AiProductCreation\Business\ProductImage\ProductImageGeneratorInterface;

/**
 * @method \SprykerAcademy\Zed\AiProductCreation\AiProductCreationConfig getConfig()
 */
class AiProductCreationBusinessFactory extends AbstractBusinessFactory
{
    public function createProductCreationWriter(): ProductCreationWriterInterface
    {
        return new ProductCreationWriter(
            $this->getProductFacade(),
            $this->getPriceProductFacade(),
            $this->getStockFacade(),
            $this->getProductCategoryFacade(),
            $this->getProductSearchFacade(),
            $this->getCategoryFacade(),
            $this->getTaxFacade(),
            $this->getLocaleFacade(),
            $this->getStoreFacade(),
            $this->getCurrencyFacade(),
            $this->getShipmentTypeFacade(),
            $this->getProductApprovalFacade(),
            $this->getConfig(),
        );
    }

    public function createProductCreationReader(): ProductCreationReaderInterface
    {
        return new ProductCreationReader(
            $this->getCategoryFacade(),
            $this->getTaxFacade(),
            $this->getLocaleFacade(),
        );
    }

    public function createProductImageAttacher(): ProductImageAttacherInterface
    {
        return new ProductImageAttacher(
            $this->getProductFacade(),
            $this->getProductImageFacade(),
        );
    }

    public function createProductImageGenerator(): ProductImageGeneratorInterface
    {
        return new ProductImageGenerator(
            $this->getFileSystemService(),
            $this->getConfig(),
        );
    }

    public function getProductFacade(): ProductFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_PRODUCT);
    }

    public function getPriceProductFacade(): PriceProductFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_PRICE_PRODUCT);
    }

    public function getStockFacade(): StockFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_STOCK);
    }

    public function getProductCategoryFacade(): ProductCategoryFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_PRODUCT_CATEGORY);
    }

    public function getProductSearchFacade(): ProductSearchFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_PRODUCT_SEARCH);
    }

    public function getCategoryFacade(): CategoryFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_CATEGORY);
    }

    public function getTaxFacade(): TaxFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_TAX);
    }

    public function getLocaleFacade(): LocaleFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_LOCALE);
    }

    public function getStoreFacade(): StoreFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_STORE);
    }

    public function getCurrencyFacade(): CurrencyFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_CURRENCY);
    }

    public function getShipmentTypeFacade(): ShipmentTypeFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_SHIPMENT_TYPE);
    }

    public function getProductApprovalFacade(): ProductApprovalFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_PRODUCT_APPROVAL);
    }

    public function getProductImageFacade(): ProductImageFacadeInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::FACADE_PRODUCT_IMAGE);
    }

    public function getFileSystemService(): FileSystemServiceInterface
    {
        return $this->getProvidedDependency(AiProductCreationDependencyProvider::SERVICE_FILE_SYSTEM);
    }
}
