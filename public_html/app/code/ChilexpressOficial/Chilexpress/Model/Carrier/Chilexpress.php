<?php
namespace ChilexpressOficial\Chilexpress\Model\Carrier;


use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Helper;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;

class Chilexpress extends AbstractCarrier implements CarrierInterface
{
    /**
     * Carrier helper
     *
     * @var Helper\Carrier
     */
    protected $_carrierHelper;

    /**
     * @var \ChilexpressOficial\Chilexpress\Helper\Data
     */
    protected $_helperData;

    /**
    * @var string
    */
    protected $_code = 'chilexpress';

    /**
     * @var bool
     */
    protected $_isFixed = false;

    /**
     * @var ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $rateMethodFactory;

    /**
     * @var \Magento\Shipping\Model\Tracking\Result\StatusFactory
     */

    protected $_trackStatusFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $customerSession;

    protected $serviceTypeCode = -1;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param StatusFactory $trackStatusFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param Helper\Carrier $carrierHelper
     * @param array $data
     */
    public function __construct( // NOSONAR
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, // NOSONAR
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory, // NOSONAR
        \Psr\Log\LoggerInterface $logger,// NOSONAR
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory, // NOSONAR
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,// NOSONAR
        \Magento\Customer\Model\Session $customerSession,// NOSONAR
        \ChilexpressOficial\Chilexpress\Helper\Data $helperData,// NOSONAR
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,// NOSONAR
        Helper\Carrier $carrierHelper,// NOSONAR
        array $data = []// NOSONAR
    ) { // NOSONAR
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->trackStatusFactory = $trackStatusFactory;
        $this->customerSession = $customerSession;
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->_helperData = $helperData;
        $this->_trackStatusFactory = $trackStatusFactory;
        $this->_carrierHelper = $carrierHelper;
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request) // NOSONAR
    {

        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $packageWeight = $request->getPackageWeight();
        if ( !$packageWeight) {
            $packageWeight = 1;
        }

        $length  = $request->getPackageDepth();
        $width  = $request->getPackageWidth();
        $height  = $request->getPackageHeight();

        if (!$height) {
            $height = 1;
        }

        if (!$length) {
            $length = 1;
        }

        if (!$width) {
            $width = 1;
        }

        $json_response = $this->_helperData->getCotizacion(
                        $request->getDestCity(),
                        $this->_helperData->getOrigenConfig('comuna_origen'),
                        $packageWeight, $height,$width, $length, 100);

        if (!$json_response || $json_response->statusCode != 0) {
            return false; // Algo fallo al obtener la cotizacion
        }
        $precio_chilexpress = 0;
        $opciones_de_envio = $json_response->data->courierServiceOptions;
        foreach($opciones_de_envio as $opcion) {
            if($opcion->serviceTypeCode == $this->serviceTypeCode)
            {
                $precio_chilexpress = intval($opcion->serviceValue);
            }
        }
        
        if ($precio_chilexpress == 0) {
            return false; // No hay ningun tipo de envio válido disponible
        }

        /*********************************************/
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();
       
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $shippingCost = $precio_chilexpress;

        $method->setPrice($shippingCost);
        $method->setCost($shippingCost);
        $result->append($method);

        

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * Check if carrier has shipping tracking option available
     *
     * @return bool
     */
    public function isTrackingAvailable() // NOSONAR
    {
        return true;
    }


     /**
     * Check if carrier has shipping label option available
     *
     * @return bool
     */
    public function isShippingLabelsAvailable() // NOSONAR
    {
        return true;
    }

    /**
     * Is state province required
     *
     * @return bool
     */
    public function isStateProvinceRequired() // NOSONAR
    {
        return true;
    }

    /**
     * Check if city option required
     *
     * @return bool
     */
    public function isCityRequired() // NOSONAR
    {
        return true;
    }

    /**
     * Determine whether zip-code is required for the country of destination
     *
     * @param string|null $countryId
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isZipCodeRequired($countryId = null)
    {
        return false;
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param \Magento\Framework\DataObject $request
     * @return \Magento\Framework\DataObject
     */
    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
         // this function is required by magento but is not necesary to implement
    }

    /**
     * Do request to shipment
     *
     * @param \Magento\Shipping\Model\Shipment\Request $request
     * @return array|\Magento\Framework\DataObject
     */
    public function requestToShipment($request) // NOSONAR
    {
        $PARAMS_KEY = 'params';
        $LENGTH_KEY = 'length';
        $WIDTH_KEY = 'width';
        $HEIGHT_KEY = 'height';
        $WEIGHT_KEY = 'weight';
        $WEIGHT_UNITS_KEY = 'weight_units';
        $DIMENSIONS_UNITS_KEY = 'dimension_units';
        $CUSTOMS_VALUE_KEY = 'customs_value';
        $DEFAULT_STRING = 'DEFAULT';
        $packages = $request->getPackages();
        if (!is_array($packages) || !$packages) {
            throw new \Magento\Framework\Exception\LocalizedException(__('No packages for request'));
        }

        $request->setOrigCountryId('CL');
        /************************/
        $customsValue = 0;
        $packageWeight = 0;
        $packages = $request->getPackages();
        $paquetes = [];
        $order_id = $request->getOrderShipment()->getOrder()->getId();
        $index = 1;

        $deliveryCodes = array(
            "chilexpressdhs_chilexpressdhs" => 3,
            "chilexpressdhss_chilexpressdhss" => 4,
            "chilexpresstercer_chilexpresstercer" => 5
        );
        $deliveryCode = $deliveryCodes[$request->getOrderShipment()->getOrder()->getShippingMethod()];

        foreach ($packages as &$piece) {
            $params = $piece[$PARAMS_KEY];
            $items = $piece['items'];

            $packageWeight = $piece[$PARAMS_KEY][$WEIGHT_KEY];

            $items_names = array();
            foreach ($items as $kid=>$item) { // NOSONAR
                $items_names[] = $item['name'] ." x ".$item['qty'];
            }

            if ($packageWeight != \Zend_Measure_Weight::KILOGRAM) {
                $packageWeight = round(100*(
                    $this->_carrierHelper->convertMeasureWeight(
                        (float)$piece[$PARAMS_KEY][$WEIGHT_KEY],
                        $piece[$PARAMS_KEY][$WEIGHT_UNITS_KEY],
                        \Zend_Measure_Weight::KILOGRAM
                    )
                ))/100;
                $piece[$PARAMS_KEY][$WEIGHT_UNITS_KEY] = \Zend_Measure_Weight::KILOGRAM;
            }

            $length = $piece[$PARAMS_KEY][$LENGTH_KEY];
            $width = $piece[$PARAMS_KEY][$WIDTH_KEY];
            $height =  $piece[$PARAMS_KEY][$HEIGHT_KEY];
            if ($piece[$PARAMS_KEY][$DIMENSIONS_UNITS_KEY] != \Zend_Measure_Length::CENTIMETER) {
                $length = round(
                    $this->_carrierHelper->convertMeasureDimension(
                        (float)$piece[$PARAMS_KEY][$LENGTH_KEY],
                        $piece[$PARAMS_KEY][$DIMENSIONS_UNITS_KEY],
                        \Zend_Measure_Length::CENTIMETER
                    )
                );
                $width = round(
                    $this->_carrierHelper->convertMeasureDimension(
                        (float)$piece[$PARAMS_KEY][$WIDTH_KEY],
                        $piece[$PARAMS_KEY][$DIMENSIONS_UNITS_KEY],
                        \Zend_Measure_Length::CENTIMETER
                    )
                );
                $height = round(
                    $this->_carrierHelper->convertMeasureDimension(
                        (float)$piece[$PARAMS_KEY][$HEIGHT_KEY],
                        $piece[$PARAMS_KEY][$DIMENSIONS_UNITS_KEY],
                        \Zend_Measure_Length::CENTIMETER
                    )
                );
                $piece[$PARAMS_KEY][$DIMENSIONS_UNITS_KEY] = \Zend_Measure_Length::CENTIMETER;
            }

            $piece[$PARAMS_KEY][$HEIGHT_KEY] = $height;
            $piece[$PARAMS_KEY][$LENGTH_KEY] = $length;
            $piece[$PARAMS_KEY][$WIDTH_KEY] = $width;

            $piece[$PARAMS_KEY][$DIMENSIONS_UNITS_KEY] = 'CENTIMETER';
            $piece[$PARAMS_KEY][$WEIGHT_KEY] = $packageWeight;
            $piece[$PARAMS_KEY][$WEIGHT_UNITS_KEY] = 'KILOGRAM';
            

            
            $paquetes[] =  array(
                $WEIGHT_KEY => $packageWeight, // Peso en kilogramos
                "height"=> $height, // Altura en centímetros
                "width"=> $width, // Ancho en centímetros
                $LENGTH_KEY=> $length,  // Largo en centímetros
                "serviceDeliveryCode"=> $deliveryCode, // Código del servicio de entrega, obtenido de la API Cotización
                "productCode"=> "3", // Código del tipo de roducto a enviar; 1 = Documento, 3 = Encomienda
                "deliveryReference"=> "ORDEN-".$order_id, // Referencia que permite identificar el envío por parte del cliente.
                "groupReference"=> "ORDEN-".$order_id."-GRUPO-".$index, // Referencia que permite identificar un grupo de bultos que va por parte del cliente.
                "declaredValue"=> $piece[$PARAMS_KEY][$CUSTOMS_VALUE_KEY], // Valor declarado del producto
                "declaredWorth"=> $piece[$PARAMS_KEY][$CUSTOMS_VALUE_KEY], // Valor declarado del producto
                "declaredContent"=> $this->_helperData->getTiendaConfig('articulos_tienda'), // Tipo de producto enviado; 1 = Moda, 2 = Tecnologia, 3 = Repuestos, 4 = Productos medicos, 5 = Otros
                "descriptionContent" => implode(';', $items_names),
                "extendedCoverageAreaIndicator"=> false, // Indicador de contratación de cobertura extendida 0 = No, 1 = Si
                "receivableAmountInDelivery"=> 1000 // Monto a cobrar, en caso que el cliente tenga habilitada esta opción.
              );

            $customsValue += $piece[$PARAMS_KEY][$CUSTOMS_VALUE_KEY];
            $packageWeight += $piece[$PARAMS_KEY][$WEIGHT_KEY];
            $index++;
        }

        $request->setPackages($packages)
                ->setPackageWeight($packageWeight)
                ->setPackageValue($customsValue)
                ->setValueWithDiscount($customsValue)
                ->setPackageCustomsValue($customsValue)
                ->setFreeMethodWeight(0);

        /********/

        $payload_header = array(
                "certificateNumber" => 0, //Número de certificado, si no se ingresa se creará uno nuevo
                "customerCardNumber"=> $this->_helperData->getOrigenConfig('numero_tcc_origen'), // Número de Tarjeta Cliente Chilexpress (TCC)
                "countyOfOriginCoverageCode"=> $this->_helperData->getOrigenConfig('comuna_origen'), // Comuna de origen
                "labelType"=> 2, // Imagen
                "marketplaceRut"=> $this->_helperData->getRemitenteConfig("rut_marketplace_remitente"), // Rut asociado al Marketplace
                "sellerRut"=> $DEFAULT_STRING, // Rut asociado al Vendedor
                "sourceChannel" => 7 // magento se identifica en el sistema como 7
            );

        $payload_address_devolucion = array(
                "addressId"=> 0,
                "countyCoverageCode"=> $this->_helperData->getDevolucionConfig('comuna_devolucion'),
                "streetName"=> $this->_helperData->getDevolucionConfig('calle_devolucion'),
                "streetNumber"=> $this->_helperData->getDevolucionConfig('numero_calle_devolucion'),
                "supplement"=> $this->_helperData->getDevolucionConfig('complemento_devolucion'),
                "addressType"=> "DEV",
                "deliveryOnCommercialOffice"=> false,
                "observation"=> $DEFAULT_STRING
            );

        $payload_contact_devolucion = array(
                "name"=> $this->_helperData->getRemitenteConfig('nombre_remitente'),
                "phoneNumber"=> $this->_helperData->getRemitenteConfig('telefono_remitente'),
                "mail"=> $this->_helperData->getRemitenteConfig('email_remitente'),
                "contactType"=> "R" // Tipo de contacto; Destinatario (D), Remitente (R)
            );
        $streetLine3 = $request->getOrderShipment()->getShippingAddress()->getStreetLine(3);
        $payload_address_destino = [
                "addressId" => 0,
                "countyCoverageCode"=> $request->getRecipientAddressCity(), // Cobertura de destino obtenido por la API Consultar Coberturas
                "streetName"=> $request->getRecipientAddressStreet1(), // Nombre de la calle
                "streetNumber"=> $request->getRecipientAddressStreet2(), // Numeración de la calle
                "supplement"=> $streetLine3, // Información complementaria de la dirección
                "addressType"=> "DEST", // Tipo de dirección; DEST = Entrega, DEV = Devolución.
                "deliveryOnCommercialOffice"=> false, // Indicador si es una entrega en oficina comercial (true) o entrega en domicilio (false)
                "commercialOfficeId"=> "",
                "observation"=> $DEFAULT_STRING // Observaciones adicionales
            ];

        $payload_contact_destino = array(
                "name"=> $request->getRecipientContactPersonName(),
                "phoneNumber"=> $request->getRecipientContactPhoneNumber(),
                "mail"=> $request->getRecipientEmail(),
                "contactType"=> "D" // Tipo de contacto; Destinatario (D), Remitente (R)
            );

            $payload = [
                "header" => $payload_header,
                "details" => [
                      [
                          "addresses" => array(
                              $payload_address_destino,
                              $payload_address_devolucion
                          ),
                          "contacts" => array( // Se debe entregar un detalle para los datos de contacto del destinatario (D) y otro para los del remitente (R)
                              $payload_contact_devolucion,
                              $payload_contact_destino
                          ),
                          "packages" => $paquetes
                      ]
                ]
            ];
        
        // echo json_encode($payload);
        
        $json_response = $this->_helperData->generarOT($payload);
        $data = [];
        if ($json_response->statusCode != 0) {
            return null;
        }

        /************************/
        foreach($json_response->data->detail as $detail)
        {
            $data[] = [
                'tracking_number' => $detail->transportOrderNumber,
                'label_content' => base64_decode($detail->label->labelData),
            ];
        }

        $response = new \Magento\Framework\DataObject(['info' => $data]);
        if(!empty($data)){
            $request->setMasterTrackingId($data[0]['tracking_number']);
        }
        return $response;
    }

    
    public function getTrackingInfo($trackingNumber)
    {
        $tracking = $this->_trackStatusFactory->create();
        $url = 'https://www.chilexpress.cl/Views/ChilexpressCL/Resultado-busqueda.aspx?DATA=' . $trackingNumber; // this is the tracking URL of stamps.com, replace this with your's
        $tracking->setData([
            'carrier' => $this->_code,
            'carrier_title' => $this->getConfigData('title'),
            'tracking' => $trackingNumber,
            'url' => $url
        ]);
        return $tracking;
    }

    
}
