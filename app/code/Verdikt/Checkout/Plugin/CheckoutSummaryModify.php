<?php

namespace Verdikt\Checkout\Plugin;

class CheckoutSummaryModify {

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $result) {
        $result['storeName'] = $this->storeManager->getStore()->getName();
        return $result;
    }
}