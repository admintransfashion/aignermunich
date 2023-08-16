<?php
/**
 * @category Trans
 * @package  Trans_CatalogMultisource
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CatalogMultisource\Setup;

use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\Db\Ddl\Table;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\InstallSchemaInterface;
use \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface as SourceItemUpdate;

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

        $this->createSourceItemUpdateHistory($installer);
        
        $installer->endSetup();
    }

    /**
     * Create table
     *
     * @param $installer
     */
    private function createSourceItemUpdateHistory($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(SourceItemUpdate::TABLE_NAME))
            ->addColumn(
                SourceItemUpdate::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )->addColumn(
                SourceItemUpdate::LAST_EXECUTED,
                Table::TYPE_DATE,
                null,
                ['nullable' => true],
                'last executed'
            )->addColumn(
                SourceItemUpdate::IS_PROCESSING,
                Table::TYPE_INTEGER,
                2,
                ['nullable' => false, 'default' => 0],
                'is processing'
            )->addColumn(
                SourceItemUpdate::FLAG,
                Table::TYPE_TEXT,
                20,
                ['nullable' => false, 'default' => 'hold'],
                'process flag success/fail/hold/cancelled'
            )->addColumn(
                SourceItemUpdate::PAYLOAD,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'payload'
            )->addColumn(
                SourceItemUpdate::SKU,
                Table::TYPE_TEXT,
                20,
                ['nullable' => true],
                'product SKU'
            )->addColumn(
                SourceItemUpdate::SOURCE_CODE,
                Table::TYPE_TEXT,
                25,
                ['nullable' => true],
                'source code/store code/warehouse code'
            )->addColumn(
                SourceItemUpdate::QTY,
                Table::TYPE_INTEGER,
                10,
                ['nullable' => true, 'default' => 0],
                'product qty'
            )->addColumn(
                SourceItemUpdate::SOURCE_ITEM_STATUS,
                Table::TYPE_INTEGER,
                2,
                ['nullable' => true, 'default' => 1],
                'source item status'
            )->addColumn(
                SourceItemUpdate::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )->addColumn(
                SourceItemUpdate::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('Table');

        $installer->getConnection()->createTable($table);
    }
}