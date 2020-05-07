<?php

namespace Verdikt\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\Store;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class CheckoutSetStore implements ObserverInterface
{
    protected $customerRepositoryInterface;

    public function __construct
    (
        HttpContext $httpContext,
        StoreCookieManagerInterface $storeCookieManager,
        StoreRepositoryInterface $storeRepository,
        CustomerRepositoryInterface $customerRepositoryInterface

    ) {
        $this->httpContext = $httpContext;
        $this->storeCookieManager = $storeCookieManager;
        $this->storeRepository = $storeRepository;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $quote = $observer->getQuote();
        $orderStoreName = $order->getStore()->getName();
        $orderStoreId = $order->getStore()->getId();

        $customer = $this->customerRepositoryInterface->getById($order->getCustomerId());
        $customer->setStoreId($orderStoreId);
        $customer->setCreatedIn($orderStoreName);
        $this->customerRepositoryInterface->save($customer);
        // Do whatever you want here

        return $this;
    }
}