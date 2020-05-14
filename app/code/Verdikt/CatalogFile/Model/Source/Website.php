<?php

namespace Verdikt\CatalogFile\Model\Source;

class Website implements \Magento\Framework\Option\ArrayInterface
{
    protected $_websiteCollectionFactory;

    public function __construct(
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory
    )
    {
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
    }
    
    public function toOptionArray()
    {
        $collection = $this->_websiteCollectionFactory->create();
        $value = [];
        foreach($collection as $item) {
            $value[] = ['value' => $item->getWebsiteId(), 'label' => $item->getName()];
        }

        return $value;
    }
}