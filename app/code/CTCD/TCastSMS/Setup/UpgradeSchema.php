<?php
/**
 * Copyright © 2023 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_TCastSMS
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\TCastSMS\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use CTCD\TCastSMS\Api\Data\LogOtpInterface;

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

        if (version_compare($context->getVersion(), '100.0.1', '<')) {
            $this->upgradeTo10001($setup);
        }

        $setup->endSetup();
    }

    /**
     * Create table tcast_log
     *
     * @param $installer
     */
    private function upgradeTo10001($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(LogOtpInterface::TABLE_NAME))
            ->addColumn(
                LogOtpInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Entity ID'
            )->addColumn(
                LogOtpInterface::VERIFICATION_ID,
                Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Log ID'
            )->addColumn(
                LogOtpInterface::MESSAGE_ID,
                Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Mobile Number'
            )->addColumn(
                LogOtpInterface::OTP_CODE,
                Table::TYPE_TEXT,
                15,
                ['nullable' => true],
                'Mobile Number'
            )->addColumn(
                LogOtpInterface::MOBILE_NUMBER,
                Table::TYPE_TEXT,
                30,
                ['nullable' => true],
                'Mobile Number'
            )->addColumn(
                LogOtpInterface::DELIVERED,
                Table::TYPE_SMALLINT,
                1,
                ['nullable' => true, 'default' => 0],
                'Delivered'
            )->addColumn(
                LogOtpInterface::VERIFIED,
                Table::TYPE_SMALLINT,
                1,
                ['nullable' => true, 'default' => 0],
                'Verified'
            )->addColumn(
                LogOtpInterface::DELIVERY_REQUEST_RESPONSE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Delivery Request Response'
            )->addColumn(
                LogOtpInterface::DELIVERY_STATUS_RESPONSE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Delivery Response Response'
            )->addColumn(
                LogOtpInterface::CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Created Time'
            )->addColumn(
                LogOtpInterface::UPDATED_AT,
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Updated Time'
            )->setComment(
                'TCastSMS OTP Log Table'
            );

        $installer->getConnection()->createTable($table);
    }
}
