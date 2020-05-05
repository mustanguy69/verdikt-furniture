<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\ResourceModel;

use Magento\Framework\Data\Collection;

class CategoryCollection extends \Magento\Catalog\Model\ResourceModel\Category\Collection
{
    const MENU_LEVEL = 2;

    /**
     * @param int|null $storeId
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIncludedInMenuCategories($storeId = null)
    {
        if ($storeId !== null) {
            $this->setStoreId($storeId);
        }

        $this->addLevelFilter(self::MENU_LEVEL);
        $this->addAttributeToFilter('include_in_menu', 1);
        $this->addAttributeToSelect('name', true);
        $this->addIsActiveFilter();
        $this->addOrder('position', Collection::SORT_ORDER_ASC);

        return $this;
    }
}
