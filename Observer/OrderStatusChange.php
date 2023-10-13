<?php


namespace Abhinay\TextLocal\Observer;

class OrderStatusChange implements \Magento\Framework\Event\ObserverInterface
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
            $orderInstance = $observer->getOrder();
            if ((!empty($orderInstance))) {
                if ($this->helper->getCustomerOrderStatus() != 0) {
                    $message = $this->helper->orderStatusMessageCustomer($orderInstance);
                    $number = $this->helper->getCustomerOrderNumber($orderInstance);
                    $this->helper->sendSms($number, $message);
                }
                if ($this->helper->getAdminOrderStatus() != 0) {
                    $messageAdmin = $this->helper->getAdminOrderStatusMessage($orderInstance);
                    $numberAdmin = $this->helper->getAdminOrderNumber();
                    $this->helper->sendSms($numberAdmin, $messageAdmin);
                }
            }
        }
    }
}
