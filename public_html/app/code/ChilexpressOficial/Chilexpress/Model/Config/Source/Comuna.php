<?php

namespace ChilexpressOficial\Chilexpress\Model\Config\Source;

class Comuna implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => 'Seleccionar Comuna'],
        ];
    }
}