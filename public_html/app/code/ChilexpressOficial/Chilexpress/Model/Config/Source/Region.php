<?php

namespace ChilexpressOficial\Chilexpress\Model\Config\Source;

class Region implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        
        $VALUE = 'value';
        $LABEL = 'label';
        return [
            [$VALUE => 'R1', $LABEL => 'TARAPACA'],
            [$VALUE => 'R2', $LABEL => 'ANTOFAGASTA'],
            [$VALUE => 'R3', $LABEL => 'ATACAMA'],
            [$VALUE => 'R4', $LABEL => 'COQUIMBO'],
            [$VALUE => 'R5', $LABEL => 'VALPARAISO'],
            [$VALUE => 'R6', $LABEL => 'LIBERTADOR GRAL BERNARDO O HIGGINS'],
            [$VALUE => 'R7', $LABEL => 'MAULE'],
            [$VALUE => 'R8', $LABEL => 'BIOBIO'],
            [$VALUE => 'R9', $LABEL => 'ARAUCANIA'],
            [$VALUE => 'RM', $LABEL => 'METROPOLITANA DE SANTIAGO'],
            [$VALUE => 'R10', $LABEL => 'LOS LAGOS'],
            [$VALUE => 'R11', $LABEL => 'AISEN DEL GRAL C IBANEZ DEL CAMPO'],
            [$VALUE => 'R12', $LABEL => 'MAGALLANES Y LA ANTARTICA CHILENA'],
            [$VALUE => 'R14', $LABEL => 'LOS RIOS'],
            [$VALUE => 'R15', $LABEL => 'ARICA Y PARINACOTA'],
            [$VALUE => 'R16', $LABEL => 'NUBLE'],
        ];
    }
}