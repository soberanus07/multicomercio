<?php

namespace ChilexpressOficial\Chilexpress\Model\Config\Source;

class CategoriaArticulo implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \ChilexpressOficial\Chilexpress\Helper\Data
     */
    protected $_helper;
    
    public function __construct(
        \ChilexpressOficial\Chilexpress\Helper\Data $helper
    )
    {
        $this->_helper  = $helper;
    }
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_helper->getDescripcionArticulos();
    }
}