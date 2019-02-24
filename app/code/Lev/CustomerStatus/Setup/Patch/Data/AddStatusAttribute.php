<?php

namespace Lev\CustomerStatus\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

/**
 * Class AddStatusAttribute
 *
 * @package Lev\CustomerStatus\Setup\Patch\Data
 */
class AddStatusAttribute implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * Attribute code for eav
     */
    const ATTRIBUTE_CODE = 'customer_status';

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * AddStatusAttribute constructor
     *
     * @param CustomerSetupFactory $customerSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup
    )
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Get array of patches that have to be executed prior to this.
     *
     * @return string[]
     */
    public static function getDependencies()
    {
        return [
            \Magento\Customer\Setup\Patch\Data\DefaultCustomerGroupsAndAttributes::class
        ];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Run code inside patch
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $customerSetup = $this->getCustomerSetup();
        $customerSetup->addAttribute(
            Customer::ENTITY,
            self::ATTRIBUTE_CODE,
            [
                'type' => 'varchar',
                'label' => 'Status',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'system' => false,
            ]
        );

        $customerStatusAttribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, self::ATTRIBUTE_CODE);
        $customerStatusAttribute->setData('used_in_forms', ['adminhtml_customer']);
        $customerStatusAttribute->save();

        return $this;
    }

    /**
     * @return CustomerSetup
     */
    private function getCustomerSetup()
    {
        return $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
    }

    /**
     * Rollback all changes, done by this patch
     *
     * @return void
     */
    public function revert()
    {
        $customerSetup = $this->getCustomerSetup();

        $customerSetup->removeAttribute(Customer::ENTITY, self::ATTRIBUTE_CODE);
    }
}