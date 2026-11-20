<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerGroupDiscountConnector\Business\Validator;

use Generated\Shared\Transfer\DiscountRuleValueValidationResponseTransfer;

interface CustomerGroupNameValidatorInterface
{
    /**
     * @param list<string> $customerGroupNames
     */
    public function validateCustomerGroupNames(array $customerGroupNames): DiscountRuleValueValidationResponseTransfer;
}
