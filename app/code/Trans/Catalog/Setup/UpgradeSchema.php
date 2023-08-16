<?php
/**
 * @category Trans
 * @package  Trans_Catalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Catalog\Setup;
 
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Trans\Catalog\Api\Data\SeasonInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @SuppressWarnings(PHPMD)
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * update schema
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->installTableMasterSeason($setup);
        }

        $setup->endSetup();
    }

    /**
     * install table user store
     * 
     * @param SchemaSetupInterface $setup
     */
    protected function installTableMasterSeason($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(SeasonInterface::TABLE_NAME))
            ->addColumn(
                SeasonInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )
            ->addColumn(
                SeasonInterface::CODE,
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'season code'
            )
            ->addColumn(
                SeasonInterface::LABEL,
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'season label'
            )
            ->addColumn(
                SeasonInterface::DESC,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'season label'
            )
            ->addColumn(
                SeasonInterface::FLAG,
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false, 'default' => 1],
                'flag'
            )
            ->addColumn(
                SeasonInterface::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )->addColumn(
                SeasonInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('user admin store');

        $setup->getConnection()->createTable($table);
    }
}
