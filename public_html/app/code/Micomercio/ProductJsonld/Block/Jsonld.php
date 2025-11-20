<?php
namespace Micomercio\ProductJsonld\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Catalog\Helper\Image;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Jsonld extends Template
{
    protected $registry;
    protected $imageHelper;
    protected $priceCurrency;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        Image $imageHelper,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->imageHelper = $imageHelper;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getCurrency()
    {
        return $this->priceCurrency->getCurrency()->getCurrencyCode();
    }

    public function getImageUrl($product)
    {
        return $this->imageHelper->init($product, 'product_base_image')->getUrl();
    }
}

