<?php

/**
 * Copyright © 2023 Trans Fashion Indonesia. All rights reserved.
 * https://transfashionindonesia.com
 *
 * @category Trans
 * @package  Trans_Customer
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edisuryadi2004@gmail.com>
 */

namespace Trans\Customer\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
	 * @var \Magento\Customer\Setup\CustomerSetupFactory
	 */
	protected $customerSetupFactory;
    
    /**
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->upgradeTo104($setup);
        }

        $setup->endSetup();
    }

    /**
     * Add pub/media/ctcd/core directory
     *
     * @param ModuleDataSetupInterface $setup
     * @return ModuleDataSetupInterface
     */
    protected function upgradeTo104(ModuleDataSetupInterface $setup)
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerSetup->updateAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'telephone',                    
            [
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => true
            ]
        );
    }
}
