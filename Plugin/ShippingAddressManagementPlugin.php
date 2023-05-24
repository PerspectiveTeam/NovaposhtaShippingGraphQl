<?php

namespace Perspective\NovaposhtaShippingGraphQl\Plugin;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Perspective\NovaposhtaShippingGraphQl\Model\Quote\Info\Type\ProcessorsChain;

class ShippingAddressManagementPlugin
{
    private ProcessorsChain $shippingCartProcessor;

    private CartRepositoryInterface $quoteRepository;

    /**
     * @param \Perspective\NovaposhtaShippingGraphQl\Model\Quote\Info\Type\ProcessorsChain $shippingCartProcessor
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        ProcessorsChain $shippingCartProcessor,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->shippingCartProcessor = $shippingCartProcessor;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param ShippingAddressManagementInterface $subject
     * @param \Closure $proceed
     * @param int $cart
     * @param AddressInterface $address
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundAssign(ShippingAddressManagementInterface $subject, $proceed, int $cart, AddressInterface $address)
    {
        $result = $proceed($cart, $address);
        $quote = $this->quoteRepository->getActive($cart);
        $this->shippingCartProcessor->process($quote);
        return $result;
    }
}
