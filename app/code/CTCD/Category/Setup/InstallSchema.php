<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Category
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Category\Setup;

use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\Db\Ddl\Table;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\InstallSchemaInterface;
use \CTCD\Category\Api\Data\CategoryImportInterface;

/**
 * {@inheritdoc}
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $installer = $setup;
        $installer->startSetup();

        $this->createCategoryImportTable($installer);

        $installer->endSetup();
    }

    /**
     * Create table CategoryImport
     *
     * @param $installer
     */
    private function createCategoryImportTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(CategoryImportInterface::TABLE_NAME))
            ->addColumn(
                CategoryImportInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Entity ID'
            )->addColumn(
                CategoryImportInterface::FILE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'CSV file'
            )->addColumn(
                CategoryImportInterface::STATUS,
                Table::TYPE_INTEGER,
                2,
                ['nullable' => true],
                'status'
            )->addColumn(
                CategoryImportInterface::ADMIN_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Admin ID'
            )->addColumn(
                CategoryImportInterface::CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Created Time'
            )->addColumn(
                CategoryImportInterface::UPDATED_AT,
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Updated Time'
            )->setComment(
                'CategoryImport Table'
            );

        $installer->getConnection()->createTable($table);
    }
}
