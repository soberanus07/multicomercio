<?php
namespace Micomercio\MpIntegration\Plugin;

use Magento\Checkout\Model\Session as CheckoutSession;

class ClientPlugin
{
    private $checkoutSession;

    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function beforeCreatePreference($subject, array $body)
    {
        $order = $this->checkoutSession->getLastRealOrder();
        if ($order && $order->getId()) {
            $body['payer'] = [
                'email' => $order->getCustomerEmail(),
                'first_name' => $order->getCustomerFirstname(),
                'last_name' => $order->getCustomerLastname(),
                'identification' => [
                    'type' => 'RUT',
                    'number' => $order->getData('customer_taxvat') ?: ''
                ],
                'phone' => [
                    'area_code' => '',
                    'number'    => $order->getBillingAddress()->getTelephone()
                ]
            ];

            // Add statement_descriptor to preferences
            $body['statement_descriptor'] = 'MULTICOMERCIO.CL';
        }
        return [$body];
    }
}
