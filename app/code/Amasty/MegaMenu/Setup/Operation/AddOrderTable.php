<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Setup\Operation;

use Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddOrderTable
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable(Position::TABLE)
        )->addColumn(
            Position::ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Amasty Menu Order Item Auto ID'
        )->addColumn(
            Position::ROOT_CATEGORY_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'nullable' => false, 'primary' => false],
            'Root Category'
        )->addColumn(
            Position::TYPE,
            Table::TYPE_TEXT,
            20,
            ['nullable' => false],
            'Amasty Menu Item Type (category or amasty link)'
        )->addColumn(
            Position::POSITION,
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'nullable' => true, 'primary' => false, 'default' => 99999],
            'Amasty Menu Item Sort Order'
        )->addColumn(
            Position::ENTITY_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'nullable' => false, 'primary' => false],
            'Amasty Menu Item ID'
        )->addIndex(
            $setup->getIdxName(
                $setup->getTable(Position::TABLE),
                [Position::ROOT_CATEGORY_ID, Position::TYPE, Position::ENTITY_ID],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [Position::ROOT_CATEGORY_ID, Position::TYPE, Position::ENTITY_ID],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        );

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
