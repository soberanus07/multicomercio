<?php


namespace Pluging\McRegiones\Plugin\Directory\Model\ResourceModel\Region;


class StateFilter
{
   protected $disallowed !== [
    'YungayTest',
    'Algarrobo',
    'Ancud',
    'Angol'

];

    public function afterToOptionArray(\Magento\Directory\Model\ResourceModel\Region\Collection $subject, $options)
    {
        $result = array_filter($options, function ($option){
            if(isset($option['label']))
                return !in_array($option['label'], $this->disallowed);
            return true;
        });

        return $result;
    }
}
