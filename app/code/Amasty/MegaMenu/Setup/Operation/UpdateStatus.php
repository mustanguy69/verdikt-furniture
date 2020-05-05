<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Setup\Operation;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Magento\Framework\DB\Ddl\Table;
use \Magento\Framework\Setup\SchemaSetupInterface;

class UpdateStatus
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(ItemInterface::TABLE_NAME);

        $setup->getConnection()->modifyColumn(
            $tableName,
            ItemInterface::STATUS,
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0
            ]
        );

        if ($setup->getConnection()->tableColumnExists($tableName, ItemInterface::SORT_ORDER)) {
            $setup->getConnection()->dropColumn($tableName, ItemInterface::SORT_ORDER);
        }
    }
}
