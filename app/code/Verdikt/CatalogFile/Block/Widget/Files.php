<?php

namespace Verdikt\CatalogFile\Block\Widget;

use Verdikt\CatalogFile\Model\FilesFactory;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface; 

class Files extends Template implements BlockInterface
{

	protected $_filesFactory;

	protected $_template = "widget/files.phtml";

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        FilesFactory $filesFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
		array $data = []
	 ) {

        $this->_storeManager = $storeManager;
        $this->_filesFactory = $filesFactory;
        $this->_objectManager = $objectManager;

		parent::__construct($context, $data);
	}

	/**
     * Get website identifier
     *
     * @return string|int|null
     */
    public function getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    public function getWebsiteIdByStore($name) {
        return $this->_storeManager->getStore($name)->getWebsiteId();
    }

    function getFileByWebsiteId() 
    {
        $websiteId = $this->getWebsiteId();
        $file = $this->_filesFactory->create();
        $file = $file->getCollection();
		$file->addFieldToSelect('*');
        $file->addFieldToFilter('website_id', ['eq' => $websiteId]);
        
        return $file->getFirstItem();
    }

    function getMediaBaseUrl($url) 
    {
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $om->get('Magento\Store\Model\StoreManagerInterface');
		$currentStore = $storeManager->getStore();

		return $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA). $url;
	}

}