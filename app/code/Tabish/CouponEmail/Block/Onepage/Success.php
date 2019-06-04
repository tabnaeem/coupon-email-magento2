<?php

namespace Tabish\CouponEmail\Block\Onepage;

class Success extends \Magento\Checkout\Block\Onepage\Success
{

	/**
	 * [getCouponCode description]
	 * @return [type] [description]
	 */
    public function getCouponCode()
    {
        return $this->_checkoutSession->getCoupon();
    }
}