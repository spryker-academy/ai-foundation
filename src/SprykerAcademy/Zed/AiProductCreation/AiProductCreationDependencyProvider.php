<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \SprykerAcademy\Zed\AiProductCreation\AiProductCreationConfig getConfig()
 */
class AiProductCreationDependencyProvider extends AbstractBundleDependencyProvider
{
    public const string FACADE_AI_FOUNDATION = 'FACADE_AI_FOUNDATION';

    public const string FACADE_PRODUCT = 'FACADE_PRODUCT';

    public const string FACADE_PRICE_PRODUCT = 'FACADE_PRICE_PRODUCT';

    public const string FACADE_STOCK = 'FACADE_STOCK';

    public const string FACADE_PRODUCT_CATEGORY = 'FACADE_PRODUCT_CATEGORY';

    public const string FACADE_PRODUCT_SEARCH = 'FACADE_PRODUCT_SEARCH';

    public const string FACADE_CATEGORY = 'FACADE_CATEGORY';

    public const string FACADE_TAX = 'FACADE_TAX';

    public const string FACADE_LOCALE = 'FACADE_LOCALE';

    public const string FACADE_STORE = 'FACADE_STORE';

    public const string FACADE_CURRENCY = 'FACADE_CURRENCY';

    public const string FACADE_SHIPMENT_TYPE = 'FACADE_SHIPMENT_TYPE';

    public const string FACADE_PRODUCT_APPROVAL = 'FACADE_PRODUCT_APPROVAL';

    public const string FACADE_PRODUCT_IMAGE = 'FACADE_PRODUCT_IMAGE';

    public const string SERVICE_FILE_SYSTEM = 'SERVICE_FILE_SYSTEM';

    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        $container->set(static::FACADE_AI_FOUNDATION, function (Container $container) {
            return $container->getLocator()->aiFoundation()->facade();
        });

        return $container;
    }

    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container->set(static::FACADE_PRODUCT, function (Container $container) {
            return $container->getLocator()->product()->facade();
        });

        $container->set(static::FACADE_PRICE_PRODUCT, function (Container $container) {
            return $container->getLocator()->priceProduct()->facade();
        });

        $container->set(static::FACADE_STOCK, function (Container $container) {
            return $container->getLocator()->stock()->facade();
        });

        $container->set(static::FACADE_PRODUCT_CATEGORY, function (Container $container) {
            return $container->getLocator()->productCategory()->facade();
        });

        $container->set(static::FACADE_PRODUCT_SEARCH, function (Container $container) {
            return $container->getLocator()->productSearch()->facade();
        });

        $container->set(static::FACADE_CATEGORY, function (Container $container) {
            return $container->getLocator()->category()->facade();
        });

        $container->set(static::FACADE_TAX, function (Container $container) {
            return $container->getLocator()->tax()->facade();
        });

        $container->set(static::FACADE_LOCALE, function (Container $container) {
            return $container->getLocator()->locale()->facade();
        });

        $container->set(static::FACADE_STORE, function (Container $container) {
            return $container->getLocator()->store()->facade();
        });

        $container->set(static::FACADE_CURRENCY, function (Container $container) {
            return $container->getLocator()->currency()->facade();
        });

        $container->set(static::FACADE_SHIPMENT_TYPE, function (Container $container) {
            return $container->getLocator()->shipmentType()->facade();
        });

        $container->set(static::FACADE_PRODUCT_APPROVAL, function (Container $container) {
            return $container->getLocator()->productApproval()->facade();
        });

        $container->set(static::FACADE_PRODUCT_IMAGE, function (Container $container) {
            return $container->getLocator()->productImage()->facade();
        });

        $container->set(static::SERVICE_FILE_SYSTEM, function (Container $container) {
            return $container->getLocator()->fileSystem()->service();
        });

        return $container;
    }
}
