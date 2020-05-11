<?php

namespace Verdikt\Checkout\Plugin;

use Magento\Customer\Model\SessionFactory;

class CheckoutSummaryModify {

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    protected $sessionFactory;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        SessionFactory $sessionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->sessionFactory = $sessionFactory;
    }
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $result) {

        $customerSession = $this->sessionFactory->create();
        if ($customerSession->isLoggedIn()) {
             $customer = $customerSession->getCustomer();
             $result['storeName'] = $customer->getCreatedIn();
        } else {
            $result['storeName'] = $this->storeManager->getStore()->getName();
        }

        return $result;
    }
}