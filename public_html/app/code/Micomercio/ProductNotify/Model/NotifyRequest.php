<?php
namespace Micomercio\ProductNotify\Model;

use Magento\Framework\Model\AbstractModel;

class NotifyRequest extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Micomercio\ProductNotify\Model\ResourceModel\NotifyRequest::class);
    }
}
