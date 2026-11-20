<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CustomerGroupDiscountConnector\Business;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\ClauseBuilder;
use Generated\Shared\DataBuilder\QuoteBuilder;
use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\CustomerGroupCollectionTransfer;
use Generated\Shared\Transfer\CustomerGroupToCustomerAssignmentTransfer;
use Generated\Shared\Transfer\CustomerGroupTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\CustomerGroup\CustomerGroupDependencyProvider;
use Spryker\Zed\CustomerGroupExtension\Dependency\Plugin\CustomerGroupProviderPluginInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group CustomerGroupDiscountConnector
 * @group Business
 * @group Facade
 * @group CustomerGroupDiscountConnectorFacadeTest
 * Add your own group annotations below this line
 */
class CustomerGroupDiscountConnectorFacadeTest extends Unit
{
    /**
     * @var string
     */
    protected const CUSTOMER_GROUP_NAME = 'Test Customer Group';

    protected const string SECOND_CUSTOMER_GROUP_NAME = 'Second Customer Group';

    protected const string OTHER_CUSTOMER_GROUP_NAME = 'Other Customer Group';

    /**
     * @var string
     */
    protected const FIELD_NAME = 'customer-group';

    /**
     * @var array<string>
     */
    protected const ACCEPTED_TYPES = [
        'string',
        'list',
    ];

    /**
     * @var \SprykerTest\Zed\CustomerGroupDiscountConnector\CustomerGroupDiscountConnectorBusinessTester
     */
    protected $tester;

    /**
     * @dataProvider isCustomerGroupSatisfiedByShouldReturnCorrectResultAccordingToClauseDataProvider
     *
     * @param string $operator
     * @param string $value
     * @param bool $expectedResult
     *
     * @return void
     */
    public function testIsCustomerGroupSatisfiedByShouldReturnCorrectResultAccordingToClause(
        string $operator,
        string $value,
        bool $expectedResult
    ): void {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $this->tester->haveCustomerGroup([
            CustomerGroupTransfer::NAME => static::CUSTOMER_GROUP_NAME,
            CustomerGroupTransfer::CUSTOMER_ASSIGNMENT => [
                CustomerGroupToCustomerAssignmentTransfer::IDS_CUSTOMER_TO_ASSIGN => [$customerTransfer->getIdCustomerOrFail()],
            ],
        ]);

        $quoteTransfer = (new QuoteBuilder())
            ->withItem()
            ->build();
        $quoteTransfer->setCustomer($customerTransfer);

        $clauseTransfer = (new ClauseBuilder([
            ClauseTransfer::ACCEPTED_TYPES => static::ACCEPTED_TYPES,
            ClauseTransfer::FIELD => static::FIELD_NAME,
            ClauseTransfer::OPERATOR => $operator,
            ClauseTransfer::VALUE => $value,
        ]))->build();

        // Act
        $result = $this->tester->getFacade()->isCustomerGroupSatisfiedBy(
            $quoteTransfer,
            $quoteTransfer->getItems()->offsetGet(0),
            $clauseTransfer,
        );

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider isCustomerGroupSatisfiedByShouldReturnCorrectResultAccordingToClauseDataProvider
     */
    public function testIsCustomerGroupSatisfiedByMatchesTheClauseAgainstAGroupProvidedByACustomerGroupProviderPlugin(
        string $operator,
        string $value,
        bool $expectedResult
    ): void {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $customerGroupTransfer = $this->tester->haveCustomerGroup([CustomerGroupTransfer::NAME => static::CUSTOMER_GROUP_NAME]);

        $customerGroupProviderPluginMock = $this->createMock(CustomerGroupProviderPluginInterface::class);
        $customerGroupProviderPluginMock->method('provideCustomerGroups')
            ->willReturn((new CustomerGroupCollectionTransfer())->addGroup($customerGroupTransfer));
        $this->tester->setDependency(
            CustomerGroupDependencyProvider::PLUGINS_CUSTOMER_GROUP_PROVIDER,
            [$customerGroupProviderPluginMock],
        );

        $quoteTransfer = (new QuoteBuilder())
            ->withItem()
            ->build();
        $quoteTransfer->setCustomer($customerTransfer);

        // Act
        $result = $this->tester->getFacade()->isCustomerGroupSatisfiedBy(
            $quoteTransfer,
            $quoteTransfer->getItems()->offsetGet(0),
            $this->createClauseTransfer($operator, $value),
        );

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    /**
     * A customer in several groups must not match a negated clause on one of them only because another group differs.
     *
     * @dataProvider isCustomerGroupSatisfiedByCustomerInSeveralGroupsDataProvider
     */
    public function testIsCustomerGroupSatisfiedByAppliesTheNegatedOperatorToAllGroupsWhenEnabled(
        string $operator,
        string $value,
        bool $expectedResult
    ): void {
        // Arrange
        $this->tester->mockConfigMethod('isNegatedOperatorAppliedToAllCustomerGroups', true);
        $quoteTransfer = $this->createQuoteWithCustomerInSeveralGroups();

        // Act
        $result = $this->tester->getFacade()->isCustomerGroupSatisfiedBy(
            $quoteTransfer,
            $quoteTransfer->getItems()->offsetGet(0),
            $this->createClauseTransfer($operator, $value),
        );

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider isCustomerGroupSatisfiedByCustomerInSeveralGroupsWithNegatedOperatorPerGroupDataProvider
     */
    public function testIsCustomerGroupSatisfiedBySatisfiesTheClauseByAnyGroupWhenTheNegatedOperatorIsNotAppliedToAllGroups(
        string $operator,
        string $value,
        bool $expectedResult
    ): void {
        // Arrange
        $this->tester->mockConfigMethod('isNegatedOperatorAppliedToAllCustomerGroups', false);
        $quoteTransfer = $this->createQuoteWithCustomerInSeveralGroups();

        // Act
        $result = $this->tester->getFacade()->isCustomerGroupSatisfiedBy(
            $quoteTransfer,
            $quoteTransfer->getItems()->offsetGet(0),
            $this->createClauseTransfer($operator, $value),
        );

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider isCustomerGroupSatisfiedByCustomerWithoutGroupsDataProvider
     */
    public function testIsCustomerGroupSatisfiedByMatchesNoClauseForACustomerWithoutGroups(
        string $operator,
        string $value,
        bool $expectedResult
    ): void {
        // Arrange
        $quoteTransfer = (new QuoteBuilder())
            ->withItem()
            ->build();
        $quoteTransfer->setCustomer($this->tester->haveCustomer());

        // Act
        $result = $this->tester->getFacade()->isCustomerGroupSatisfiedBy(
            $quoteTransfer,
            $quoteTransfer->getItems()->offsetGet(0),
            $this->createClauseTransfer($operator, $value),
        );

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    public function testIsCustomerGroupSatisfiedByReturnsFalseForAQuoteWithoutCustomer(): void
    {
        // Arrange
        $quoteTransfer = (new QuoteBuilder())
            ->withItem()
            ->build();

        // Act
        $result = $this->tester->getFacade()->isCustomerGroupSatisfiedBy(
            $quoteTransfer,
            $quoteTransfer->getItems()->offsetGet(0),
            $this->createClauseTransfer('=', static::CUSTOMER_GROUP_NAME),
        );

        // Assert
        $this->assertFalse($result);
    }

    protected function createQuoteWithCustomerInSeveralGroups(): QuoteTransfer
    {
        $customerTransfer = $this->tester->haveCustomer();
        $this->tester->haveCustomerGroup([
            CustomerGroupTransfer::NAME => static::CUSTOMER_GROUP_NAME,
            CustomerGroupTransfer::CUSTOMER_ASSIGNMENT => [
                CustomerGroupToCustomerAssignmentTransfer::IDS_CUSTOMER_TO_ASSIGN => [$customerTransfer->getIdCustomerOrFail()],
            ],
        ]);
        $this->tester->haveCustomerGroup([
            CustomerGroupTransfer::NAME => static::SECOND_CUSTOMER_GROUP_NAME,
            CustomerGroupTransfer::CUSTOMER_ASSIGNMENT => [
                CustomerGroupToCustomerAssignmentTransfer::IDS_CUSTOMER_TO_ASSIGN => [$customerTransfer->getIdCustomerOrFail()],
            ],
        ]);

        $quoteTransfer = (new QuoteBuilder())
            ->withItem()
            ->build();
        $quoteTransfer->setCustomer($customerTransfer);

        return $quoteTransfer;
    }

    protected function createClauseTransfer(string $operator, string $value): ClauseTransfer
    {
        return (new ClauseBuilder([
            ClauseTransfer::ACCEPTED_TYPES => static::ACCEPTED_TYPES,
            ClauseTransfer::FIELD => static::FIELD_NAME,
            ClauseTransfer::OPERATOR => $operator,
            ClauseTransfer::VALUE => $value,
        ]))->build();
    }

    /**
     * @return array<string, array<string|bool>>
     */
    public function isCustomerGroupSatisfiedByShouldReturnCorrectResultAccordingToClauseDataProvider(): array
    {
        return [
            'equal to the group' => ['=', static::CUSTOMER_GROUP_NAME, true],
            'equal to the group, lower case' => ['=', strtolower(static::CUSTOMER_GROUP_NAME), true],
            'equal to the group, upper case' => ['=', strtoupper(static::CUSTOMER_GROUP_NAME), true],
            'not equal to the group' => ['!=', 'Test', true],
            'not equal to the group, same name' => ['!=', static::CUSTOMER_GROUP_NAME, false],
            'in a list with the customer group' => ['is in', static::OTHER_CUSTOMER_GROUP_NAME . ';' . static::CUSTOMER_GROUP_NAME, true],
            'not in a list with the customer group' => ['is not in', static::OTHER_CUSTOMER_GROUP_NAME . ';' . static::CUSTOMER_GROUP_NAME, false],
            'not in a list without the customer group' => ['is not in', static::OTHER_CUSTOMER_GROUP_NAME . ';' . static::SECOND_CUSTOMER_GROUP_NAME, true],
            'contains the first word' => ['CONTAINS', 'Test', true],
            'contains the middle word' => ['CONTAINS', 'Customer', true],
            'contains the last word' => ['CONTAINS', 'Group', true],
            'contains the beginning' => ['CONTAINS', 'Test Customer', true],
            'contains the end' => ['CONTAINS', 'Customer Group', true],
            'contains no part' => ['CONTAINS', 'Test Group', false],
            'does not contain a part' => ['DOES NOT CONTAIN', 'Test Group', true],
            'does not contain the beginning' => ['DOES NOT CONTAIN', 'Test Customer', false],
        ];
    }

    /**
     * @return array<string, array<string|bool>>
     */
    public function isCustomerGroupSatisfiedByCustomerInSeveralGroupsDataProvider(): array
    {
        return [
            'equal to one of the groups' => ['=', static::CUSTOMER_GROUP_NAME, true],
            'equal to no group' => ['=', static::OTHER_CUSTOMER_GROUP_NAME, false],
            'not equal to one of the groups' => ['!=', static::CUSTOMER_GROUP_NAME, false],
            'not equal to one of the groups, other case' => ['!=', strtoupper(static::SECOND_CUSTOMER_GROUP_NAME), false],
            'not equal to any group' => ['!=', static::OTHER_CUSTOMER_GROUP_NAME, true],
            'in a list with one of the groups' => ['is in', static::OTHER_CUSTOMER_GROUP_NAME . ';' . static::CUSTOMER_GROUP_NAME, true],
            'not in a list with one of the groups' => ['is not in', static::OTHER_CUSTOMER_GROUP_NAME . ';' . static::CUSTOMER_GROUP_NAME, false],
            'not in a list without any group' => ['is not in', static::OTHER_CUSTOMER_GROUP_NAME, true],
            'contains a part of one of the groups' => ['CONTAINS', 'Second', true],
            'does not contain a part of one of the groups' => ['DOES NOT CONTAIN', 'Second', false],
            'does not contain a part of any group' => ['DOES NOT CONTAIN', 'Other', true],
        ];
    }

    /**
     * @return array<string, array<string|bool>>
     */
    public function isCustomerGroupSatisfiedByCustomerInSeveralGroupsWithNegatedOperatorPerGroupDataProvider(): array
    {
        return [
            'equal to one of the groups' => ['=', static::CUSTOMER_GROUP_NAME, true],
            'equal to no group' => ['=', static::OTHER_CUSTOMER_GROUP_NAME, false],
            'not equal to one of the groups' => ['!=', static::CUSTOMER_GROUP_NAME, true],
            'not equal to any group' => ['!=', static::OTHER_CUSTOMER_GROUP_NAME, true],
            'not in a list with one of the groups' => ['is not in', static::OTHER_CUSTOMER_GROUP_NAME . ';' . static::CUSTOMER_GROUP_NAME, true],
            'not in a list with all groups' => ['is not in', static::CUSTOMER_GROUP_NAME . ';' . static::SECOND_CUSTOMER_GROUP_NAME, false],
            'does not contain a part of one of the groups' => ['DOES NOT CONTAIN', 'Second', true],
            'does not contain a part of all groups' => ['DOES NOT CONTAIN', 'Customer Group', false],
        ];
    }

    /**
     * @return array<string, array<string|bool>>
     */
    public function isCustomerGroupSatisfiedByCustomerWithoutGroupsDataProvider(): array
    {
        return [
            'equal' => ['=', static::CUSTOMER_GROUP_NAME, false],
            'in a list' => ['is in', static::CUSTOMER_GROUP_NAME, false],
            'contains' => ['CONTAINS', 'Test', false],
            'not equal' => ['!=', static::CUSTOMER_GROUP_NAME, false],
            'not in a list' => ['is not in', static::CUSTOMER_GROUP_NAME, false],
            'does not contain' => ['DOES NOT CONTAIN', 'Test', false],
        ];
    }
}
