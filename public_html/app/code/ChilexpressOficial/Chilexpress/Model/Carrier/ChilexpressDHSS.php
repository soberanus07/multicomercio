<?php
namespace ChilexpressOficial\Chilexpress\Model\Carrier;


use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Helper;
use Magento\Shipping\Model\Carrier\CarrierInterface;

class ChilexpressDHSS extends Chilexpress implements CarrierInterface
{
    protected $serviceTypeCode = 4;
    /**
    * @var string
    */
    protected $_code = 'chilexpressdhss';

}