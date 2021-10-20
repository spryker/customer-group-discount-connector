<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerGroupDiscountConnector;

use Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade\CustomerGroupDiscountConnectorToCustomerGroupFacadeBridge;
use Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade\CustomerGroupDiscountConnectorToDiscountFacadeBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\CustomerGroupDiscountConnector\CustomerGroupDiscountConnectorConfig getConfig()
 */
class CustomerGroupDiscountConnectorDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_DISCOUNT = 'FACADE_DISCOUNT';

    /**
     * @var string
     */
    public const FACADE_CUSTOMER_GROUP = 'FACADE_CUSTOMER_GROUP';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container->set(static::FACADE_DISCOUNT, function (Container $container) {
            return new CustomerGroupDiscountConnectorToDiscountFacadeBridge(
                $container->getLocator()->discount()->facade(),
            );
        });

        $container->set(static::FACADE_CUSTOMER_GROUP, function (Container $container) {
            return new CustomerGroupDiscountConnectorToCustomerGroupFacadeBridge(
                $container->getLocator()->customerGroup()->facade(),
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        return $container;
    }
}
