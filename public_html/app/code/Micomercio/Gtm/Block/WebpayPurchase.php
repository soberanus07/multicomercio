<?php

namespace Micomercio\Gtm\Block;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Session;

class WebpayPurchase extends Template
{
    protected $checkoutSession;

    public function __construct(
        Template\Context $context,
        Session $checkoutSession,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
    }

    public function getOrderAmount()
    {
        $order = $this->checkoutSession->getLastRealOrder();
        return $order ? $order->getGrandTotal() : 0;
    }

    public function getOrderId()
    {
        $order = $this->checkoutSession->getLastRealOrder();
        return $order ? $order->getIncrementId() : null;
    }
}
