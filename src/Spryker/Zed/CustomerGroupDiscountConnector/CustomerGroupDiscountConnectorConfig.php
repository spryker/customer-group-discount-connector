<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerGroupDiscountConnector;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class CustomerGroupDiscountConnectorConfig extends AbstractBundleConfig
{
    /**
     * @uses \Spryker\Zed\CustomerGroup\Communication\Controller\SuggestController::indexAction()
     */
    protected const string CUSTOMER_GROUP_SUGGEST_URL = '/customer-group/suggest';

    protected const bool IS_NEGATED_OPERATOR_APPLIED_TO_ALL_CUSTOMER_GROUPS = false;

    /**
     * Specification:
     * - Returns the Back Office URL that suggests customer group names for the `customer-group` discount rule field.
     *
     * @api
     */
    public function getCustomerGroupSuggestUrl(): string
    {
        return static::CUSTOMER_GROUP_SUGGEST_URL;
    }

    /**
     * Specification:
     * - Defines how the `customer-group` discount rule evaluates `!=`, `is not in` and `does not contain` for a customer
     *   in several groups.
     * - When `false`, the rule is satisfied when at least one of the groups satisfies the clause.
     * - When `true`, the rule is satisfied only when none of the groups matches the value.
     *
     * @api
     */
    public function isNegatedOperatorAppliedToAllCustomerGroups(): bool
    {
        return static::IS_NEGATED_OPERATOR_APPLIED_TO_ALL_CUSTOMER_GROUPS;
    }
}
