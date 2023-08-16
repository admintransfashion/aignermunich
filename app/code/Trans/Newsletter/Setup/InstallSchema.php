<?php
/**
 * @category Trans
 * @package  Trans_Newsletter
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Newsletter\Setup;

use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\Db\Ddl\Table;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\InstallSchemaInterface;
use \Trans\Newsletter\Api\Data\NewsletterAdditionalInterface;

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

        $this->createNewsletterAdditionalTable($installer);
        
        $installer->endSetup();
    }

    /**
     * Create table reservation
     *
     * @param $installer
     */
    private function createNewsletterAdditionalTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(NewsletterAdditionalInterface::TABLE_NAME))
            ->addColumn(
                NewsletterAdditionalInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )->addColumn(
                NewsletterAdditionalInterface::SUBSCRIBER_ID,
                Table::TYPE_INTEGER,
                2,
                ['nullable' => false],
                'subscriber id'
            )->addColumn(
                NewsletterAdditionalInterface::SUBSCRIBE_MEN,
                Table::TYPE_BOOLEAN,
                1,
                ['nullable' => true, 'default' => 0],
                'subscribe for men category'
            )->addColumn(
                NewsletterAdditionalInterface::SUBSCRIBE_WOMEN,
                Table::TYPE_BOOLEAN,
                1,
                ['nullable' => true, 'default' => 0],
                'subscribe for women category'
            )->addColumn(
                NewsletterAdditionalInterface::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )->addColumn(
                NewsletterAdditionalInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('Reservation Table');

        $installer->getConnection()->createTable($table);
    }
}
