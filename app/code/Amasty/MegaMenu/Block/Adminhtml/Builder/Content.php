<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Block\Adminhtml\Builder;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Model\OptionSource\Status;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\Store;

class Content extends \Magento\Backend\Block\Template
{
    protected $_template = 'Amasty_MegaMenu::builder/content.phtml';

    /**
     * @var CategoryCollection
     */
    private $categoryCollection;

    /**
     * @var \Amasty\MegaMenu\Model\Menu\TreeResolver
     */
    private $treeResolver;

    /**
     * @var \Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Collection
     */
    private $itemsCollection;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Amasty\MegaMenu\Model\ResourceModel\Menu\Item\CollectionFactory
     */
    private $itemsCollectionFactory;

    /**
     * @var \Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position\CollectionFactory
     */
    private $positionCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\MegaMenu\Model\Menu\TreeResolver $treeResolver,
        CategoryCollection $categoryCollection,
        \Amasty\MegaMenu\Model\ResourceModel\Menu\Item\CollectionFactory $itemsCollectionFactory,
        \Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position\CollectionFactory $positionCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->treeResolver = $treeResolver;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->itemsCollectionFactory = $itemsCollectionFactory;
        $this->positionCollectionFactory = $positionCollectionFactory;
    }

    /**
     * @return \Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItems()
    {
        $storeId = $this->getStoreId();

        return $this->positionCollectionFactory->create()->getSortedCollection($storeId);
    }

    /**
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/*/move');
    }

    /**
     * @param ItemInterface $item
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getContentItem($item)
    {
        $entityId = $item->getEntityId();
        switch ($item->getType()) {
            case 'category':
                $collection = $this->getCategoryCollection();
                break;
            case 'custom':
            default:
                $collection = $this->getItemsCollection();
                break;
        }

        return $collection->getItemByColumnValue('entity_id', $entityId);
    }

    /**
     * @return CategoryCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCategoryCollection()
    {
        if ($this->categoryCollection === null) {
            /** @var CategoryCollection $collection */
            $this->categoryCollection = $this->categoryCollectionFactory->create();
            $this->categoryCollection->setStoreId($this->getStoreId());
            $this->categoryCollection->addAttributeToSelect('name');
            $this->categoryCollection->addFieldToFilter(
                'path',
                ['like' => '1/' . $this->getRootCategoryId() . '/%']
            ); //load only from store root
            $this->categoryCollection->addAttributeToFilter('include_in_menu', 1);
            $this->categoryCollection->addIsActiveFilter();
        }

        return $this->categoryCollection;
    }

    /**
     * @return \Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Collection
     */
    private function getItemsCollection()
    {
        if ($this->itemsCollection === null) {
            $this->itemsCollection = $this->itemsCollectionFactory->create();
            $this->itemsCollection->addFieldToFilter('store_id', Store::DEFAULT_STORE_ID)
                ->addFieldToFilter(ItemInterface::TYPE, ItemInterface::CUSTOM_TYPE);

            $storeCollection = $this->itemsCollectionFactory->create()
                ->addFieldToFilter('store_id', $this->getStoreId())
                ->addFieldToFilter(ItemInterface::TYPE, ItemInterface::CUSTOM_TYPE);
            foreach ($this->itemsCollection->getItems() as $key => $item) {
                $storeModel = $storeCollection->getCustomItemByEntityId($item->getEntityId());
                if ($storeModel) {
                    $item->addData(
                        array_filter(
                            $storeModel->getData(),
                            static function ($var) {
                                return $var !== null;
                            }
                        )
                    );
                }

                if ($item->getStatus() == Status::DISABLED) {
                    $this->itemsCollection->removeItemByKey($key);
                }
            }
        }

        return $this->itemsCollection;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getRootCategoryId()
    {
        return $this->_storeManager->getStore($this->getStoreId())->getRootCategoryId();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getStoreId()
    {
        return $this->getLayout()->getBlock('menu.builder.store.switcher')->getStoreId();
    }
}
