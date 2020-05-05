<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\ResourceModel\Menu\Item;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;

class Collection extends AbstractCollection
{
    /**
     * @var array
     */
    private $customItems = [];

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setIdFieldName(ItemInterface::ID);
        $this->_init(
            \Amasty\MegaMenu\Model\Menu\Item::class,
            \Amasty\MegaMenu\Model\ResourceModel\Menu\Item::class
        );
    }

    /**
     * @inheritdoc
     */
    public function addItem(\Magento\Framework\DataObject $item)
    {
        if ($item->getData(ItemInterface::TYPE) == ItemInterface::CUSTOM_TYPE) {
            $this->customItems[$item->getData(ItemInterface::ENTITY_ID)] = $item;
        }

        return parent::addItem($item);
    }

    /**
     * @param $entityId
     * @return ItemInterface|null
     */
    public function getCustomItemByEntityId($entityId)
    {
        $this->load();
        return $this->customItems[$entityId] ?? null;
    }
}
