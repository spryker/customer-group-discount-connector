<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerGroupDiscountConnector\Business\DecisionRule;

use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\CustomerGroupCollectionTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\CustomerGroupDiscountConnector\CustomerGroupDiscountConnectorConfig;
use Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade\CustomerGroupDiscountConnectorToCustomerGroupFacadeInterface;
use Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade\CustomerGroupDiscountConnectorToDiscountFacadeInterface;

class CustomerGroupDecisionRule implements CustomerGroupDecisionRuleInterface
{
    /**
     * @var array<string, string>
     */
    protected const array NEGATED_OPERATOR_MAP = [
        '!=' => '=',
        'is not in' => 'is in',
        'does not contain' => 'contains',
    ];

    /**
     * @var \Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade\CustomerGroupDiscountConnectorToDiscountFacadeInterface
     */
    protected $discountFacade;

    /**
     * @var \Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade\CustomerGroupDiscountConnectorToCustomerGroupFacadeInterface
     */
    protected $customerGroupFacade;

    protected CustomerGroupDiscountConnectorConfig $customerGroupDiscountConnectorConfig;

    public function __construct(
        CustomerGroupDiscountConnectorToDiscountFacadeInterface $discountFacade,
        CustomerGroupDiscountConnectorToCustomerGroupFacadeInterface $customerGroupFacade,
        CustomerGroupDiscountConnectorConfig $customerGroupDiscountConnectorConfig
    ) {
        $this->discountFacade = $discountFacade;
        $this->customerGroupFacade = $customerGroupFacade;
        $this->customerGroupDiscountConnectorConfig = $customerGroupDiscountConnectorConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $currentItemTransfer
     * @param \Generated\Shared\Transfer\ClauseTransfer $clauseTransfer
     *
     * @return bool
     */
    public function isSatisfiedBy(
        QuoteTransfer $quoteTransfer,
        ItemTransfer $currentItemTransfer,
        ClauseTransfer $clauseTransfer
    ) {
        if (!$this->rulePreCheck($quoteTransfer)) {
            return false;
        }

        $customerGroupCollectionTransfer = $this->customerGroupFacade
            ->getCustomerGroupCollectionByCustomer($quoteTransfer->getCustomerOrFail());

        if ($customerGroupCollectionTransfer->getGroups()->count() === 0) {
            return false;
        }

        $lowercasedClauseTransfer = $this->createLowercasedClauseTransfer($clauseTransfer);
        $negatedOperator = $this->findNegatedOperator($lowercasedClauseTransfer);

        if ($negatedOperator === null || !$this->customerGroupDiscountConnectorConfig->isNegatedOperatorAppliedToAllCustomerGroups()) {
            return $this->hasMatchingCustomerGroup($customerGroupCollectionTransfer, $lowercasedClauseTransfer);
        }

        return !$this->hasMatchingCustomerGroup(
            $customerGroupCollectionTransfer,
            $lowercasedClauseTransfer->setOperator($negatedOperator),
        );
    }

    protected function findNegatedOperator(ClauseTransfer $clauseTransfer): ?string
    {
        return static::NEGATED_OPERATOR_MAP[mb_strtolower(trim((string)$clauseTransfer->getOperator()))] ?? null;
    }

    protected function hasMatchingCustomerGroup(
        CustomerGroupCollectionTransfer $customerGroupCollectionTransfer,
        ClauseTransfer $clauseTransfer
    ): bool {
        foreach ($customerGroupCollectionTransfer->getGroups() as $customerGroupTransfer) {
            if ($this->discountFacade->queryStringCompare($clauseTransfer, mb_strtolower((string)$customerGroupTransfer->getName()))) {
                return true;
            }
        }

        return false;
    }

    protected function createLowercasedClauseTransfer(ClauseTransfer $clauseTransfer): ClauseTransfer
    {
        return (clone $clauseTransfer)->setValue(mb_strtolower((string)$clauseTransfer->getValue()));
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function rulePreCheck(QuoteTransfer $quoteTransfer)
    {
        if (!$quoteTransfer->getCustomer()) {
            return false;
        }

        if (!$quoteTransfer->getCustomer()->getIdCustomer()) {
            return false;
        }

        return true;
    }
}
