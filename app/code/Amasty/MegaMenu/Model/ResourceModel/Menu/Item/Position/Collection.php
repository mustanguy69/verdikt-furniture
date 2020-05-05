<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position;

use Amasty\MegaMenu\Model\Menu\Item\Position;
use Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position as PositionResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setIdFieldName(PositionResource::ID);
        $this->_init(
            Position::class,
            PositionResource::class
        );
    }

    /**
     * @param int $storeId
     * @return Collection
     */
    public function getSortedCollection(int $storeId)
    {
        return $this->addFieldToFilter(PositionResource::STORE_VIEW, $storeId)
            ->addOrder(PositionResource::POSITION, 'asc');
    }
}
