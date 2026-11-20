<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerGroupDiscountConnector\Communication\Plugin\Discount;

use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\DiscountRuleValueValidationResponseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Discount\Business\QueryString\ComparatorOperators;
use Spryker\Zed\DiscountExtension\Dependency\Plugin\DecisionRulePluginInterface;
use Spryker\Zed\DiscountExtension\Dependency\Plugin\DiscountRuleWithValueSuggestUrlPluginInterface;
use Spryker\Zed\DiscountExtension\Dependency\Plugin\DiscountRuleWithValueValidationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\CustomerGroupDiscountConnector\Business\CustomerGroupDiscountConnectorBusinessFactory getBusinessFactory()
 * @method \Spryker\Zed\CustomerGroupDiscountConnector\Business\CustomerGroupDiscountConnectorFacadeInterface getFacade()
 * @method \Spryker\Zed\CustomerGroupDiscountConnector\CustomerGroupDiscountConnectorConfig getConfig()
 */
class CustomerGroupSuggestDecisionRulePlugin extends AbstractPlugin implements DecisionRulePluginInterface, DiscountRuleWithValueSuggestUrlPluginInterface, DiscountRuleWithValueValidationPluginInterface
{
    protected const string FIELD_NAME_CUSTOMER_GROUP = 'customer-group';

    /**
     * {@inheritDoc}
     * - Matches the clause against the direct groups of `QuoteTransfer.customer` and the groups provided by the `CustomerGroupProviderPluginInterface` stack.
     * - Compares the group names case-insensitively, non-ASCII letters included.
     * - Does not match a customer without groups.
     * - For `!=`, `is not in` and `does not contain`, matches only when none of the groups matches the value.
     *
     * @api
     */
    public function isSatisfiedBy(
        QuoteTransfer $quoteTransfer,
        ItemTransfer $itemTransfer,
        ClauseTransfer $clauseTransfer
    ): bool {
        return $this->getFacade()
            ->isCustomerGroupSatisfiedBy($quoteTransfer, $itemTransfer, $clauseTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getFieldName(): string
    {
        return static::FIELD_NAME_CUSTOMER_GROUP;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return list<string>
     */
    public function acceptedDataTypes(): array
    {
        return [
            ComparatorOperators::TYPE_STRING,
            ComparatorOperators::TYPE_LIST,
        ];
    }

    /**
     * {@inheritDoc}
     * - Suggests the customer group names, the rule compares the customer's group name with the value.
     *
     * @api
     */
    public function getQueryStringValueSuggestUrl(): string
    {
        return $this->getConfig()->getCustomerGroupSuggestUrl();
    }

    /**
     * {@inheritDoc}
     * - Returns the values that match no customer group name in `invalidValues`, compared case-insensitively like the rule itself.
     *
     * @api
     *
     * @param list<string> $values
     */
    public function validateQueryStringValues(array $values): DiscountRuleValueValidationResponseTransfer
    {
        return $this->getBusinessFactory()
            ->createCustomerGroupNameValidator()
            ->validateCustomerGroupNames($values);
    }
}
