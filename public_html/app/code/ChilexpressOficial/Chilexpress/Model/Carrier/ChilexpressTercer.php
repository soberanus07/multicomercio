<?php
namespace ChilexpressOficial\Chilexpress\Model\Carrier;


use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Helper;
use Magento\Shipping\Model\Carrier\CarrierInterface;

class ChilexpressTercer extends Chilexpress implements CarrierInterface
{
    protected $serviceTypeCode = 5;
    /**
    * @var string
    */
    protected $_code = 'chilexpresstercer';

}