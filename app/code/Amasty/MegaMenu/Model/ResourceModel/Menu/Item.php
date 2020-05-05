<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\ResourceModel\Menu;

use Amasty\MegaMenu\Model\ResourceModel\CategoryCollection;
use Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Magento\Store\Model\StoreManagerInterface;

class Item extends AbstractDb
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Initialize table nad PK name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ItemInterface::TABLE_NAME, ItemInterface::ID);
    }

    /**
     * @param Category|ItemInterface $object
     * @return AbstractDb
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $data = [];
        $storeIds = [];

        switch ($object->getType()) {
            case ItemInterface::CUSTOM_TYPE:
                foreach ($this->storeManager->getStores() as $store) {
                    $storeIds[] = $store->getId();
                }
                break;
            case ItemInterface::CATEGORY_TYPE:
                $category = $this->categoryRepository->get($object->getEntityId());
                if ($category->getLevel() == CategoryCollection::MENU_LEVEL) {
                    $storeIds = $category->getStoreIds();
                }
                break;
        }
        foreach ($storeIds as $storeId) {
            $data[] = [
                Position::STORE_VIEW => $storeId,
                Position::TYPE => $object->getType(),
                Position::ENTITY_ID => $object->getEntityId()
            ];
        }
        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate(
                $this->getTable(Position::TABLE),
                $data,
                [ItemInterface::ENTITY_ID]
            );
        }

        return parent::_afterSave($object);
    }
}
