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

use CTCD\Category\Api\Data\CategoryImportInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use CTCD\Category\Api\Data\CategoryImportMapInterface;
use CTCD\Category\Api\Data\CategoryImportDataInterface;
use CTCD\Category\Api\Data\CategoryImportWaitInterface;

/**
 * @SuppressWarnings(PHPMD)
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * upgrade table
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->createCategoryImportDataTable($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $table = $setup->getTable(CategoryImportDataInterface::TABLE_NAME);
            if ($setup->getConnection()->isTableExists($table) === true) {
                if ($setup->getConnection()->tableColumnExists($table, CategoryImportDataInterface::SEQUENCE) === false) {
                    $setup->getConnection()->addColumn(
                        CategoryImportDataInterface::TABLE_NAME,
                        CategoryImportDataInterface::SEQUENCE,
                        ['type' => Table::TYPE_INTEGER, 'length' => null, 'nullable' => true, 'comment' => 'Data Sequence', 'after' => CategoryImportDataInterface::JSON_DATA]
                    );
                }
            }
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $table = $setup->getTable(CategoryImportDataInterface::TABLE_NAME);
            if ($setup->getConnection()->isTableExists($table) === true) {
                if ($setup->getConnection()->tableColumnExists($table, CategoryImportDataInterface::COLUMN) === false) {
                    $setup->getConnection()->addColumn(
                        CategoryImportDataInterface::TABLE_NAME,
                        CategoryImportDataInterface::COLUMN,
                        ['type' => Table::TYPE_TEXT, 'length' => null, 'nullable' => true, 'comment' => 'Column', 'after' => CategoryImportDataInterface::SEQUENCE]
                    );
                }
            }
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->createCategoryImportMapTable($setup);
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $this->createWaitingParentDataTable($setup);
        }

        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $table = $setup->getTable(CategoryImportWaitInterface::TABLE_NAME);
            if ($setup->getConnection()->isTableExists($table) === true) {
                if ($setup->getConnection()->tableColumnExists($table, CategoryImportWaitInterface::CODE) === false) {
                    $setup->getConnection()->addColumn(
                        CategoryImportWaitInterface::TABLE_NAME,
                        CategoryImportWaitInterface::CODE,
                        ['type' => Table::TYPE_TEXT, 'length' => 60, 'nullable' => true, 'comment' => 'Data Code', 'after' => CategoryImportWaitInterface::JSON_KEYS]
                    );
                }
            }
        }

        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $table = $setup->getTable(CategoryImportMapInterface::TABLE_NAME);
            if ($setup->getConnection()->isTableExists($table) === true) {
                if ($setup->getConnection()->tableColumnExists($table, CategoryImportMapInterface::OFFLINE_ID) === false) {
                    $setup->getConnection()->addColumn(
                        CategoryImportMapInterface::TABLE_NAME,
                        CategoryImportMapInterface::OFFLINE_ID,
                        ['type' => Table::TYPE_TEXT, 'length' => null, 'nullable' => true, 'comment' => 'Category Offline ID', 'after' => CategoryImportMapInterface::CATEGORY_ID]
                    );
                }
            }
        }

        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $table = $setup->getTable(CategoryImportInterface::TABLE_NAME);
            if ($setup->getConnection()->isTableExists($table) === true) {
                if ($setup->getConnection()->tableColumnExists($table, CategoryImportInterface::ADMIN_ID) === false) {
                    $setup->getConnection()->addColumn(
                        CategoryImportInterface::TABLE_NAME,
                        CategoryImportInterface::ADMIN_ID,
                        ['type' => Table::TYPE_INTEGER, 'length' => null, 'nullable' => true, 'comment' => 'Admin ID', 'after' => CategoryImportInterface::STATUS]
                    );
                }
            }
        }

        $setup->endSetup();
    }

    /**
     * Create table CategoryImportData
     *
     * @param $installer
     */
    private function createCategoryImportDataTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(CategoryImportDataInterface::TABLE_NAME))
            ->addColumn(
                CategoryImportDataInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Entity ID'
            )->addColumn(
                CategoryImportDataInterface::IMPORT_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Import ID'
            )->addColumn(
                CategoryImportDataInterface::JSON_DATA,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'JSON data'
            )->addColumn(
                CategoryImportDataInterface::STATUS,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Status'
            )->addColumn(
                CategoryImportDataInterface::CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Created Time'
            )->addColumn(
                CategoryImportDataInterface::UPDATED_AT,
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Updated Time'
            )->setComment(
                'CategoryImportData Table'
            );

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table CategoryImportMap
     *
     * @param $installer
     */
    private function createCategoryImportMapTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(CategoryImportMapInterface::TABLE_NAME))
            ->addColumn(
                CategoryImportMapInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )->addColumn(
                CategoryImportMapInterface::CATEGORY_CODE,
                Table::TYPE_TEXT,
                60,
                ['nullable' => false],
                'category code'
            )->addColumn(
                CategoryImportMapInterface::CATEGORY_ID,
                Table::TYPE_INTEGER,
                5,
                ['nullable' => false],
                'magento category id'
            )->addColumn(
                CategoryImportMapInterface::CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Created Time'
            )->addColumn(
                CategoryImportMapInterface::UPDATED_AT,
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Updated Time'
            )->setComment(
                'CategoryImportMap Table'
            );

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table category_import_waiting
     *
     * @param $installer
     */
    private function createWaitingParentDataTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(CategoryImportWaitInterface::TABLE_NAME))
            ->addColumn(
                CategoryImportWaitInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )->addColumn(
                CategoryImportWaitInterface::JSON_DATA,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'JSON data of category'
            )->addColumn(
                CategoryImportWaitInterface::JSON_KEYS,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'JSON data of array keys'
            )->addColumn(
                CategoryImportWaitInterface::CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Created Time'
            )->addColumn(
                CategoryImportWaitInterface::UPDATED_AT,
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Updated Time'
            )->setComment(
                'Category import data waiting for parent data'
            );

        $installer->getConnection()->createTable($table);
    }
}
