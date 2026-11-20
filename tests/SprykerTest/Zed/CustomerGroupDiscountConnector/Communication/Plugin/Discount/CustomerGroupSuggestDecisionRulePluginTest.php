<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CustomerGroupDiscountConnector\Communication\Plugin\Discount;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\ClauseBuilder;
use Generated\Shared\DataBuilder\QuoteBuilder;
use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\CustomerGroupToCustomerAssignmentTransfer;
use Generated\Shared\Transfer\CustomerGroupTransfer;
use Spryker\Zed\CustomerGroupDiscountConnector\Communication\Plugin\Discount\CustomerGroupSuggestDecisionRulePlugin;
use Spryker\Zed\Discount\Business\QueryString\ComparatorOperators;
use Spryker\Zed\Discount\Business\QueryString\Specification\MetaData\MetaProviderFactory;
use Spryker\Zed\Discount\DiscountDependencyProvider;
use SprykerTest\Zed\CustomerGroupDiscountConnector\CustomerGroupDiscountConnectorCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group CustomerGroupDiscountConnector
 * @group Communication
 * @group Plugin
 * @group Discount
 * @group CustomerGroupSuggestDecisionRulePluginTest
 * Add your own group annotations below this line
 */
class CustomerGroupSuggestDecisionRulePluginTest extends Unit
{
    /**
     * @uses \Spryker\Zed\CustomerGroup\Communication\Controller\SuggestController::indexAction()
     */
    protected const string CUSTOMER_GROUP_SUGGEST_URL = '/customer-group/suggest';

    protected const string FIELD_NAME = 'customer-group';

    protected const string MISSING_CUSTOMER_GROUP_NAME = 'missing-customer-group';

    protected const string ASSIGNED_NON_ASCII_CUSTOMER_GROUP_NAME = 'Ärzte Suggest Plugin Test';

    protected const string OTHER_NON_ASCII_CUSTOMER_GROUP_NAME = 'Österreich Suggest Plugin Test';

    protected CustomerGroupDiscountConnectorCommunicationTester $tester;

    public function testGetQueryStringValueSuggestUrlReturnsTheCustomerGroupSuggestUrl(): void
    {
        // Arrange
        $customerGroupSuggestDecisionRulePlugin = new CustomerGroupSuggestDecisionRulePlugin();

        // Act
        $valueSuggestUrl = $customerGroupSuggestDecisionRulePlugin->getQueryStringValueSuggestUrl();

        // Assert
        $this->assertSame(static::CUSTOMER_GROUP_SUGGEST_URL, $valueSuggestUrl);
    }

    public function testValidateQueryStringValuesReturnsOnlyTheNamesOfMissingCustomerGroups(): void
    {
        // Arrange
        $customerGroupTransfer = $this->tester->haveCustomerGroup([CustomerGroupTransfer::NAME => sprintf('Existing Group %s', uniqid())]);

        // Act
        $discountRuleValueValidationResponseTransfer = (new CustomerGroupSuggestDecisionRulePlugin())->validateQueryStringValues([
            mb_strtolower($customerGroupTransfer->getNameOrFail()),
            static::MISSING_CUSTOMER_GROUP_NAME,
        ]);

        // Assert
        $this->assertFalse($discountRuleValueValidationResponseTransfer->getIsSuccessful());
        $this->assertSame([static::MISSING_CUSTOMER_GROUP_NAME], $discountRuleValueValidationResponseTransfer->getInvalidValues());
    }

    public function testValidateQueryStringValuesIsSuccessfulWhenEveryCustomerGroupExists(): void
    {
        // Arrange
        $customerGroupTransfer = $this->tester->haveCustomerGroup([CustomerGroupTransfer::NAME => sprintf('Existing Group %s', uniqid())]);

        // Act
        $discountRuleValueValidationResponseTransfer = (new CustomerGroupSuggestDecisionRulePlugin())
            ->validateQueryStringValues([$customerGroupTransfer->getNameOrFail()]);

        // Assert
        $this->assertTrue($discountRuleValueValidationResponseTransfer->getIsSuccessful());
        $this->assertSame([], $discountRuleValueValidationResponseTransfer->getInvalidValues());
    }

    /**
     * The clause value is lowercased the way the discount query string parser stores it: `strtolower()` leaves "Ä" and "Ö" unchanged.
     *
     * @dataProvider provideNonAsciiCustomerGroupClauses
     */
    public function testANonAsciiCustomerGroupNameAcceptedOnSaveIsSatisfiedForAnAssignedCustomer(string $operator, string $queryStringValue): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $this->tester->haveCustomerGroup([CustomerGroupTransfer::NAME => static::OTHER_NON_ASCII_CUSTOMER_GROUP_NAME]);
        $this->tester->haveCustomerGroup([
            CustomerGroupTransfer::NAME => static::ASSIGNED_NON_ASCII_CUSTOMER_GROUP_NAME,
            CustomerGroupTransfer::CUSTOMER_ASSIGNMENT => [
                CustomerGroupToCustomerAssignmentTransfer::IDS_CUSTOMER_TO_ASSIGN => [$customerTransfer->getIdCustomerOrFail()],
            ],
        ]);
        $customerGroupSuggestDecisionRulePlugin = new CustomerGroupSuggestDecisionRulePlugin();
        $this->tester->setDependency(DiscountDependencyProvider::DECISION_RULE_PLUGINS, [$customerGroupSuggestDecisionRulePlugin]);

        $quoteTransfer = (new QuoteBuilder())->withItem()->build();
        $quoteTransfer->setCustomer($customerTransfer);

        // Act
        $validationErrors = $this->tester->getLocator()->discount()->facade()->validateQueryStringByType(
            MetaProviderFactory::TYPE_DECISION_RULE,
            sprintf('%s %s "%s"', static::FIELD_NAME, $operator, $queryStringValue),
        );
        $isSatisfied = $customerGroupSuggestDecisionRulePlugin->isSatisfiedBy(
            $quoteTransfer,
            $quoteTransfer->getItems()->offsetGet(0),
            $this->createClauseTransfer($operator, strtolower($queryStringValue)),
        );

        // Assert
        $this->assertSame([], $validationErrors);
        $this->assertTrue($isSatisfied);
    }

    /**
     * @return array<string, array{string, string}>
     */
    protected function provideNonAsciiCustomerGroupClauses(): array
    {
        return [
            'equal' => ['=', static::ASSIGNED_NON_ASCII_CUSTOMER_GROUP_NAME],
            'is in' => ['is in', sprintf('%s;%s', static::OTHER_NON_ASCII_CUSTOMER_GROUP_NAME, static::ASSIGNED_NON_ASCII_CUSTOMER_GROUP_NAME)],
        ];
    }

    protected function createClauseTransfer(string $operator, string $value): ClauseTransfer
    {
        return (new ClauseBuilder([
            ClauseTransfer::ACCEPTED_TYPES => [ComparatorOperators::TYPE_STRING, ComparatorOperators::TYPE_LIST],
            ClauseTransfer::FIELD => static::FIELD_NAME,
            ClauseTransfer::OPERATOR => $operator,
            ClauseTransfer::VALUE => $value,
        ]))->build();
    }
}
