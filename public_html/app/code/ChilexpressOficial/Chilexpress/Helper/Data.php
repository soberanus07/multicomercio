<?php

namespace ChilexpressOficial\Chilexpress\Helper;

use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
    * @var ResultFactory
    */
    protected $_resultFactory;

    protected $_httpClientFactory;

    protected $_baseUrl;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        ResultFactory $resultFactory,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
    ) {
        parent::__construct($context);
        $this->_resultFactory = $resultFactory;
        $this->_httpClientFactory   = $httpClientFactory;
        

        if ($this->getModulosConfig('ambiente') == 'production') {
            $this->_baseUrl = 'https://services.wschilexpress.com/';
        } else if($this->getModulosConfig('ambiente') == 'staging') {
            $this->_baseUrl = 'https://testservices.wschilexpress.com/';
        } else {
            $this->_baseUrl = 'https://devservices.wschilexpress.com/';
        }
        
    }

    public function getDescripcionArticulos()
    {
        $url = 'https://services.wschilexpress.com/agendadigital/api/v3/Cotizador/GetArticulos';
        $client = $this->_httpClientFactory->create();
        $client->setUri($url);
        $client->setConfig(['timeout' => 1000]); // NOSONAR
        $client->setHeaders([
            "Ocp-Apim-Subscription-Key: ".'9c853753ce314c81934c4f966dad7755', // NOSONAR
            'Accept: application/json', // NOSONAR
            'Content-Type: application/json' // NOSONAR
        ]);

        $client->setMethod(\Zend_Http_Client::GET);

        try {
            $data = json_decode($client->request()->getBody());
        } catch (\Exception $e) {
            return false;
        }
        $descripciones = array();
        foreach ($data->ListArticulos as $entry) {
            $descripciones[$entry->Codigo] = $entry->Glosa;
        }
        return $descripciones;
    }

    public function getComunasByRegion($regionCode)
    {
        $url = $this->_baseUrl.'georeference/api/v1.0/coverage-areas?RegionCode='.$regionCode.'&type=1';
        $client = $this->_httpClientFactory->create();
        $client->setUri($url);
        $client->setConfig(['timeout' => 1000]); // NOSONAR
        $client->setHeaders([
            "Ocp-Apim-Subscription-Key: ".$this->getModulosConfig('api_key_georeferencia_value'),  // NOSONAR
            'Accept: application/json',  // NOSONAR
            'Content-Type: application/json'  // NOSONAR
        ]);

        $client->setMethod(\Zend_Http_Client::GET);

        try {
            $json_response = json_decode($client->request()->getBody());
        } catch (\Exception $e) {
            return false;
        }        
        $mapear_comuna = function ($item) {
            return array("id"=>$item->countyCode, "name" => $item->coverageName);
        };
        return array_map($mapear_comuna, $json_response->coverageAreas);
    }

    public function getCotizacion($comuna_origen, $comuna_destino, $weight, $height,$width, $length, $declaredWorth)
    {
        $payload = array(
            "originCountyCode" =>	$comuna_origen,
            "destinationCountyCode" => $comuna_destino,
            "package" => array(
                "weight" =>	$weight,
                "height" =>	$height,
                "width" =>	$width,
                "length" =>	$length
            ),
            "productType" => 3,
            "contentType" => 1,
            "declaredWorth" => $declaredWorth,
            "deliveryTime" => 0
        );
        
        $url = $this->_baseUrl.'rating/api/v1.0/rates/courier';
        $client = $this->_httpClientFactory->create();
        $client->setUri($url);
        $client->setConfig(['timeout' => 1000]);  // NOSONAR
        $client->setHeaders([
            "Ocp-Apim-Subscription-Key: ".$this->getModulosConfig('api_key_cotizador_value'),  // NOSONAR
            'Accept: application/json',  // NOSONAR
            'Content-Type: application/json'  // NOSONAR
        ]);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($payload));
        
        try {
            $responseBody = $client->request()->getBody();
        } catch (\Exception $e) {
            return false;
        }
        
        return json_decode($responseBody);
    }

    public function generarOT($payload)
    {
        $url = $this->_baseUrl.'transport-orders/api/v1.0/transport-orders';
        $client = $this->_httpClientFactory->create();
        $client->setUri($url);
        $client->setConfig(['timeout' => 1000]);
        $client->setHeaders([
            "Ocp-Apim-Subscription-Key: ".$this->getModulosConfig('api_key_generacion_ot_value'),  // NOSONAR
            'Accept: application/json',  // NOSONAR
            'Content-Type: application/json'  // NOSONAR
        ]);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($payload));

        try {
            $responseBody = $client->request()->getBody();
        } catch (\Exception $e) {
            return false;
        }
        
        return json_decode($responseBody);
    }

    public function obtenerEstadoOt($trackingId, $orderId) {
        $url = $this->_baseUrl.'transport-orders/api/v1.0/tracking';
        $payload = array(
            "reference"=> "ORDEN-".$orderId,
            "transportOrderNumber"=> intval($trackingId),
            "rut"=> $this->getRemitenteConfig("rut_marketplace_remitente"),
            "showTrackingEvents" => 1
        );

        $client = $this->_httpClientFactory->create();
        $client->setUri($url);
        $client->setConfig(['timeout' => 1000]);
        $client->setHeaders([
            "Ocp-Apim-Subscription-Key: ".$this->getModulosConfig('api_key_generacion_ot_value'),  // NOSONAR
            'Accept: application/json',  // NOSONAR
            'Content-Type: application/json'  // NOSONAR
        ]);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($payload));

        try {
            $responseBody = $client->request()->getBody();
        } catch (\Exception $e) {
            return false;
        }

        return json_decode($responseBody);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getModulosConfig($code, $storeId = null)
    {
        return $this->getConfigValue('chilexpress_modulos/chilexpress_modulos_group/'. $code, $storeId);
    }
    public function getOrigenConfig($code, $storeId = null)
    {
        return $this->getConfigValue('chilexpress_origen/chilexpress_origen_group/'. $code, $storeId);
    }
    public function getRemitenteConfig($code, $storeId = null)
    {
        return $this->getConfigValue('chilexpress_remitente/chilexpress_remitente_group/'. $code, $storeId);
    }
    public function getDevolucionConfig($code, $storeId = null)
    {
        return $this->getConfigValue('chilexpress_devolucion/chilexpress_devolucion_group/'. $code, $storeId);
    }
    public function getTiendaConfig($code, $storeId = null)
    {
        return $this->getConfigValue('chilexpress_tienda/chilexpress_tienda_group/'. $code, $storeId);
    }
}