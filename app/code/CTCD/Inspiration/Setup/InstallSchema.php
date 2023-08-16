<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Inspiration
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Inspiration\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
use CTCD\Inspiration\Api\Data\InspirationInterface;
use CTCD\Inspiration\Helper\Data as DataHelper;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return SchemaSetupInterface
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /* Add Inspiration Table */
        $this->generateInspirationTable($setup);

        $setup->endSetup();
    }

    /**
     * Generate Template Class Table
     *
     * @param SchemaSetupInterface $setup
     * @return SchemaSetupInterface
     */
    protected function generateInspirationTable(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'ctcd_inspiration'
         */
        if ($setup->tableExists(DataHelper::INSPIRATION_TABLE)) {
            $setup->getConnection()->dropTable(DataHelper::INSPIRATION_TABLE);
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable(DataHelper::INSPIRATION_TABLE)
        )->addColumn(
            InspirationInterface::ENTITY_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            InspirationInterface::TITLE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Title'
        )->addColumn(
            InspirationInterface::CONTENT,
            Table::TYPE_TEXT,
            Table::MAX_TEXT_SIZE,
            ['nullable' => true],
            'Content'
        )->addColumn(
            InspirationInterface::URL_KEY,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Url Key'
        )->addColumn(
            InspirationInterface::SORT_ORDER,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => 0],
            'Sort Order'
        )->addColumn(
            InspirationInterface::HISTORY,
            Table::TYPE_TEXT,
            Table::MAX_TEXT_SIZE,
            ['nullable' => true],
            'History'
        )->addColumn(
            InspirationInterface::INCLUDE_MENU,
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => 0],
            'Include In Menu'
        )->addColumn(
            InspirationInterface::ACTIVE,
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => 0],
            'Is Active'
        )->addColumn(
            InspirationInterface::CREATED_AT,
            Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'Created Time'
        )->addColumn(
            InspirationInterface::UPDATED_AT,
            Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'Updated Time'
        )->addIndex(
            $setup->getIdxName(
                $setup->getTable(DataHelper::INSPIRATION_TABLE),
                [InspirationInterface::TITLE, InspirationInterface::URL_KEY],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            [InspirationInterface::TITLE, InspirationInterface::URL_KEY],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT ]
        )->setComment(
            'CTCD Inspiration Table'
        );

        $setup->getConnection()->createTable($table);
    }
}
