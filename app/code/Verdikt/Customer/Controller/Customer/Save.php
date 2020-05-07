<?php

namespace Verdikt\Customer\Controller\Customer;

use Magento\Store\Model\Store;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Save extends \Magento\Framework\App\Action\Action { 

    protected $httpContext;

    protected $storeCookieManager;

    protected $storeRepository;

    protected $sessionFactory;

	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        HttpContext $httpContext,
        StoreCookieManagerInterface $storeCookieManager,
        StoreRepositoryInterface $storeRepository,
        CustomerRepositoryInterface $customerRepositoryInterface,
        SessionFactory $sessionFactory)
	{
        $this->httpContext = $httpContext;
        $this->storeCookieManager = $storeCookieManager;
        $this->storeRepository = $storeRepository;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->sessionFactory = $sessionFactory;

        return parent::__construct($context);
	}

	public function execute()
	{
        $post = $this->getRequest()->getPostValue();
        $selectedStore = $post['selected-store'];

        $store = $this->storeRepository->getActiveStoreByCode($selectedStore);
        $this->httpContext->setValue(Store::ENTITY, $selectedStore, 'default');
        $this->storeCookieManager->setStoreCookie($store);

        $session = $this->sessionFactory->create();
        
        if ($session->isLoggedIn()) {
            $customerSession = $session->getCustomer();
            $customerId = $customerSession->getId();
            $customer = $this->customerRepositoryInterface->getById($customerId);
            $customer->setStoreId($store->getId());
            $customer->setCreatedIn($store->getName());

            $this->customerRepositoryInterface->save($customer);
        }

        return $this->_redirect('selectedstore/customer/index');
	}
} 
