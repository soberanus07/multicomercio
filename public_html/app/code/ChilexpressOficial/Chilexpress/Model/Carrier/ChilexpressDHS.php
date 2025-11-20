<?php
namespace ChilexpressOficial\Chilexpress\Model\Carrier;


use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Helper;
use Magento\Shipping\Model\Carrier\CarrierInterface;

class ChilexpressDHS extends Chilexpress implements CarrierInterface
{

    protected $serviceTypeCode = 3;
    /**
    * @var string
    */
    protected $_code = 'chilexpressdhs';
}