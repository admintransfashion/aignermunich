<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Core
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Core\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @param DirectoryList $directoryList
     */
    public function __construct(
        DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        if (version_compare($context->getVersion(), '100.0.2', '<')) {
            $this->upgradeTo10002($setup);
        }

        $setup->endSetup();
    }

    /**
     * Add pub/media/ctcd/core directory
     *
     * @param ModuleDataSetupInterface $setup
     * @return ModuleDataSetupInterface
     */
    protected function upgradeTo10002(ModuleDataSetupInterface $setup)
    {
        $path = $this->directoryList->getRoot().'/pub/media/ctcd/';

        if (!file_exists($path)) {
            mkdir($path, 0775);
        }

        $path = $this->directoryList->getRoot().'/pub/media/ctcd/core/';

        if (!file_exists($path)) {
            mkdir($path, 0775);
        }
    }
}
