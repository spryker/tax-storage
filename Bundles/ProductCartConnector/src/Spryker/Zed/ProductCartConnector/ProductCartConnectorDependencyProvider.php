<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\ProductCartConnector\Dependency\Facade\ProductCartConnectorToProductBridge;

class ProductCartConnectorDependencyProvider extends AbstractBundleDependencyProvider
{

    const FACADE_PRODUCT = 'facade product';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container[self::FACADE_PRODUCT] = function (Container $container) {
            return new ProductCartConnectorToProductBridge($container->getLocator()->product()->facade());
        };

        return $container;
    }

}
