<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Category
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Category\Setup;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
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
    public function install (ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $path = $this->directoryList->getRoot().'/pub/media/ctcd/';

        if (!file_exists($path)) {
            mkdir($path, 0775);
        }

        $path = $this->directoryList->getRoot().'/pub/media/ctcd/category_import/';

        if (!file_exists($path)) {
            mkdir($path, 0775);
        }

        $path = $this->directoryList->getRoot().'/pub/media/ctcd/category_import/tmp/';

        if (!file_exists($path)) {
            mkdir($path, 0775);
        }

        $path = $this->directoryList->getRoot().'/pub/media/ctcd/category_import/img/';

        if (!file_exists($path)) {
            mkdir($path, 0775);
        }

        $setup->endSetup();
    }
}
