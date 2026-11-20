<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerGroupDiscountConnector\Business\Validator;

use Generated\Shared\Transfer\CustomerGroupConditionsTransfer;
use Generated\Shared\Transfer\CustomerGroupCriteriaTransfer;
use Generated\Shared\Transfer\DiscountRuleValueValidationResponseTransfer;
use Spryker\Zed\CustomerGroupDiscountConnector\Dependency\Facade\CustomerGroupDiscountConnectorToCustomerGroupFacadeInterface;

class CustomerGroupNameValidator implements CustomerGroupNameValidatorInterface
{
    public function __construct(
        protected CustomerGroupDiscountConnectorToCustomerGroupFacadeInterface $customerGroupFacade
    ) {
    }

    /**
     * @param list<string> $customerGroupNames
     */
    public function validateCustomerGroupNames(array $customerGroupNames): DiscountRuleValueValidationResponseTransfer
    {
        $unknownCustomerGroupNames = $this->getUnknownCustomerGroupNames($customerGroupNames);

        return (new DiscountRuleValueValidationResponseTransfer())
            ->setIsSuccessful($unknownCustomerGroupNames === [])
            ->setInvalidValues($unknownCustomerGroupNames);
    }

    /**
     * @param list<string> $customerGroupNames
     *
     * @return list<string>
     */
    protected function getUnknownCustomerGroupNames(array $customerGroupNames): array
    {
        if ($customerGroupNames === []) {
            return [];
        }

        $existingLowercasedNames = $this->getExistingLowercasedNames($customerGroupNames);

        return array_values(array_filter(
            $customerGroupNames,
            fn (string $customerGroupName): bool => !isset($existingLowercasedNames[mb_strtolower($customerGroupName)]),
        ));
    }

    /**
     * @param list<string> $customerGroupNames
     *
     * @return array<string, true>
     */
    protected function getExistingLowercasedNames(array $customerGroupNames): array
    {
        $customerGroupCollectionTransfer = $this->customerGroupFacade->getCustomerGroupCollection(
            (new CustomerGroupCriteriaTransfer())->setCustomerGroupConditions(
                (new CustomerGroupConditionsTransfer())->setNames($customerGroupNames),
            ),
        );

        $existingLowercasedNames = [];

        foreach ($customerGroupCollectionTransfer->getGroups() as $customerGroupTransfer) {
            $existingLowercasedNames[mb_strtolower((string)$customerGroupTransfer->getName())] = true;
        }

        return $existingLowercasedNames;
    }
}
