<?php
namespace ChilexpressOficial\Chilexpress\Ui\Component\Listing\Column;
 
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\Escaper;
use \Magento\Framework\UrlInterface;
use \Magento\Shipping\Helper\Data;
 
class TrackingCode extends Column
{
 
    protected $_orderRepository;
    protected $_searchCriteria;
    protected $_context;
    protected $_escaper;
    private $urlBuilder;
    private $shippingHelperData;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteria,
        Escaper $escaper,
        UrlInterface $urlBuilder,
        Data $shippingHelperData,
        array $components = [], array $data = [])
    {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        $this->_escaper = $escaper;
        $this->urlBuilder  = $urlBuilder;
        $this->shippingHelperData = $shippingHelperData;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    public function getTrackingPopUrl($shipment)
    {
        return $this->urlBuilder->getUrl(
            'admin/order_shipment/view',
            [
                'shipment_id' =>  $shipment->getEntityId(),
                '_secure' => true,
            ]
        );
    }
    
    public function prepareDataSource(array $dataSource) // NOSONAR
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }
        
        foreach ($dataSource['data']['items'] as & $item) {
            $order  = $this->_orderRepository->get($item["entity_id"]);
            $order_id = $order->getEntityId();
            $codes = [];
            if( $order->getShipmentsCollection()->getSize() > 0 ) {
                foreach($order->getShipmentsCollection() as $shipment){
                    foreach($shipment->getTracksCollection() as $track) {
                        //echo $track->getTrackNumber()."\n";
                        
                        if (substr( $track->getCarrierCode(), 0, 11 ) === 'chilexpress' && $track->getTrackNumber()) {
                            $codes[] = '<a href="#" onclick="popWin(\''.$this->_escaper->escapeUrl($this->shippingHelperData->getTrackingPopupUrlBySalesModel($shipment)).'\', \'trackorder\',\'width=800,height=600,resizable=yes,scrollbars=yes\')">'.$track->getTrackNumber().'</a>';
                            //print_r(get_class_methods($track));
                        }
                        
                    }
                }
            }
            $extra = '<script> if(typeof popWin === "undefined") { function popWin(d,e,b){console.log("D",d,"E",e,"B",b); var e=window.open(d,e,b);e.focus()} }</script>';
            $item['tracking_code'] = implode(", ", $codes).$extra;
        }
    
        return $dataSource;
        
    }
}