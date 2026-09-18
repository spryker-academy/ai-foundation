<?php

declare(strict_types=1);

namespace SprykerAcademy\Zed\CatalogAssistant;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class CatalogAssistantDependencyProvider extends AbstractBundleDependencyProvider
{
    public const string FACADE_PRODUCT = 'FACADE_PRODUCT';

    public const string FACADE_AVAILABILITY = 'FACADE_AVAILABILITY';

    public const string FACADE_PRICE_PRODUCT = 'FACADE_PRICE_PRODUCT';

    public const string FACADE_STORE = 'FACADE_STORE';

    public const string FACADE_LOCALE = 'FACADE_LOCALE';

    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container->set(static::FACADE_PRODUCT, static fn (Container $container) => $container->getLocator()->product()->facade());
        $container->set(static::FACADE_AVAILABILITY, static fn (Container $container) => $container->getLocator()->availability()->facade());
        $container->set(static::FACADE_PRICE_PRODUCT, static fn (Container $container) => $container->getLocator()->priceProduct()->facade());
        $container->set(static::FACADE_STORE, static fn (Container $container) => $container->getLocator()->store()->facade());
        $container->set(static::FACADE_LOCALE, static fn (Container $container) => $container->getLocator()->locale()->facade());

        return $container;
    }
}
