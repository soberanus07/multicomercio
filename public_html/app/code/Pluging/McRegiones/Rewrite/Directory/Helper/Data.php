<?php

namespace Pluging\McRegiones\Rewrite\Directory\Helper;


class Data extends \Magento\Directory\Helper\Data
{
   protected $disallowed = [
    'YungayTest',
    'Algarrobo',
    'Ancud',
    'Angol'
    ];

    /**
     * Retrieve regions data json
     *
     * @return string
     */
    public function getRegionJson()
    {
        \Magento\Framework\Profiler::start('TEST: ' . _METHOD, ['group' => 'TEST', 'method' => __METHOD_]);
        if (!$this->_regionJson) {
            $cacheKey = 'DIRECTORY_REGIONS_JSON_STORE' . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $regions = $this->getRegionData();
                if(isset($regions['CL'])) {
                    $regions['CL'] = array_filter($regions['CL'], function ($region) {
                        if (isset($region['name']))
                            return !in_array($region['name'], $this->disallowed);
                        return true;
                    });
                }
                $json = $this->jsonHelper->jsonEncode($regions);
                if ($json === false) {
                    $json = 'false';
                }
                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->_regionJson = $json;
        }

        \Magento\Framework\Profiler::stop('TEST: ' . _METHOD_);
        return $this->_regionJson;
    }
}