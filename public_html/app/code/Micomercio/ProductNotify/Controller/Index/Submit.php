<?php
namespace Micomercio\ProductNotify\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Micomercio\ProductNotify\Model\NotifyRequestFactory;
use Psr\Log\LoggerInterface;

class Submit extends Action implements CsrfAwareActionInterface
{
    protected $formKeyValidator;
    protected $notifyRequestFactory;
    protected $jsonFactory;
    protected $logger;

    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        NotifyRequestFactory $notifyRequestFactory,
        JsonFactory $jsonFactory,
        LoggerInterface $logger
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->notifyRequestFactory = $notifyRequestFactory;
        $this->jsonFactory = $jsonFactory;
        $this->logger = $logger;
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

        // 1. VALIDACIÓN DE HONEYPOT (website_url - nuevo)
        if (!empty($data['website_url'])) {
            $this->logBotAttempt('honeypot_website_url', $data);
            return $this->createFakeSuccessResponse();
        }

        // 2. VALIDACIÓN DE HONEYPOT (company - tu versión original)
        if (!empty($data['company'])) {
            $this->logBotAttempt('honeypot_company', $data);
            return $this->createFakeSuccessResponse();
        }

        // 3. VALIDACIÓN DE HONEYPOT (honey_field - por si acaso)
        if (!empty($data['honey_field'])) {
            $this->logBotAttempt('honeypot_honey_field', $data);
            return $this->createFakeSuccessResponse();
        }

        // 4. VALIDACIÓN DE TIEMPO
        if (isset($data['_timestamp'])) {
            $elapsedTime = time() - (int)$data['_timestamp'];
            
            // Muy rápido (menos de 3 segundos) = Bot
            if ($elapsedTime < 3) {
                $this->logBotAttempt('too_fast', $data);
                return $this->createFakeSuccessResponse();
            }
            
            // Muy lento (más de 1 hora) = Formulario expirado
            if ($elapsedTime > 3600) {
                return $this->jsonFactory->create()->setData([
                    'success' => false,
                    'message' => __('El formulario ha expirado. Por favor recarga la página.')
                ]);
            }
        }

        // 5. VALIDACIÓN BÁSICA DE CAMPOS
        if (!$this->formKeyValidator->validate($this->getRequest()) || 
            empty($data['name']) || 
            empty($data['email'])) {
            return $this->jsonFactory->create()->setData([
                'success' => false,
                'message' => __('Por favor completa todos los campos requeridos.')
            ]);
        }

        // 6. VALIDACIÓN DE EMAIL
        $email = trim($data['email']);
        if (!$this->validateEmail($email)) {
            $this->logBotAttempt('invalid_email', $data);
            return $this->createFakeSuccessResponse();
        }

        // 7. VALIDACIÓN DE NOMBRE (detectar patrones sospechosos)
        if ($this->isSuspiciousName($data['name'])) {
            $this->logBotAttempt('suspicious_name', $data);
            return $this->createFakeSuccessResponse();
        }

        // 8. VALIDACIÓN DE TELÉFONO (detectar SQL injection)
        if (isset($data['phone']) && !empty($data['phone']) && $this->isSuspiciousPhone($data['phone'])) {
            $this->logBotAttempt('suspicious_phone', $data);
            return $this->createFakeSuccessResponse();
        }

        // Si pasa todas las validaciones, guardar
        try {
            $model = $this->notifyRequestFactory->create();
            $model->setData([
                'product_sku' => strip_tags($data['product_sku']),
                'name' => strip_tags($data['name']),
                'email' => filter_var($email, FILTER_SANITIZE_EMAIL),
                'phone' => isset($data['phone']) ? $this->sanitizePhone($data['phone']) : null,
            ]);
            $model->save();

            return $this->jsonFactory->create()->setData([
                'success' => true,
                'message' => __('¡Te avisaremos cuando el producto esté disponible!')
            ]);
        } catch (\Exception $e) {
            $this->logger->error('ProductNotify Error: ' . $e->getMessage());
            return $this->jsonFactory->create()->setData([
                'success' => false,
                'message' => __('Ocurrió un error. Por favor intenta más tarde.')
            ]);
        }
    }

    /**
     * Valida email y bloquea dominios sospechosos
     */
    protected function validateEmail($email)
    {
        // Formato válido
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Bloquear emails de ejemplo/testing
        $blockedEmails = ['testing@example.com', 'test@test.com', 'example@example.com'];
        if (in_array(strtolower($email), $blockedEmails)) {
            return false;
        }

        // Bloquear dominios desechables
        $disposableProviders = [
            'mailinator.com', 'guerrillamail.com', 'temp-mail.org',
            '10minutemail.com', 'throwaway.email', 'fakeinbox.com',
            'example.com', 'test.com'
        ];
        
        $domain = substr(strrchr($email, "@"), 1);
        if (in_array(strtolower($domain), $disposableProviders)) {
            return false;
        }

        return true;
    }

    /**
     * Detecta nombres sospechosos
     */
    protected function isSuspiciousName($name)
    {
        $suspiciousPatterns = [
            'pHqghUme', 'test', 'admin', 'root', 'null',
            'select', 'union', 'drop', 'insert', 'delete'
        ];

        $nameLower = strtolower($name);
        foreach ($suspiciousPatterns as $pattern) {
            if (stripos($nameLower, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detecta teléfonos con SQL injection
     */
    protected function isSuspiciousPhone($phone)
    {
        $sqlPatterns = [
            'select', 'union', 'drop', 'insert', 'delete', 'update',
            'waitfor', 'sleep', 'benchmark', 'pg_sleep',
            'or ', 'and ', '--', '/*', '*/', 'xor', '@@',
            'concat', 'char(', 'chr(', '0x'
        ];

        $phoneLower = strtolower($phone);
        foreach ($sqlPatterns as $pattern) {
            if (stripos($phoneLower, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitiza el teléfono eliminando caracteres peligrosos
     */
    protected function sanitizePhone($phone)
    {
        // Solo permitir números, espacios, +, -, (, )
        return preg_replace('/[^0-9\s\+\-\(\)]/', '', $phone);
    }

    /**
     * Registra intentos de bot
     */
    protected function logBotAttempt($reason, $post)
    {
        $ip = $this->getRequest()->getClientIp();
        $userAgent = $this->getRequest()->getHeader('User-Agent');
        
        $this->logger->warning('ProductNotify Bot Attempt', [
            'reason' => $reason,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'email' => isset($post['email']) ? $post['email'] : 'N/A',
            'phone' => isset($post['phone']) ? substr($post['phone'], 0, 50) : 'N/A',
            'product_sku' => isset($post['product_sku']) ? $post['product_sku'] : 'N/A'
        ]);
    }

    /**
     * Respuesta falsa de éxito para confundir bots
     */
    protected function createFakeSuccessResponse()
    {
        return $this->jsonFactory->create()->setData([
            'success' => true,
            'message' => __('¡Te avisaremos cuando el producto esté disponible!')
        ]);
    }
}
