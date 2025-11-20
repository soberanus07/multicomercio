<?php
namespace ChilexpressOficial\Chilexpress\Ui\Component\Listing\Column;
 
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;

 
class TrackingActions extends Column
{
 
    protected $_orderRepository;
    protected $_searchCriteria;
    protected $_urlBuilder;
    protected $_context;

 
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteria,
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        array $components = [], array $data = [])
    {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        $this->_urlBuilder = $urlBuilder;
        $this->_context = $context;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource) // NOSONAR
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }
        
        foreach ($dataSource['data']['items'] as & $item) {
            $order  = $this->_orderRepository->get($item["entity_id"]);
            $shipment_id = false;
            if( $order->getShipmentsCollection()->getSize() > 0 ) {
                foreach($order->getShipmentsCollection() as $shipment){
                    foreach($shipment->getTracksCollection() as $track) {
                        if (substr( $track->getCarrierCode(), 0, 11 ) === 'chilexpress' && $track->getTrackNumber()) {
                            $shipment_id = $shipment->getEntityId();
                        }
                    }
                }
            }

            if($shipment_id){
                $view_shipment_url = $this->context->getUrl('sales/shipment/view', array('shipment_id' => $shipment_id));
                $print_label_url = $this->context->getUrl('adminhtml/order_shipment/printLabel', array('shipment_id' => $shipment_id));
                $item['tracking_actions'] =  "<a onclick='setLocation(\"$view_shipment_url\");' href='$view_shipment_url' target='_blank'>Ver Envío</a> &nbsp; <a onclick='setLocation(\"$print_label_url\");' href='$print_label_url'>PDF</a>";
            } else {
                $item['tracking_actions'] = '';
            }
        }

        return $dataSource;
    }
}