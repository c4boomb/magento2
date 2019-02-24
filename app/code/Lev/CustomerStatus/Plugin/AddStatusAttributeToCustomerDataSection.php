<?php

namespace Lev\CustomerStatus\Plugin;

use Magento\Customer\CustomerData\Customer;
use Magento\Customer\Helper\Session\CurrentCustomer;

/**
 * Class AddStatusAttributeToCustomerDataSection
 * @package Lev\CustomerStatus\Plugin
 */
class AddStatusAttributeToCustomerDataSection
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomerHelper;

    /**
     * AddStatusAttributeToCustomerDataSection constructor
     *
     * @param CurrentCustomer $currentCustomerHelper
     */
    public function __construct(CurrentCustomer $currentCustomerHelper)
    {
        $this->currentCustomerHelper = $currentCustomerHelper;
    }

    /**
     * Add customer_status attribute to Section Data
     *
     * @param Customer $subject
     * @param $result
     * @return mixed
     */
    public function afterGetSectionData(Customer $subject, $result)
    {
        if (!empty($result) && $this->currentCustomerHelper->getCustomerId()) {
            $result['customer_status'] = '';

            $customer = $this->currentCustomerHelper->getCustomer();
            $customerStatusAttributeData = $customer->getCustomAttribute('customer_status');

            if ($customerStatusAttributeData) {
                $result['customer_status'] = $customerStatusAttributeData->getValue();
            }
        }

        return $result;
    }
}