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

use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\Db\Ddl\Table;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\InstallSchemaInterface;
use \Trans\Reservation\Api\Data\ReservationInterface;
use \Trans\Reservation\Api\Data\ReservationItemInterface;

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

        $this->createReservationTable($installer);
        $this->createReservationItemTable($installer);

        $installer->endSetup();
    }

    /**
     * Create table reservation
     *
     * @param $installer
     */
    private function createReservationTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(ReservationInterface::TABLE_NAME))
            ->addColumn(
                ReservationInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )->addColumn(
                ReservationInterface::FLAG,
                Table::TYPE_TEXT,
                20,
                ['nullable' => false, 'default' => 'new'],
                'process flag finish/confirm/new/cancel'
            )->addColumn(
                ReservationInterface::SOURCE_CODE,
                Table::TYPE_TEXT,
                25,
                ['nullable' => true],
                'source code/store code/warehouse code'
            )->addColumn(
                ReservationInterface::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )->addColumn(
                ReservationInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('Reservation Table');

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table reservation item
     *
     * @param $installer
     */
    private function createReservationItemTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(ReservationItemInterface::TABLE_NAME))
            ->addColumn(
                ReservationItemInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )->addColumn(
                ReservationItemInterface::RESERVATION_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Reservation Id'
            )->addColumn(
                ReservationItemInterface::FLAG,
                Table::TYPE_TEXT,
                20,
                ['nullable' => false, 'default' => 'new'],
                'process flag finish/confirm/new/cancel'
            )->addColumn(
                ReservationItemInterface::QTY,
                Table::TYPE_INTEGER,
                9,
                ['nullable' => true],
                'quantity'
            )->addColumn(
                ReservationItemInterface::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )->addColumn(
                ReservationItemInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('Reservation Item Table');

        $installer->getConnection()->createTable($table);
    }
}
