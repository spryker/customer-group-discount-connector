<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade;

use Generated\Shared\Transfer\CustomerGroupCollectionTransfer;
use Generated\Shared\Transfer\CustomerGroupCriteriaTransfer;
use Generated\Shared\Transfer\CustomerTransfer;

interface CustomerGroupDiscountConnectorToCustomerGroupFacadeInterface
{
    /**
     * @param int $idCustomer
     *
     * @return \Generated\Shared\Transfer\CustomerGroupCollectionTransfer
     */
    public function getCustomerGroupCollectionByIdCustomer($idCustomer): CustomerGroupCollectionTransfer;

    public function getCustomerGroupCollectionByCustomer(CustomerTransfer $customerTransfer): CustomerGroupCollectionTransfer;

    public function getCustomerGroupCollection(
        CustomerGroupCriteriaTransfer $customerGroupCriteriaTransfer
    ): CustomerGroupCollectionTransfer;
}
