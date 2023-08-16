<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Newsletter
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Newsletter\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use Trans\Newsletter\Api\Data\NewsletterAdditionalInterface;

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
            $table = $setup->getTable(NewsletterAdditionalInterface::TABLE_NAME);
            if ($setup->getConnection()->isTableExists($table) === true) {
                if ($setup->getConnection()->tableColumnExists($table, NewsletterAdditionalInterface::SUBSCRIBE_CATEGORY) === false) {
                    $setup->getConnection()->addColumn(
                        NewsletterAdditionalInterface::TABLE_NAME,
                        NewsletterAdditionalInterface::SUBSCRIBE_CATEGORY,
                        ['type' => Table::TYPE_TEXT, 'length' => 15, 'nullable' => true, 'comment' => 'Subscribe Category', 'after' => NewsletterAdditionalInterface::SUBSCRIBE_WOMEN]
                    );
                }
            }
        }

        $setup->endSetup();
    }
}
