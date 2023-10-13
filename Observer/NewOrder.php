<?php

namespace Abhinay\TextLocal\Observer;

class NewOrder implements \Magento\Framework\Event\ObserverInterface
{
    protected $helper;

    protected $request;

    public function __construct(
        \Abhinay\TextLocal\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->request = $request;
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (($this->helper->getTextSmsStatus()) && ($this->helper->getTextSmsStatus() != 0)) {
            $orderId = $observer->getEvent()->getOrderIds();
            $orderInstance = $this->helper->getOrder($orderId['0']);
            if (!empty($orderInstance)) {
                if ($this->helper->getNewOrderNotificationCustomer() != 0) {
                    $message = $this->helper->getNewOrderMessageCustomer($orderInstance);
                    $number = $this->helper->getOrderCustomerNumber($orderInstance);
                    $this->helper->sendSms($number, $message);
                }
                if ($this->helper->getNewOrderNotificationAdmin() != 0) {
                    $messageAdmin = $this->helper->getNewOrderMessageAdmin($orderInstance);
                    $numberAdmin = $this->helper->getOrderMessageDeliveryNumberAdmin();
                    $this->helper->sendSms($numberAdmin, $messageAdmin);
                }
            }
        }
    }
}
