<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ChilexpressOficial\Chilexpress\Block\Tracking;

use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

/**
 * Tracking popup
 *
 * @api
 * @since 100.0.2
 */
class Popup extends \Magento\Shipping\Block\Tracking\Popup
{
 
    /**
     * @var \ChilexpressOficial\Chilexpress\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Shipping\Model\Tracking\Result\StatusFactory
     */
    private $trackingResultFactory;


    private $_shipmentRepository;

    /**
     * Popup constructor.
     *
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory         $trackingResultFactory
     * @param \Magento\Framework\View\Element\Template\Context              $context
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter
     * @param array                                                         $data
     */
    public function __construct(
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackingResultFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \ChilexpressOficial\Chilexpress\Helper\Data $helperData,
        \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        array $data = []
    ) {
        $this->trackingResultFactory = $trackingResultFactory;
        $this->_helperData = $helperData;
        $this->_shipmentRepository = $shipmentRepository;
        parent::__construct($context, $registry, $dateTimeFormatter, $data);
    }

    /**
     * Retrieve array of tracking info
     *
     * @return array
     */
    public function getTrackingInfo() // NOSONAR
    {
        $CHILEXPRESS_KEY = 'chilexpress';
        $results = parent::getTrackingInfo();  

        foreach ($results as $shipId => $result) : 
            if ($shipId == 0) {
                break;
            }
            $shipment  = $this->_shipmentRepository->get($shipId);
            $order_id = $shipment->getOrder()->getEntityId();
            foreach ($result as $counter => $track) :
                $tracking_id = '';
                if (!is_array($track)) {
                    $data = $track->getData();
                    $tracking_id = $data["tracking"];
                } else{
                    $data = [];
                    $tracking_id = $track["number"];
                }
                $tracking_ot = $this->_helperData->obtenerEstadoOt($tracking_id, $order_id);
                if ($tracking_ot->statusCode == 0) {
                    $data[$CHILEXPRESS_KEY] = $tracking_ot->data;
                    if(is_array($track)){
                        $results[$shipId][$counter][$CHILEXPRESS_KEY] = $tracking_ot->data;
                    } else {
                        $data[$CHILEXPRESS_KEY] = $tracking_ot->data;
                    }
                } else {
                    if(is_array($track)){
                        $results[$shipId][$counter][$CHILEXPRESS_KEY] = false; //
                    } else {
                        $data[$CHILEXPRESS_KEY] = false;
                    }
                }
                if (!is_array($track)) {
                    $track->setData($data);
                }
            endforeach;
        endforeach;
        return $results;
    }
}
