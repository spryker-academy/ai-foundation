<?php

declare(strict_types=1);

namespace SprykerAcademy\Zed\CatalogAssistant\Business;

use Spryker\Zed\Availability\Business\AvailabilityFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\PriceProduct\Business\PriceProductFacadeInterface;
use Spryker\Zed\Product\Business\ProductFacadeInterface;
use Spryker\Zed\Store\Business\StoreFacadeInterface;
use SprykerAcademy\Zed\CatalogAssistant\Business\ProductDetails\ProductDetailsReader;
use SprykerAcademy\Zed\CatalogAssistant\Business\ProductDetails\ProductDetailsReaderInterface;
use SprykerAcademy\Zed\CatalogAssistant\CatalogAssistantDependencyProvider;

class CatalogAssistantBusinessFactory extends AbstractBusinessFactory
{
    public function createProductDetailsReader(): ProductDetailsReaderInterface
    {
        return new ProductDetailsReader(
            $this->getProductFacade(),
            $this->getAvailabilityFacade(),
            $this->getPriceProductFacade(),
            $this->getStoreFacade(),
            $this->getLocaleFacade(),
        );
    }

    public function getProductFacade(): ProductFacadeInterface
    {
        return $this->getProvidedDependency(CatalogAssistantDependencyProvider::FACADE_PRODUCT);
    }

    public function getAvailabilityFacade(): AvailabilityFacadeInterface
    {
        return $this->getProvidedDependency(CatalogAssistantDependencyProvider::FACADE_AVAILABILITY);
    }

    public function getPriceProductFacade(): PriceProductFacadeInterface
    {
        return $this->getProvidedDependency(CatalogAssistantDependencyProvider::FACADE_PRICE_PRODUCT);
    }

    public function getStoreFacade(): StoreFacadeInterface
    {
        return $this->getProvidedDependency(CatalogAssistantDependencyProvider::FACADE_STORE);
    }

    public function getLocaleFacade(): LocaleFacadeInterface
    {
        return $this->getProvidedDependency(CatalogAssistantDependencyProvider::FACADE_LOCALE);
    }
}
