<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerGroupDiscountConnector\Business;

use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

/**
 * @method \Spryker\Zed\CustomerGroupDiscountConnector\Business\CustomerGroupDiscountConnectorBusinessFactory getFactory()
 */
interface CustomerGroupDiscountConnectorFacadeInterface
{
    /**
     * Specification:
     *
     *  - Check if provided group is assigned to current logged in customer.
     *  - Resolves the customer groups of `QuoteTransfer.customer` with `CustomerGroupFacade::getCustomerGroupCollectionByCustomer()`:
     *    the direct groups plus those provided by the `CustomerGroupProviderPluginInterface` stack.
     *  - Return true if group is set.
     *  - Returns false when the customer has no groups.
     *  - Returns true when at least one of the groups satisfies the clause.
     *  - For `!=`, `is not in` and `does not contain`, returns true only when none of the groups matches the value,
     *    if `CustomerGroupDiscountConnectorConfig::isNegatedOperatorAppliedToAllCustomerGroups()` returns `true`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\ClauseTransfer $clauseTransfer
     *
     * @return bool
     */
    public function isCustomerGroupSatisfiedBy(QuoteTransfer $quoteTransfer, ItemTransfer $itemTransfer, ClauseTransfer $clauseTransfer);
}
