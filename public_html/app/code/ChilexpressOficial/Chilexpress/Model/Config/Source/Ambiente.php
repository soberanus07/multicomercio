<?php

namespace ChilexpressOficial\Chilexpress\Model\Config\Source;

class Ambiente implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $VALUE = 'value';
        $LABEL = 'label';
        return [
            [$VALUE => 'production', $LABEL => 'Production'],
            [$VALUE => 'staging', $LABEL => 'Staging'],
        ];
    }
}