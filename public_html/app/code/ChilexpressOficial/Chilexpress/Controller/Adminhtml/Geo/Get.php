<?php
namespace ChilexpressOficial\Chilexpress\Controller\Adminhtml\Geo;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\HTTP\ZendClientFactory;


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

    /**
     * Get constructor.
     * @param Action\Context $context
     * @param ResultFactory $resultFactory
     * @param \ChilexpressOficial\Chilexpress\Helper\Data $helper
     */
    public function __construct(
        Action\Context $context,
        ResultFactory $resultFactory,
        \ChilexpressOficial\Chilexpress\Helper\Data $helper,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
        
    )
    {
        parent::__construct($context);
        $this->_helper  = $helper;
        $this->_resultFactory = $resultFactory;
        $this->_httpClientFactory = $httpClientFactory;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $region = $params['region'];
        $comunas = $this->_helper->getComunasByRegion($region);
        $resultJson = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData(array("status"=>"ok","region"=>$region , "data"=>$comunas  ));
        return $resultJson;
    }

    protected function _isAllowed()
    {
        return true;
    }

}