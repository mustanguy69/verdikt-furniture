<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Model\Quote;

use Amasty\Checkout\Model\Config;
use Amasty\Checkout\Model\FieldsDefaultProvider;
use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Webapi\ServiceOutputProcessor;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;

/**
 * Initialize additional quote data to avoid extra requests on storefront.
 *
 * @since 3.0.0
 */
class CheckoutInitialization implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $checkoutConfig;

    /**
     * @var FieldsDefaultProvider
     */
    private $defaultProvider;

    /**
     * @var ServiceOutputProcessor
     */
    private $outputProcessor;

    /**
     * @var PaymentInformationManagement
     */
    private $paymentManagement;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Shipping\AddressMethods
     */
    private $addressMethods;

    public function __construct(
        Config $checkoutConfig,
        PaymentInformationManagement $paymentManagement,
        FieldsDefaultProvider $defaultProvider,
        ServiceOutputProcessor $outputProcessor,
        CartRepositoryInterface $quoteRepository,
        Shipping\AddressMethods $addressMethods
    ) {
        $this->checkoutConfig = $checkoutConfig;
        $this->defaultProvider = $defaultProvider;
        $this->outputProcessor = $outputProcessor;
        $this->paymentManagement = $paymentManagement;
        $this->quoteRepository = $quoteRepository;
        $this->addressMethods = $addressMethods;
    }

    /**
     * Get shipping
     *
     * @param Quote $quote
     *
     * @return array|object
     */
    public function getShippingMethods($quote)
    {
        $methods = $this->addressMethods->getShippingMethods($this->getShippingAddress($quote));

        return $this->outputProcessor->convertValue(
            $methods,
            '\Magento\Quote\Api\Data\ShippingMethodInterface[]'
        );
    }

    /**
     * Resolve quote shipping address.
     *
     * @param Quote $quote
     *
     * @return Address
     */
    public function getShippingAddress($quote)
    {
        if ($quote->getCustomerId() && $shippingAddress = $this->getCustomerShippingAddress($quote)) {
            return $shippingAddress;
        }

        /** @var Address $shippingAddress */
        $shippingAddress = $quote->getShippingAddress();
        if ($shippingAddress->getId() && $shippingAddress->getCountryId()) {
            return $shippingAddress;
        }

        $shippingAddress->addData($this->defaultProvider->getDefaultData());
        if (!$shippingAddress->getCountryId()) {
            $shippingAddress->setCountryId($this->checkoutConfig->getDefaultCountryId());
        }

        return $shippingAddress;
    }

    /**
     * Check quote shipping address
     * convert customer address into shipping if quote shipping is not selected.
     * false if customer doesn't have any address.
     *
     * @param Quote $quote
     *
     * @return Address|false
     */
    private function getCustomerShippingAddress($quote)
    {
        $shippingAddress = $quote->getShippingAddress();
        if ($shippingAddress->getId() && $shippingAddress->getCustomerAddressId()) {
            return $shippingAddress;
        }

        $customerAddress = $this->getCustomerAddress($quote->getCustomer());

        if (!$customerAddress) {
            return false;
        }

        $addressByCustomer = $quote->getShippingAddressByCustomerAddressId($customerAddress->getId());
        if ($addressByCustomer) {
            $quote->setShippingAddress($addressByCustomer);

            return $addressByCustomer;
        }

        $shippingAddress->importCustomerAddressData($customerAddress);

        return $shippingAddress;
    }

    /**
     * Get default of first customer address.
     *
     * @param CustomerInterface $customer
     *
     * @return AddressInterface|false
     */
    private function getCustomerAddress($customer)
    {
        $customer->getDefaultShipping();
        $addresses = $customer->getAddresses();
        if (empty($addresses)) {
            return false;
        }

        foreach ($addresses as $customerAddress) {
            if ($customerAddress->isDefaultShipping()) {
                return $customerAddress;
            }
        }

        return reset($addresses);
    }

    /**
     * @param Quote $quote
     */
    public function initializeShipping(CartInterface $quote)
    {
        $shippingAddress = $this->getShippingAddress($quote);

        $this->addressMethods->processShippingAssignment($quote, $shippingAddress);

        $this->quoteRepository->save($quote);
    }

    /**
     * @param int $quoteId
     *
     * @return array
     */
    public function getPaymentMethods(int $quoteId)
    {
        $result = $this->paymentManagement->getPaymentInformation($quoteId);

        return $this->outputProcessor->convertValue($result, PaymentDetailsInterface::class);
    }
}
