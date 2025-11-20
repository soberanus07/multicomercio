<?php
namespace Micomercio\ProductNotify\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class NotifyRequest extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('micomercio_product_notify', 'id');
    }
}
