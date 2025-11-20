<?php
namespace ChilexpressOficial\Chilexpress\Controller\Geo;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Directory\Model\ResourceModel\Region\Collection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;

class Get extends Action
{
    /**
     * @var ResultFactory
     */
    protected $_resultFactory;

    /**
     * @var \ChilexpressOficial\Chilexpress\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $_httpClientFactory;

    protected $_regionFactory;

    /**
     * Get constructor.
     * @param Action\Context $context
     * @param ResultFactory $resultFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \ChilexpressOficial\Chilexpress\Helper\Data $helper
     */
    public function __construct(
        Action\Context $context,
        ResultFactory $resultFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \ChilexpressOficial\Chilexpress\Helper\Data $helper
    )
    {
        parent::__construct($context);
        $this->_helper  = $helper;
        $this->_resultFactory = $resultFactory;
        $this->_regionFactory = $regionFactory;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $region_id = intval($params['region']);
        $regionObj = $this->_regionFactory->create()->load($region_id);
        $region = $regionObj->getCode();

        $url = 'https://testservices.wschilexpress.com/georeference/api/v1.0/coverage-areas?RegionCode=' . $region . '&type=1';

        try {
            $httpHeaders = new \Zend\Http\Headers();
            $httpHeaders->addHeaders([
                "Ocp-Apim-Subscription-Key" => "134b01b545bc4fb29a994cddedca9379",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]);

            $request = new \Zend\Http\Request();
            $request->setHeaders($httpHeaders);
            $request->setUri($url);
            $request->setMethod(\Zend\Http\Request::METHOD_GET);

            $client = new \Zend\Http\Client();
            $options = [
                'adapter'   => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
                'maxredirects' => 0,
                'timeout' => 10
            ];
            $client->setOptions($options);

            $response = $client->send($request);
            if (!$response->isSuccess()) {
                throw new \Exception("API call failed");
            }

            $json_response = json_decode($response->getBody());
            $mapear_comuna = function ($item) {
                return array("id" => $item->countyCode, "name" => $item->coverageName);
            };
            $o = array_map($mapear_comuna, $json_response->coverageAreas);
        } catch (\Exception $e) {
            // Fallback: leer archivo local si existe
            $cacheFile = BP . '/var/chilexpress_geo_cache.json';
            $o = [];
            if (file_exists($cacheFile)) {
                $localData = json_decode(file_get_contents($cacheFile), true);
                if (isset($localData[$region])) {
                    $o = $localData[$region];
                }
            }
        }

        $resultJson = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData(array("status" => "ok", "region" => $region, "data" => $o));
        return $resultJson;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
