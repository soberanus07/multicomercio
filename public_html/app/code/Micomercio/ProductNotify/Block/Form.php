<?php
namespace Micomercio\ProductNotify\Block;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Registry;

class Form extends Template
{
    protected $registry;
    protected $formKey;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        FormKey $formKey,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->formKey = $formKey;
        parent::__construct($context, $data);
    }

    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getFormAction()
    {
        return $this->getUrl('productnotify/index/submit');
    }
}
