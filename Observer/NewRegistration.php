<?php

namespace Abhinay\TextLocal\Observer;

class NewRegistration implements \Magento\Framework\Event\ObserverInterface
{
    protected $customerRepository;

    protected $helper;

    protected $request;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerInstanceRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Abhinay\TextLocal\Helper\Data $helper
    )
    {
        $this->request = $request;
        $this->customerRepository = $customerInstanceRepository;
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $postData = (array)$this->request->getPost();
        $customerInstance = $observer->getEvent()->getCustomer();
        if ((array_key_exists('mobile_number', $postData)) || (array_key_exists('telephone', $postData))) {
            if (!empty($postData['mobile_number'])) {
                $this->saveCustomerNumber($postData['mobile_number'], $customerInstance);
            } else {
                $this->saveCustomerNumber($postData['telephone'], $customerInstance);
            }
        }
        if (($this->helper->getTextSmsStatus()) && ($this->helper->getTextSmsStatus() != 0)) {
            if ($this->helper->getNewRegistrationStatus() != 0) {
                $messageCustomer = $this->helper->getRegistrationMessage($customerInstance);
                $mobile = '';
                if (array_key_exists('mobile_number', $postData)) {
                    $mobile = $postData['mobile_number'];
                }
                if (!empty($mobile)) {
                    $this->helper->sendSms($mobile, $messageCustomer);
                }
            }
            if ($this->helper->getRegistrationMsgStatusAdmin() != 0) {
                $numberAdmin = $this->helper->getAdminNumber();
                $messageAdmin = $this->helper->getRegistrationMessageAdmin($customerInstance);
                $this->helper->sendSms($numberAdmin, $messageAdmin);
            }
        }
    }

    public function saveCustomerNumber($mobileNumber, $customerInstance)
    {
        $id = $this->helper->getSiteWebsiteId();
        $customer = $this->customerRepository->get($customerInstance->getEmail(), $id);
        $customer->setCustomAttribute('mobile', $mobileNumber);
        $this->customerRepository->save($customer);
    }
}
