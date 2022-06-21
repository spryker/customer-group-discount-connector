<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CustomerGroupDiscountConnector\Business\DecisionRule;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\CustomerGroupCollectionTransfer;
use Generated\Shared\Transfer\CustomerGroupTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\CustomerGroupDiscountConnector\Business\DecisionRule\CustomerGroupDecisionRule;
use Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade\CustomerGroupDiscountConnectorToCustomerGroupFacadeInterface;
use Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade\CustomerGroupDiscountConnectorToDiscountFacadeInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group CustomerGroupDiscountConnector
 * @group Business
 * @group DecisionRule
 * @group CustomerGroupDecisionRuleTest
 * Add your own group annotations below this line
 */
class CustomerGroupDecisionRuleTest extends Unit
{
    /**
     * @var string
     */
    protected const CUSTOMER_GROUP_NAMES_TEST_GROUP_1 = 'customer_group_names_test_group_1';

    /**
     * @var string
     */
    protected const CUSTOMER_GROUP_NAMES_TEST_GROUP_2 = 'customer_group_names_test_group_2';

    /**
     * @return void
     */
    public function testIsSatisfiedWhenCustomerIsNotSetShouldReturnFalse(): void
    {
        $customerGroupDecisionRule = $this->createCustomerGroupDecisionRule();

        $this->assertFalse(
            $customerGroupDecisionRule->isSatisfiedBy($this->createQuoteTransfer(), $this->createItemTransfer(), $this->createClauseTransfer()),
        );
    }

    /**
     * @return void
     */
    public function testIsSatisfiedWhenCustomerGroupIsNotSetShouldReturnFalse(): void
    {
        $customerGroupDecisionRule = $this->createCustomerGroupDecisionRule();

        $quoteTransfer = $this->createQuoteTransfer();

        $customerTransfer = $this->createCustomerTransfer();
        $customerTransfer->setIdCustomer(1);
        $quoteTransfer->setCustomer($customerTransfer);

        $this->assertFalse(
            $customerGroupDecisionRule->isSatisfiedBy($quoteTransfer, $this->createItemTransfer(), $this->createClauseTransfer()),
        );
    }

    /**
     * @return void
     */
    public function testIsSatisfiedWhenAllDataIsPresentShouldExecuteDiscountQueryString(): void
    {
        $discountFacadeMock = $this->createDiscountFacadeMock();
        $customerGroupFacadeMock = $this->createCustomerGroupFacadeMock();

        $quoteTransfer = $this->createQuoteTransfer();
        $customerTransfer = $this->createCustomerTransfer();
        $customerTransfer->setIdCustomer(1);
        $quoteTransfer->setCustomer($customerTransfer);

        $customerGroupCollectionTransfer = $this->createCustomerGroupCollectionTransfer();
        $customerGroupFacadeMock->expects($this->exactly(2))
            ->method('getCustomerGroupCollectionByIdCustomer')
            ->willReturn($customerGroupCollectionTransfer);

        $discountFacadeMock
            ->method('queryStringCompare')
            ->willReturnCallback(function (ClauseTransfer $clauseTransfer, string $customerGroupName) {
                return $clauseTransfer->getValue() === strtolower($customerGroupName);
            });

        $clauseTransfer = $this->createClauseTransfer();
        $itemTransfer = $this->createItemTransfer();

        $customerGroupDecisionRule = $this->createCustomerGroupDecisionRule($discountFacadeMock, $customerGroupFacadeMock);

        $clauseTransfer->setValue(static::CUSTOMER_GROUP_NAMES_TEST_GROUP_1);
        $this->assertTrue(
            $customerGroupDecisionRule->isSatisfiedBy($quoteTransfer, $itemTransfer, $clauseTransfer),
            'isSatisfiedBy should work correctly when all data is present',
        );

        $clauseTransfer->setValue(static::CUSTOMER_GROUP_NAMES_TEST_GROUP_2);
        $this->assertTrue(
            $customerGroupDecisionRule->isSatisfiedBy($quoteTransfer, $itemTransfer, $clauseTransfer),
            'isSatisfiedBy should work correctly with multiple groups when all data is present',
        );
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerGroupCollectionTransfer
     */
    protected function createCustomerGroupCollectionTransfer(): CustomerGroupCollectionTransfer
    {
        return (new CustomerGroupCollectionTransfer())
            ->addGroup($this->addCustomerGroup(static::CUSTOMER_GROUP_NAMES_TEST_GROUP_1))
            ->addGroup($this->addCustomerGroup(static::CUSTOMER_GROUP_NAMES_TEST_GROUP_2));
    }

    /**
     * @param string $name
     *
     * @return \Generated\Shared\Transfer\CustomerGroupTransfer
     */
    protected function addCustomerGroup(string $name): CustomerGroupTransfer
    {
        return (new CustomerGroupTransfer())->setName($name);
    }

    /**
     * @param \Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade\CustomerGroupDiscountConnectorToDiscountFacadeInterface|null $discountFacadeMock
     * @param \Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade\CustomerGroupDiscountConnectorToCustomerGroupFacadeInterface|null $customerGroupFacadeMock
     *
     * @return \Spryker\Zed\CustomerGroupDiscountConnector\Business\DecisionRule\CustomerGroupDecisionRule
     */
    protected function createCustomerGroupDecisionRule(
        ?CustomerGroupDiscountConnectorToDiscountFacadeInterface $discountFacadeMock = null,
        ?CustomerGroupDiscountConnectorToCustomerGroupFacadeInterface $customerGroupFacadeMock = null
    ): CustomerGroupDecisionRule {
        if ($discountFacadeMock === null) {
            $discountFacadeMock = $this->createDiscountFacadeMock();
        }

        if ($customerGroupFacadeMock === null) {
            $customerGroupFacadeMock = $this->createCustomerGroupFacadeMock();
        }

        return new CustomerGroupDecisionRule($discountFacadeMock, $customerGroupFacadeMock);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade\CustomerGroupDiscountConnectorToDiscountFacadeInterface
     */
    protected function createDiscountFacadeMock(): CustomerGroupDiscountConnectorToDiscountFacadeInterface
    {
        return $this->getMockBuilder(CustomerGroupDiscountConnectorToDiscountFacadeInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade\CustomerGroupDiscountConnectorToCustomerGroupFacadeInterface
     */
    protected function createCustomerGroupFacadeMock(): CustomerGroupDiscountConnectorToCustomerGroupFacadeInterface
    {
        return $this->getMockBuilder(CustomerGroupDiscountConnectorToCustomerGroupFacadeInterface::class)->getMock();
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer(): QuoteTransfer
    {
        return new QuoteTransfer();
    }

    /**
     * @return \Generated\Shared\Transfer\ClauseTransfer
     */
    protected function createClauseTransfer(): ClauseTransfer
    {
        return new ClauseTransfer();
    }

    /**
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function createItemTransfer(): ItemTransfer
    {
        return new ItemTransfer();
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function createCustomerTransfer(): CustomerTransfer
    {
        return new CustomerTransfer();
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerGroupTransfer
     */
    protected function createCustomerGroupTransfer(): CustomerGroupTransfer
    {
        return new CustomerGroupTransfer();
    }
}
