<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade;

use Generated\Shared\Transfer\CustomerGroupCollectionTransfer;
use Generated\Shared\Transfer\CustomerGroupCriteriaTransfer;
use Generated\Shared\Transfer\CustomerTransfer;

class CustomerGroupDiscountConnectorToCustomerGroupFacadeBridge implements CustomerGroupDiscountConnectorToCustomerGroupFacadeInterface
{
    /**
     * @var \Spryker\Zed\CustomerGroup\Business\CustomerGroupFacadeInterface
     */
    protected $customerGroupFacade;

    /**
     * @param \Spryker\Zed\CustomerGroup\Business\CustomerGroupFacadeInterface $customerGroupFacade
     */
    public function __construct($customerGroupFacade)
    {
        $this->customerGroupFacade = $customerGroupFacade;
    }

    /**
     * @param int $idCustomer
     *
     * @return \Generated\Shared\Transfer\CustomerGroupCollectionTransfer
     */
    public function getCustomerGroupCollectionByIdCustomer($idCustomer): CustomerGroupCollectionTransfer
    {
        return $this->customerGroupFacade
            ->getCustomerGroupCollectionByIdCustomer($idCustomer);
    }

    public function getCustomerGroupCollectionByCustomer(CustomerTransfer $customerTransfer): CustomerGroupCollectionTransfer
    {
        return $this->customerGroupFacade->getCustomerGroupCollectionByCustomer($customerTransfer);
    }

    public function getCustomerGroupCollection(
        CustomerGroupCriteriaTransfer $customerGroupCriteriaTransfer
    ): CustomerGroupCollectionTransfer {
        return $this->customerGroupFacade->getCustomerGroupCollection($customerGroupCriteriaTransfer);
    }
}
