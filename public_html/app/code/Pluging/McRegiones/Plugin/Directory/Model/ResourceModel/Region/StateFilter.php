<?php


namespace Pluging\McRegiones\Plugin\Directory\Model\ResourceModel\Region;


class StateFilter
{
   protected $disallowed = [
    'AISEN DEL GRAL C IBANEZ DEL CAMPO',
    'ANTOFAGASTA',
    'ARAUCANIA',
    'ARICA Y PARINACOTA',
    'ATACAMA',
    'BIOBIO',
    'COQUIMBO',
    'LIBERTADOR GRAL BERNARDO O HIGGINS',
    'LOS LAGOS',
    'LOS RIOS',
    'MAGALLANES Y LA ANTARTICA CHILENA',
    'MAULE',
    'NUBLE',
    'TARAPACA',
    'VALPARAISO',
    'METROPOLITANA DE SANTIAGO'


];

    public function afterToOptionArray(\Magento\Directory\Model\ResourceModel\Region\Collection $subject, $options)
    {
      $result = array_filter($options, function ($option){
             if(isset($option['label']))
                 return in_array($option['label'], $this->disallowed);

 /* Se elimina (!) de in_array para mostrar el listado de comunas a del checkout */


             return true;
         });

         return $result;
    }
}
