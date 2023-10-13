<?php

namespace Abhinay\TextLocal\Helper;

/**
 * Class Data
 * @package Abhinay\TextLocal\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Abhinay\Core\Logger\Logger
     */
    protected $customLogger;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Abhinay\Core\Logger\Logger $customLogger
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\Order $order,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Abhinay\Core\Logger\Logger $customLogger
    )
    {
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->order = $order;
        $this->scopeConfig = $scopeConfig;
        $this->customLogger = $customLogger;
    }

    /**
     * @return mixed
     */
    public function getTextSmsStatus()
    {
        return $this->scopeConfig->getValue('textlocalsms/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getNewRegistrationStatus()
    {
        return $this->scopeConfig->getValue('textlocalsms/registration/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $customerInstance
     * @return array
     */
    public function getRegistrationDetail($customerInstance)
    {
        $details = [
            $customerInstance->getEmail(),
            $customerInstance->getFirstname() . ' ' . $customerInstance->getLastname(),
            $customerInstance->getFirstname(),
            $customerInstance->getMiddlename(),
            $customerInstance->getLastname()
        ];
        return $details;
    }

    /**
     * @param $customerInstance
     * @return mixed
     */
    public function getRegistrationMessage($customerInstance)
    {
        $variables = ['{{email}}', '{{name}}', '{{firstname}}', '{{middlename}}', '{{lastname}}'];
        $customerDetail = $this->getRegistrationDetail($customerInstance);
        $registrationMessage = $this->scopeConfig->getValue('textlocalsms/registration/message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return str_replace($variables, $customerDetail, $registrationMessage);
    }

    /**
     * @return mixed
     */
    public function getRegistrationMsgStatusAdmin()
    {
        return $this->scopeConfig->getValue('textlocalsms/registration/admin_msg', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $customerInstance
     * @return mixed
     */
    public function getRegistrationMessageAdmin($customerInstance)
    {
        $variableList = ['{{email}}', '{{name}}', '{{firstname}}', '{{middlename}}', '{{lastname}}'];
        $customerDetail = $this->getRegistrationDetail($customerInstance);
        $registrationMessage = $this->scopeConfig->getValue('textlocalsms/registration/admin_message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return str_replace($variableList, $customerDetail, $registrationMessage);
    }

    /**
     * @return mixed
     */
    public function getAdminNumber()
    {
        return $this->scopeConfig->getValue('textlocalsms/registration/admin_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getNewOrderNotificationCustomer()
    {
        return $this->scopeConfig->getValue('textlocalsms/order/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $orderInstance
     * @return mixed
     */
    public function getNewOrderMessageCustomer($orderInstance)
    {
        $dynamicVar = ['{{order_id}}', '{{email}}', '{{name}}', '{{firstname}}', '{{middlename}}', '{{lastname}}', '{{postal}}', '{{city}}'];
        $orderData = $this->getOrderData($orderInstance);
        $message = $this->getNewOrderMsgCustomer();
        return str_replace($dynamicVar, $orderData, $message);
    }

    /**
     * @return mixed
     */
    public function getNewOrderMsgCustomer()
    {
        return $this->scopeConfig->getValue('textlocalsms/order/message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $orderInstance
     * @return array
     */
    public function getOrderData($orderInstance)
    {
        $addressDetail = $orderInstance->getBillingAddress();
        $orderDataList = array($orderInstance->getIncrementId(),
            $addressDetail->getEmail(),
            $addressDetail->getFirstname() . ' ' . $addressDetail->getLastname(),
            $addressDetail->getFirstname(),
            $addressDetail->getMiddlename(),
            $addressDetail->getLastname(),
            $addressDetail->getPostcode(),
            $addressDetail->getCity()
        );
        return $orderDataList;
    }

    /**
     * @return mixed
     */
    public function getCustomerOrderStatus()
    {
        return $this->scopeConfig->getValue('textlocalsms/order_status/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $orderInstance
     * @return mixed
     */
    public function orderStatusMessageCustomer($orderInstance)
    {
        $dynamicVar = ['{{order_id}}', '{{order_status}}', '{{email}}', '{{name}}', '{{firstname}}', '{{middlename}}', '{{lastname}}', '{{postal}}'];
        $message = $this->getOrderStatusMessageCustomer($orderInstance);
        $orderStatusData = $this->getOrderData($orderInstance);
        return str_replace($dynamicVar, $orderStatusData, $message);
    }

    /**
     * @param $orderInstance
     * @return mixed
     */
    public function getOrderStatusMessageCustomer($orderInstance)
    {
        $orderState = $orderInstance->getState();
        $configPathVal = 'textlocalsms/order_status/' . $orderState . '_message_customer';
        return $this->scopeConfig->getValue($configPathVal, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $orderInstance
     * @return mixed
     */
    public function getCustomerOrderNumber($orderInstance)
    {
        $addressDetail = $orderInstance->getBillingAddress();
        $number = $addressDetail->getTelephone();
        return $number;
    }

    /**
     * @return mixed
     */
    public function getAdminOrderStatus()
    {
        return $this->scopeConfig->getValue('textlocalsms/order_status/admin_msg', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $orderInstance
     * @return mixed
     */
    public function getAdminOrderStatusMessage($orderInstance)
    {
        $dynamicVar = ['{{order_id}}', '{{order_status}}', '{{email}}', '{{name}}', '{{firstname}}', '{{middlename}}', '{{lastname}}', '{{postal}}'];
        $orderDetail = $this->getOrderData($orderInstance);
        $message = $this->getOrderStatusMsgAdmin($orderInstance);
        return str_replace($dynamicVar, $orderDetail, $message);
    }

    /**
     * @param $orderInstance
     * @return mixed
     */
    public function getOrderStatusMsgAdmin($orderInstance)
    {
        $state = $orderInstance->getState();
        $configPath = 'textlocalsms/order_status/' . $state . '_message_admin';
        return $this->scopeConfig->getValue($configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getAdminOrderNumber()
    {
        return $this->scopeConfig->getValue('textlocalsms/order_status/admin_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $orderInstance
     * @return mixed
     */
    public function getOrderCustomerNumber($orderInstance)
    {
        $addressDetail = $orderInstance->getBillingAddress();
        $telephoneNumber = $addressDetail->getTelephone();
        return $telephoneNumber;
    }

    /**
     * @return mixed
     */
    public function getNewOrderNotificationAdmin()
    {
        return $this->scopeConfig->getValue('textlocalsms/order/admin_msg', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $orderInstance
     * @return mixed
     */
    public function getNewOrderMessageAdmin($orderInstance)
    {
        $dynamicVar = ['{{order_id}}', '{{email}}', '{{name}}', '{{firstname}}', '{{middlename}}', '{{lastname}}', '{{postal}}', '{{city}}'];
        $orderDataList = $this->getOrderData($orderInstance);
        $message = $this->getAdminNewOrderMsg();
        return str_replace($dynamicVar, $orderDataList, $message);
    }

    /**
     * @return mixed
     */
    public function getOrderMessageDeliveryNumberAdmin()
    {
        return $this->scopeConfig->getValue('textlocalsms/order/admin_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getAdminNewOrderMsg()
    {
        return $this->scopeConfig->getValue('textlocalsms/order/admin_message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getSiteWebsiteId()
    {
        return $this->storeManager->getStore()->getWebsiteId();
    }

    /**
     * @return array
     */
    public function getTextLocalDetails()
    {
        $response = [];
        $response['apikey'] = $this->scopeConfig->getValue('textlocalsms/general/api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $response;
    }

    /**
     * @param $id
     * @return $this
     */
    public function getOrder($id)
    {
        return $this->order->load($id);
    }

    /**
     * @param $smsto
     * @param $message
     */
    public function sendSms($smsto, $message)
    {
        $textLocalDetails = $this->getTextLocalDetails();
        $apiKey = urlencode($textLocalDetails['apikey']);
        $sender = urlencode('TXTLCL');
        $data = array('apikey' => $apiKey, 'numbers' => $smsto, 'sender' => $sender, 'message' => $message);
        $ch = curl_init('https://api.textlocal.in/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $this->customLogger->info('Textlocal response for mobile number ' . $smsto);
        $this->customLogger->info('Textlocal API response : ' . json_encode($response));
    }

    /**
     * @return string
     */
    public function getCustomerRegistrationTemplate()
    {
        if ($this->scopeConfig->isSetFlag('textlocalsms/general/enable')) {
            $template = 'Abhinay_TextLocal::customer/registration.phtml';
        } else {
            $template = 'Magento_Customer::form/register.phtml';
        }
        return $template;
    }
}
