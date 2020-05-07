<?php

namespace Verdikt\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\Store;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class CustomerLogin implements ObserverInterface
{
    
    public function __construct
    (
        HttpContext $httpContext,
        StoreCookieManagerInterface $storeCookieManager,
        StoreRepositoryInterface $storeRepository

    ) {
        $this->httpContext = $httpContext;
        $this->storeCookieManager = $storeCookieManager;
        $this->storeRepository = $storeRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $store = $this->storeRepository->getById($customer->getStoreId());
        $this->httpContext->setValue(Store::ENTITY, $store->getCode(), 'default');
        $this->storeCookieManager->setStoreCookie($store);
        
        return true;
    }
}