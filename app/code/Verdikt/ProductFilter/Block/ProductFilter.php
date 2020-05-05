<?php
namespace Verdikt\ProductFilter\Block;
class ProductFilter extends \Magento\Framework\View\Element\Template
{    
    protected $_productCollectionFactory;
        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,        
        array $data = []
    )
    {    
        $this->_productCollectionFactory = $productCollectionFactory;    
        parent::__construct($context, $data);
    }
    
    public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                    ->addAttributeToFilter('is_saleable', 1, 'left')
                    ->addAttributeToSort('created_at','desc');

        $collection->setPageSize(12); // fetching only 3 products
        return $collection;
    }
}
?>