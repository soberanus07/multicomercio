<?php
namespace Micomercio\ProductNotify\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Micomercio\ProductNotify\Model\NotifyRequestFactory;

class Submit extends Action implements CsrfAwareActionInterface
{
    protected $formKeyValidator;
    protected $notifyRequestFactory;

    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        NotifyRequestFactory $notifyRequestFactory
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->notifyRequestFactory = $notifyRequestFactory;
        parent::__construct($context);
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    public function execute()
{
    $data = $this->getRequest()->getPostValue();

    // Verifica form key y campos obligatorios
    if (
        !$this->formKeyValidator->validate($this->getRequest()) ||
        empty($data['name']) ||
        empty($data['email'])
    ) {
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl('/');
    }

    // Validación Honeypot: si el campo oculto contiene algo, se asume intento de bot
    if (!empty($data['honey_field'])) {
        // Redirige silenciosamente sin guardar nada
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($this->_redirect->getRefererUrl());
    }

    $model = $this->notifyRequestFactory->create();
    $model->setData([
        'product_sku' => strip_tags($data['product_sku']),
        'name' => strip_tags($data['name']),
        'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
        'phone' => isset($data['phone']) ? strip_tags($data['phone']) : null,
    ]);
    $model->save();

    $this->messageManager->addSuccessMessage(__('¡Te avisaremos cuando el producto esté disponible!'));
    return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($this->_redirect->getRefererUrl());
}

}
