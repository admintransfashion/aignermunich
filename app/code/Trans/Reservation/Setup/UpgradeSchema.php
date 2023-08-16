<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Reservation
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Reservation\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use Trans\Reservation\Api\Data\SourceAttributeInterface;
use Trans\Reservation\Api\Data\ReservationInterface;
use Trans\Reservation\Api\Data\ReservationItemInterface;
use Trans\Reservation\Api\Data\ReservationAttributeInterface;
use Trans\Reservation\Api\Data\ReservationConfigInterface;
use Trans\Reservation\Api\Data\UserStoreInterface;

/**
 * @SuppressWarnings(PHPMD)
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationInterface::TABLE_NAME),
                ReservationInterface::CUSTOMER_ID,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'LENGTH' => 3,
                    'comment' => 'Customer ID'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationInterface::TABLE_NAME),
                ReservationInterface::CUSTOMER_ID,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'LENGTH' => 3,
                    'comment' => 'Customer ID'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationInterface::TABLE_NAME),
                ReservationInterface::RESERVATION_NUMBER,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Reservation Number'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationInterface::TABLE_NAME),
                ReservationInterface::TIME_START,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'comment' => 'Reservation Time Start'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationInterface::TABLE_NAME),
                ReservationInterface::TIME_END,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'comment' => 'Reservation Time End'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::PRODUCT_ID,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'Product id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $this->installTableReservationAttribute($setup);
        }

        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $this->installTableSourceAttribute($setup);
        }

        if (version_compare($context->getVersion(), '1.0.9', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationInterface::TABLE_NAME),
                ReservationInterface::RESERVATION_DATE_SUBMIT,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'comment' => 'Reservation date submit'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationInterface::TABLE_NAME),
                ReservationInterface::RESERVATION_DATE_CONFIRM,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'comment' => 'Reservation date confirmed by admin'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationInterface::TABLE_NAME),
                ReservationInterface::RESERVATION_DATE_END,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'nullable' => true,
                    'comment' => 'Reservation date end'
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable(ReservationInterface::TABLE_NAME),
                ReservationInterface::TIME_END,
                ReservationInterface::TIME_END,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 6,
                    'nullable' => true,
                    'comment' => 'Reservation time end'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $setup->getConnection()->changeColumn(
                $setup->getTable(ReservationInterface::TABLE_NAME),
                ReservationInterface::RESERVATION_DATE_END,
                ReservationInterface::RESERVATION_DATE_END,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'comment' => 'Reservation time end'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.2', '<')) {
            $this->installTableReservationProductConfig($setup);
        }

        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationConfigInterface::TABLE_NAME),
                ReservationConfigInterface::FILTER,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 30,
                    'nullable' => false,
                    'comment' => 'product buffer filter by'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.4', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::END_TIME,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 6,
                    'nullable' => true,
                    'comment' => 'Reservation time end'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::END_DATE,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'comment' => 'Reservation date end'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.5', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationConfigInterface::TABLE_NAME),
                ReservationConfigInterface::STORE_CODE,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 30,
                    'nullable' => false,
                    'comment' => 'store code'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.6', '<')) {
            $setup->getConnection()->changeColumn(
                $setup->getTable(ReservationConfigInterface::TABLE_NAME),
                ReservationConfigInterface::PRODUCT_SKU,
                ReservationConfigInterface::PRODUCT_SKU,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '2M',
                    'nullable' => true,
                    'comment' => 'Reservation time end'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.7', '<')) {
            $setup->getConnection()->changeColumn(
                $setup->getTable(ReservationConfigInterface::TABLE_NAME),
                ReservationConfigInterface::STORE_CODE,
                ReservationConfigInterface::STORE_CODE,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '2M',
                    'nullable' => true,
                    'comment' => 'store code'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.8', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::BUFFER,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 5,
                    'nullable' => true,
                    'comment' => 'Product buffer'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::MAXQTY,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 5,
                    'nullable' => true,
                    'comment' => 'Reservation max qty'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.9', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::SOURCE_CODE,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 50,
                    'nullable' => true,
                    'comment' => 'source code'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::START_TIME,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 6,
                    'nullable' => true,
                    'comment' => 'Reservation time start'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::START_DATE,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'nullable' => true,
                    'comment' => 'Reservation date start'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            $setup->getConnection()->changeColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::END_DATE,
                ReservationItemInterface::END_DATE,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'nullable' => true,
                    'comment' => 'Reservation date end'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::REFERENCE_NUMBER,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 50,
                    'nullable' => true,
                    'comment' => 'Reference number'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.3', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationInterface::TABLE_NAME),
                ReservationInterface::OMS_REFERENCE_NUMBER,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'OMS Reference number'
                ]
            );
        }

        $this->alterTableInventorySource($setup, $context);

        if (version_compare($context->getVersion(), '1.2.6', '<')) {
            $this->installTableUserStore($setup);
        }

        if (version_compare($context->getVersion(), '1.2.7', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::BUSINESS_STATUS,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 50,
                    'nullable' => false,
                    'default' => ReservationItemInterface::BUSINESS_STATUS_RESERVE,
                    'comment' => 'business status (reserve, visit, made purchase, change product, canceled)'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.8', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::REMINDER_EMAIL,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 2,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'reminder email notification'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::AUTOCANCEL_EMAIL,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 2,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'auto cancel email notification'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.9', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(ReservationItemInterface::TABLE_NAME),
                ReservationItemInterface::CREATE_ORDER_OMS,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 2,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'create order to OMS'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $this->upgradeTo130($setup);
        }

        if (version_compare($context->getVersion(), '1.3.1', '<')) {
            $this->upgradeTo131($setup);
        }

        $setup->endSetup();
    }

    /**
     * alter table inventory_source
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    protected function alterTableInventorySource($setup, $context)
    {
        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('inventory_source'),
                'hour_open',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Open store hours'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('inventory_source'),
                'hour_close',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Close store hours'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.4', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('inventory_source'),
                'district_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 5,
                    'nullable' => true,
                    'comment' => 'district id'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('inventory_source'),
                'district',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'district'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.5', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('inventory_source'),
                'city_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 5,
                    'nullable' => true,
                    'comment' => 'city id'
                ]
            );
        }
    }

    /**
     * install table reservation attribute
     *
     * @param SchemaSetupInterface $setup
     */
    protected function installTableReservationAttribute($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ReservationAttributeInterface::TABLE_NAME))
            ->addColumn(
                ReservationAttributeInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )
            ->addColumn(
                ReservationAttributeInterface::RESERVATION_ID,
                Table::TYPE_TEXT,
                5,
                ['nullable' => false],
                'Reservation id'
            )
            ->addColumn(
                ReservationAttributeInterface::ATTRIBUTE,
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'attribute'
            )
            ->addColumn(
                ReservationAttributeInterface::VALUE,
                Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'value'
            )
            ->addColumn(
                ReservationAttributeInterface::FLAG,
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false, 'default' => 1],
                'flag (true/false)'
            )
            ->addColumn(
                ReservationAttributeInterface::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )->addColumn(
                ReservationAttributeInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('reservation attribute table');

        $setup->getConnection()->createTable($table);
    }

    /**
     * install table source attribute
     *
     * @param SchemaSetupInterface $setup
     */
    protected function installTableSourceAttribute($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(SourceAttributeInterface::TABLE_NAME))
            ->addColumn(
                SourceAttributeInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )
            ->addColumn(
                SourceAttributeInterface::SOURCE_CODE,
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Source Code'
            )
            ->addColumn(
                SourceAttributeInterface::ATTRIBUTE,
                Table::TYPE_TEXT,
                25,
                ['nullable' => false],
                'attribute name'
            )
            ->addColumn(
                SourceAttributeInterface::VALUE,
                Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'value'
            )
            ->addColumn(
                SourceAttributeInterface::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )->addColumn(
                ReservationAttributeInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('source attribute table');

        $setup->getConnection()->createTable($table);
    }

    /**
     * install table reservation_product_config
     *
     * @param SchemaSetupInterface $setup
     */
    protected function installTableReservationProductConfig($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ReservationConfigInterface::TABLE_NAME))
            ->addColumn(
                ReservationConfigInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )
            ->addColumn(
                ReservationConfigInterface::CONFIG,
                Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'Product Buffer Config'
            )
            ->addColumn(
                ReservationConfigInterface::TITLE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Buffer Title'
            )
            ->addColumn(
                ReservationConfigInterface::VALUE,
                Table::TYPE_INTEGER,
                5,
                ['nullable' => false],
                'Config Value'
            )
            ->addColumn(
                ReservationConfigInterface::CATEGORY_ID,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Category Id'
            )
            ->addColumn(
                ReservationConfigInterface::PRODUCT_SKU,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product SKU'
            )->addColumn(
                ReservationConfigInterface::FLAG,
                Table::TYPE_INTEGER,
                2,
                ['nullable' => false, 'default' => 1],
                'Flag (bool)'
            )
            ->addColumn(
                ReservationConfigInterface::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )->addColumn(
                ReservationConfigInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('reservation product buffer table');

        $setup->getConnection()->createTable($table);
    }

    /**
     * install table user store
     *
     * @param SchemaSetupInterface $setup
     */
    protected function installTableUserStore($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(UserStoreInterface::TABLE_NAME))
            ->addColumn(
                UserStoreInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )
            ->addColumn(
                UserStoreInterface::USER_ID,
                Table::TYPE_INTEGER,
                5,
                ['nullable' => false],
                'User id'
            )
            ->addColumn(
                UserStoreInterface::STORE_CODE,
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'store_code'
            )
            ->addColumn(
                UserStoreInterface::CREATED_BY,
                Table::TYPE_INTEGER,
                5,
                ['nullable' => true],
                'User id'
            )->addColumn(
                UserStoreInterface::UPDATED_BY,
                Table::TYPE_INTEGER,
                5,
                ['nullable' => true],
                'User id'
            )
            ->addColumn(
                UserStoreInterface::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )->addColumn(
                UserStoreInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('user admin store');

        $setup->getConnection()->createTable($table);
    }

    /**
     * Upgrade to v1.3.0
     *
     * @param SchemaSetupInterface $setup
     */
    protected function upgradeTo130($setup)
    {
        /**
         * Update table 'reservation'
         */
        $table = $setup->getTable(ReservationInterface::TABLE_NAME);
        if ($setup->getConnection()->isTableExists($table) == true) {
            /**
             * Delete unused columns
             */
            if ($setup->getConnection()->tableColumnExists($table, 'source_code') == true) {
                $setup->getConnection()->dropColumn(ReservationInterface::TABLE_NAME, 'source_code');
            }
            if ($setup->getConnection()->tableColumnExists($table, 'reservation_time_start') == true) {
                $setup->getConnection()->dropColumn(ReservationInterface::TABLE_NAME, 'reservation_time_start');
            }
            if ($setup->getConnection()->tableColumnExists($table, 'reservation_time_end') == true) {
                $setup->getConnection()->dropColumn(ReservationInterface::TABLE_NAME, 'reservation_time_end');
            }
            if ($setup->getConnection()->tableColumnExists($table, 'reservation_date_confirm') == true) {
                $setup->getConnection()->dropColumn(ReservationInterface::TABLE_NAME, 'reservation_date_confirm');
            }
            if ($setup->getConnection()->tableColumnExists($table, 'reservation_date_end') == true) {
                $setup->getConnection()->dropColumn(ReservationInterface::TABLE_NAME, 'reservation_date_end');
            }
            if ($setup->getConnection()->tableColumnExists($table, 'oms_reference_number') == true) {
                $setup->getConnection()->dropColumn(ReservationInterface::TABLE_NAME, 'oms_reference_number');
            }

            /**
             * Change the order of columns for better visual
             */
            if ($setup->getConnection()->tableColumnExists($table, 'created_at') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationInterface::TABLE_NAME,
                    'created_at',
                    'created_at',
                    ['type' => Table::TYPE_TIMESTAMP, 'length' => null, 'nullable' => false, 'default' => Table::TIMESTAMP_INIT, 'comment' => 'Created Time', 'after' => 'reservation_date_submit']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'updated_at') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationInterface::TABLE_NAME,
                    'updated_at',
                    'updated_at',
                    ['type' => Table::TYPE_TIMESTAMP, 'length' => null, 'nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE, 'comment' => 'Updated Time', 'after' => 'created_at']
                );
            }
        }

        /**
         * Update table 'reservation_item'
         */
        $table = $setup->getTable(ReservationItemInterface::TABLE_NAME);
        if ($setup->getConnection()->isTableExists($table) == true) {
            /**
             * Delete unused columns
             */
            if ($setup->getConnection()->tableColumnExists($table, 'create_order_oms') == true) {
                $setup->getConnection()->dropColumn(ReservationItemInterface::TABLE_NAME, 'create_order_oms');
            }

            /**
             * Change the order of columns for better visual
             */
            if ($setup->getConnection()->tableColumnExists($table, 'reservation_id') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'reservation_id',
                    'reservation_id',
                    ['type' => Table::TYPE_INTEGER, 'length' => null, 'nullable' => false, 'comment' => 'Reservation Id', 'after' => 'id']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'reference_number') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'reference_number',
                    'reference_number',
                    ['type' => Table::TYPE_TEXT, 'length' => 50, 'nullable' => true, 'comment' => 'Reference Number', 'after' => 'reservation_id']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'source_code') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'source_code',
                    'source_code',
                    ['type' => Table::TYPE_TEXT, 'length' => 50, 'nullable' => true, 'comment' => 'Source Code', 'after' => 'reference_number']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'product_id') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'product_id',
                    'product_id',
                    ['type' => Table::TYPE_INTEGER, 'length' => null, 'nullable' => true, 'comment' => 'Product Id', 'after' => 'source_code']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'qty') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'qty',
                    'qty',
                    ['type' => Table::TYPE_INTEGER, 'length' => null, 'nullable' => true, 'comment' => 'Qty', 'after' => 'product_id']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'buffer') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'buffer',
                    'buffer',
                    ['type' => Table::TYPE_INTEGER, 'length' => null, 'nullable' => true, 'comment' => 'Product Buffer', 'after' => 'qty']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'maxqty') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'maxqty',
                    'maxqty',
                    ['type' => Table::TYPE_INTEGER, 'length' => null, 'nullable' => true, 'comment' => 'Reservation Max Qty', 'after' => 'buffer']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'reservation_date_start') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'reservation_date_start',
                    'reservation_date_start',
                    ['type' => Table::TYPE_DATE, 'length' => null, 'nullable' => true, 'comment' => 'Reservation Start Date', 'after' => 'maxqty']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'reservation_time_start') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'reservation_time_start',
                    'reservation_time_start',
                    ['type' => Table::TYPE_TEXT, 'length' => 6, 'nullable' => true, 'comment' => 'Reservation Start Time', 'after' => 'reservation_date_start']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'reservation_date_end') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'reservation_date_end',
                    'reservation_date_end',
                    ['type' => Table::TYPE_DATE, 'length' => null, 'nullable' => true, 'comment' => 'Reservation End Date', 'after' => 'reservation_time_start']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'reservation_time_end') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'reservation_time_end',
                    'reservation_time_end',
                    ['type' => Table::TYPE_TEXT, 'length' => 6, 'nullable' => true, 'comment' => 'Reservation End Time', 'after' => 'reservation_date_end']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'flag') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'flag',
                    'flag',
                    ['type' => Table::TYPE_TEXT, 'length' => 20, 'nullable' => false, 'default' => 'new', 'comment' => 'Process Flag', 'after' => 'reservation_time_end']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'business_status') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'business_status',
                    'business_status',
                    ['type' => Table::TYPE_TEXT, 'length' => 50, 'nullable' => false, 'default' => 'reserve', 'comment' => 'Business Status', 'after' => 'flag']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'reminder_email') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'reminder_email',
                    'reminder_email',
                    ['type' => Table::TYPE_INTEGER, 'length' => null, 'nullable' => false, 'default' => 0, 'comment' => 'Reminder Email', 'after' => 'business_status']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'autocancel_email') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'autocancel_email',
                    'autocancel_email',
                    ['type' => Table::TYPE_INTEGER, 'length' => null, 'nullable' => false, 'default' => 0, 'comment' => 'Auto Cancel Email', 'after' => 'reminder_email']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'created_at') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'created_at',
                    'created_at',
                    ['type' => Table::TYPE_TIMESTAMP, 'length' => null, 'nullable' => false, 'default' => Table::TIMESTAMP_INIT, 'comment' => 'Created Time', 'after' => 'autocancel_email']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'updated_at') == true) {
                $setup->getConnection()->changeColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'updated_at',
                    'updated_at',
                    ['type' => Table::TYPE_TIMESTAMP, 'length' => null, 'nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE, 'comment' => 'Updated Time', 'after' => 'created_at']
                );
            }
        }
    }

    /**
     * Upgrade to v1.3.1
     *
     * @param SchemaSetupInterface $setup
     */
    protected function upgradeTo131($setup)
    {
        /**
         * Update table 'reservation_item'
         */
        $table = $setup->getTable(ReservationItemInterface::TABLE_NAME);
        if ($setup->getConnection()->isTableExists($table) == true) {
            if ($setup->getConnection()->tableColumnExists($table, 'base_price') == false) {
                $setup->getConnection()->addColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'base_price',
                    ['type' => Table::TYPE_DECIMAL, 'length' => '12,4', 'nullable' => true, 'comment' => 'Base Price Item', 'after' => 'product_id']
                );
            }
            if ($setup->getConnection()->tableColumnExists($table, 'final_price') == false) {
                $setup->getConnection()->addColumn(
                    ReservationItemInterface::TABLE_NAME,
                    'final_price',
                    ['type' => Table::TYPE_DECIMAL, 'length' => '12,4', 'nullable' => true, 'comment' => 'Final Price Item', 'after' => 'base_price']
                );
            }
        }
    }
}
