<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\Menu;

use Amasty\MegaMenu\Model\OptionSource\Status;
use Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position\Collection as PositionCollection;
use Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position\CollectionFactory as PositionCollectionFactory;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Collection as ItemCollection;
use Amasty\MegaMenu\Model\ResourceModel\Menu\Item\CollectionFactory as ItemCollectionFactory;
use Amasty\MegaMenu\Model\ResourceModel\Menu\ResourceResolver;

class TreeResolver
{
    const ITEM_POSITION_CLASS_PREFIX = 'nav-';

    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    /**
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * @var LayerResolver
     */
    private $layerResolver;

    /**
     * @var CategoryHelper
     */
    private $categoryHelper;

    /**
     * @var ItemCollectionFactory
     */
    private $itemCollectionFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var ResourceResolver
     */
    private $resourceResolver;

    /**
     * @var array
     */
    private $noIncludeInMenu = [];

    /**
     * @var PositionCollectionFactory
     */
    private $positionCollectionFactory;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        ItemCollectionFactory $itemCollectionFactory,
        LayerResolver $layerResolver,
        CategoryHelper $categoryHelper,
        StoreManagerInterface $storeManager,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        ResourceResolver $resourceResolver,
        PositionCollectionFactory $positionCollectionFactory,
        UrlInterface $urlBuilder
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->nodeFactory = $nodeFactory;
        $this->treeFactory = $treeFactory;
        $this->layerResolver = $layerResolver;
        $this->categoryHelper = $categoryHelper;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->resourceResolver = $resourceResolver;
        $this->positionCollectionFactory = $positionCollectionFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param int $storeId
     *
     * @return Node
     */
    public function get($storeId)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
        $categoryCollection = $this->getCategoryCollection($storeId);
        $items = $this->getItemsCollection($storeId);
        $currentCategory = $this->getCurrentCategory();
        $rootCategoryId = $this->getCategoryRootId($storeId);
        $parentCategoryNode = $this->getMenu();
        $mapping = [
            $rootCategoryId => $parentCategoryNode
        ];

        $this->addAdditionalLinks($mapping, $rootCategoryId, $this->getBeforeAdditionalLinks());
        $counter = 1;

        foreach ($this->getSortedItems($storeId) as $sortedItem) {
            switch ($sortedItem->getType()) {
                case ItemInterface::CATEGORY_TYPE:
                    /** @var Category $category */
                    $category = $categoryCollection->getItemById($sortedItem->getEntityId());

                    if ($category === null
                        || $category->getLevel() != \Amasty\MegaMenu\Model\ResourceModel\CategoryCollection::MENU_LEVEL
                    ) {
                        continue 2;
                    }

                    $mapping[$category->getId()] = $this->createCategoryNode(
                        $parentCategoryNode,
                        $category,
                        $currentCategory,
                        true
                    )->setPositionClass(self::ITEM_POSITION_CLASS_PREFIX . $counter); //add node in stack
                    $counter++;

                    break;
                case ItemInterface::CUSTOM_TYPE:
                    $mapKey = 'custom-' . $sortedItem->getEntityId();
                    $item = $items->getCustomItemByEntityId($sortedItem->getEntityId());

                    if (!isset($mapping[$mapKey]) && $item) {
                        $mapping[$mapKey] = $this->createNewNode(
                            $parentCategoryNode,
                            $item,
                            $storeId
                        )->setPositionClass(self::ITEM_POSITION_CLASS_PREFIX . $counter);
                        $counter++;
                    }

                    break;
            }
        }

        foreach ($categoryCollection as $category) {
            if (!$category->getParentCategory()->getIncludeInMenu()
                || !$category->getParentCategory()->getIsActive()
                || isset($this->noIncludeInMenu[$category->getParentId()])
            ) {
                $this->noIncludeInMenu[$category->getId()] = 0;
                continue;
            }

            $categoryParentId = $category->getParentId();

            if (!isset($mapping[$categoryParentId])) {
                $parentIds = $category->getParentIds();
                foreach ($parentIds as $parentId) {
                    if (isset($mapping[$parentId])) {
                        $categoryParentId = $parentId;
                    }
                }
            }

            /** @var Node $parentCategoryNode */
            $parentCategoryNode = $mapping[$categoryParentId];

            if (!isset($mapping[$category->getId()])) {
                $mapping[$category->getId()] = $this->createCategoryNode(
                    $parentCategoryNode,
                    $category,
                    $currentCategory,
                    $category->getParentId() == $categoryParentId
                ); //add node in stack
            }
        }

        $this->overrideByCustomItem($mapping, $storeId);
        $this->addAdditionalLinks($mapping, $rootCategoryId, $this->getAdditionalLinks());

        return $mapping[$rootCategoryId];
    }

    /**
     * @param array $mapping
     * @param int $parentItemId
     * @param array $links
     */
    public function addAdditionalLinks(&$mapping, $parentItemId, $links)
    {
        /** @var Node $parentCategoryNode */
        $parentCategoryNode = $mapping[$parentItemId];
        foreach ($links as $item) {
            if (is_array($item)) {
                $item = $this->dataObjectFactory->create(['data' => $item]);
            }
            if (!$item->getName() || !$item->getId()) {
                continue;
            }

            $childNode = $this->nodeFactory->create(
                [
                    'data' => [
                        ItemInterface::NAME => $item->getName(),
                        'id' => 'additional-node-' . $item->getId(),
                        'url' => $item->getUrl(),
                        ItemInterface::WIDTH => (int)$item->getWidth(),
                        ItemInterface::CONTENT => $item->getContent(),
                        'has_active' => false,
                        'is_active' => $this->isItemActive($item->getUrl()),
                        'is_category' => false,
                        'is_parent_active' => true
                    ],
                    'idField' => 'id',
                    'tree' => $parentCategoryNode->getTree(),
                    'parent' => $parentCategoryNode
                ]
            );
            $parentCategoryNode->addChild($childNode);

            $mapping['additional' . $item->getId()] = $childNode;
        }
    }

    /**
     * public method for creating plugins
     * @return array
     */
    public function getBeforeAdditionalLinks()
    {
        return [];
    }

    /**
     * public method for creating plugins
     * @return array
     */
    public function getAdditionalLinks()
    {
        return [];
    }

    /**
     * @param array $mapping
     * @param int $storeId
     */
    private function overrideByCustomItem(&$mapping, $storeId)
    {
        /** @var Node $parentNode */
        $parentNode = $mapping[$this->getCategoryRootId($storeId)];
        $disabled = [];
        /** @var ItemInterface $item */
        foreach ($this->getItemsCollection($storeId) as $item) {
            switch ($item->getType()) {
                case 'category':
                    $mapKey = $item->getEntityId();
                    $dataToImport = ItemInterface::FIELDS_BY_STORE_CATEGORY;
                    $dataToImport['am_mega_menu_fieldset'][] = ItemInterface::STATUS;
                    if (!isset($mapping[$mapKey])) {
                        continue 2;
                    }
                    break;
                case 'custom':
                    $mapKey = 'custom-' . $item->getEntityId();
                    $dataToImport = ItemInterface::FIELDS_BY_STORE_CUSTOM;
                    if (!isset($mapping[$mapKey])) {
                        $mapping[$mapKey] = $this->createNewNode($parentNode, $item, $storeId);
                    }
                    break;
                default:
                    continue 2;
            }

            /** @var Node $node */
            $node = $mapping[$mapKey];
            $status = $item->getStatus();
            if (($status === null && !$node->getData('status'))
                || $status == Status::DISABLED
                && $item->getStoreId() != Store::DEFAULT_STORE_ID
            ) {
                unset($mapping[$mapKey]);
                $parentNode->removeChild($node);
            } else {
                foreach ($dataToImport as $fieldSet) {
                    foreach ($fieldSet as $field) {
                        $data = $item->getData($field);
                        if ($data !== null) {
                            $node->setData($field, $data);
                        }
                    }
                }
            }
            if ($item->getStatus() == Status::DISABLED && $item->getStoreId() == Store::DEFAULT_STORE_ID) {
                $disabled[] = $mapKey;
            }
        }

        foreach ($disabled as $mapKey) {
            if (isset($mapping[$mapKey])) {
                $node = $mapping[$mapKey];
                if ($node->getStatus() == Status::DISABLED) {
                    unset($mapping[$mapKey]);
                    $parentNode->removeChild($node);
                }
            }
        }
    }

    /**
     * @param $parentNode
     * @param ItemInterface $item
     * @param $storeId
     *
     * @return Node
     */
    private function createNewNode($parentNode, ItemInterface $item, $storeId)
    {
        $itemNode = $this->nodeFactory->create(
            [
                'data' => $this->getItemAsArray(
                    $storeId,
                    $item
                ),
                'idField' => 'id',
                'tree' => $parentNode->getTree(),
                'parent' => $parentNode
            ]
        );
        $parentNode->addChild($itemNode);

        return $itemNode;
    }

    /**
     * @param Node $parentNode
     * @param Category $category
     * @param Category $currentCategory
     * @param bool $isParentActive
     * @return Node
     */
    private function createCategoryNode($parentNode, $category, $currentCategory, $isParentActive)
    {
        $categoryNode = $this->nodeFactory->create(
            [
                'data' => $this->getCategoryAsArray(
                    $category,
                    $currentCategory,
                    $isParentActive
                ),
                'idField' => 'id',
                'tree' => $parentNode->getTree(),
                'parent' => $parentNode
            ]
        );
        $parentNode->addChild($categoryNode);

        return $categoryNode;
    }

    /**
     * @return Node
     */
    private function getMenu()
    {
        return $this->nodeFactory->create(
            [
                'data' => [],
                'idField' => 'root',
                'tree' => $this->treeFactory->create()
            ]
        );
    }

    /**
     * Get current Category from catalog layer
     *
     * @return \Magento\Catalog\Model\Category|null
     */
    private function getCurrentCategory()
    {
        $result = null;
        $catalogLayer = $this->layerResolver->get();

        if ($catalogLayer) {
            $result = $catalogLayer->getCurrentCategory();
        }

        return $result;
    }

    /**
     * Convert category to array
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Catalog\Model\Category $currentCategory
     * @param bool $isParentActive
     *
     * @return array
     */
    private function getCategoryAsArray($category, $currentCategory, $isParentActive)
    {
        return [
            'name' => $category->getName(),
            'id' => 'category-node-' . $category->getId(),
            'url' => $this->categoryHelper->getCategoryUrl($category),
            'has_active' => in_array(
                (string)$category->getId(),
                explode('/', $currentCategory->getPath()),
                true
            ),
            'is_active' => $category->getId() == $currentCategory->getId(),
            'is_category' => true,
            'is_parent_active' => $isParentActive
        ];
    }

    /**
     * @param int $storeId
     * @param ItemInterface $item
     *
     * @return array
     */
    private function getItemAsArray($storeId, $item)
    {
        $url = $this->getAbsoluteUrl($storeId, $item->getUrl());

        return [
            ItemInterface::NAME => $item->getName(),
            'id' => 'custom-node-' . $item->getEntityId(),
            'url' => $url,
            ItemInterface::WIDTH => $item->getWidth(),
            ItemInterface::CONTENT => $item->getContent(),
            'has_active' => false,
            'is_active' => $this->isItemActive($url),
            'is_category' => false,
            'is_parent_active' => true,
            ItemInterface::STATUS => $item->getStatus()
        ];
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    protected function isItemActive(string $url)
    {
        if ($url) {
            $result = strpos($this->urlBuilder->getCurrentUrl(), $url) !== false;
        }

        return $result ?? false;
    }

    /**
     * @param int $storeId
     * @return CategoryCollection
     * @throws LocalizedException
     */
    private function getCategoryCollection($storeId)
    {
        /** @var CategoryCollection $collection */
        $collection = $this->categoryCollectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addAttributeToSelect('name');
        $collection->addFieldToFilter(
            'path',
            ['like' => '1/' . $this->getCategoryRootId($storeId) . '/%']
        ); //load only from store root
        $collection->addAttributeToFilter('include_in_menu', 1);
        $collection->addIsActiveFilter();
        $collection->addUrlRewriteToResult();
        $collection->addOrder('level', Collection::SORT_ORDER_ASC);
        $collection->addOrder('position', Collection::SORT_ORDER_ASC);
        $collection->addOrder('parent_id', Collection::SORT_ORDER_ASC);
        $collection->addOrder('entity_id', Collection::SORT_ORDER_ASC);

        return $collection;
    }

    /**
     * @param int $storeId
     *
     * @return ItemCollection
     */
    private function getItemsCollection($storeId)
    {
        /** @var ItemCollection $collection */
        $collection = $this->itemCollectionFactory->create();
        $collection->getSelect()->joinLeft(
            ['links' => $collection->getTable(LinkInterface::TABLE_NAME)],
            'main_table.entity_id = links.entity_id AND main_table.type = \'custom\'',
            ['link_type', 'page_id']
        );
        $collection->addFieldToFilter('store_id', [$storeId, Store::DEFAULT_STORE_ID]);
        $collection->addOrder('store_id', Collection::SORT_ORDER_ASC);

        $this->resourceResolver->joinLink($collection, 'links', 'url');

        return $collection;
    }

    /**
     * @param $storeId
     *
     * @return int
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCategoryRootId($storeId)
    {
        return $this->storeManager->getStore($storeId)->getRootCategoryId();
    }

    /**
     * Get store base url
     *
     * @param int $storeId
     * @param string $type
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStoreBaseUrl($storeId, $type = \Magento\Framework\UrlInterface::URL_TYPE_LINK)
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore($storeId);
        $isSecure = $store->isUrlSecure();

        return rtrim($store->getBaseUrl($type, $isSecure), '/') . '/';
    }

    /**
     * Get url
     *
     * @param int $storeId
     * @param string $url
     * @param string $type
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getAbsoluteUrl($storeId, $url, $type = \Magento\Framework\UrlInterface::URL_TYPE_LINK)
    {
        return $this->getStoreBaseUrl($storeId, $type) . ltrim($url, '/');
    }

    /**
     * @param int $storeId
     * @return PositionCollection
     */
    public function getSortedItems(int $storeId)
    {
        return $this->positionCollectionFactory->create()->getSortedCollection($storeId);
    }
}
