<?php

namespace Abhinay\TextLocal\Model\Config\Source;

class OtpType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'alphabet', 'label' => __('Alphabet OTP')],
            ['value' => 'numeric', 'label' => __('Numeric OTP')],
            ['value' => 'alpha-num', 'label' => __('Alpha-Numeric OTP')],
        ];
    }
}
