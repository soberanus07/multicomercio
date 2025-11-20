<?php
namespace Micomercio\MpPayerData\Plugin;

use Magento\Checkout\Model\Session as CheckoutSession;

class ClientPlugin
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * Constructor
     *
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Add payer data before creating preference
     *
     * @param \MercadoPago\Core\Model\Client $subject
     * @param array                             $body
     * @return array
     */
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
                    'number' => $order->getBillingAddress()->getTelephone()
                ]
            ];
        }
        return [$body];
    }
}
