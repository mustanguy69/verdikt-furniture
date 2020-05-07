<?php

namespace Verdikt\Customer\Block;

use Magento\Customer\Model\SessionFactory;
use Magento\Store\Model\StoreManagerInterface;



class SelectedStore extends \Magento\Framework\View\Element\Template
{
    protected $sessionFactory;

	protected $storeManager;

	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		SessionFactory $sessionFactory,
		StoreManagerInterface $storeManager)
	{
        parent::__construct($context);
		$this->sessionFactory = $sessionFactory;
		$this->storeManager = $storeManager;
	}

	public function getCustomer()
	{
        $customerSession = $this->sessionFactory->create();
        if ($customerSession->isLoggedIn()) {
             $customer = $customerSession->getCustomer();
        }
		
		return $customer;
	}

	public function getBaseUrl() {
		return $this->storeManager->getStore()->getBaseUrl();
	}
}