<?php
namespace Tabish\CouponEmail\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddExtraDataToTransport implements ObserverInterface
{
    /**
     * [$__checkoutSession description]
     * @var [type]
     */
    protected $__checkoutSession;

    /**
     * [__construct description]
     * @param \Magento\Checkout\Model\Session $checkoutSession [description]
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ){
        $this->__checkoutSession = $checkoutSession;
    }

    /**
     * [execute description]
     * @param  \Magento\Framework\Event\Observer $observer [description]
     * @return [type]                                      [description]
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $writer = new \Zend\Log\Writer\Stream( BP.'/var/log/orderEmail.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $transport = $observer->getEvent()->getTransport();
        
        $transport['CouponEmail'] = '10% Coupon for your next order: ' . $this->createCouponCode( $observer );

        $logger->info("AddExtraDataToTransport  Coupon Code: ".$transport['CouponEmail'] );
    }


    protected function createCouponCode( $observer )
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $state = $objectManager->get('Magento\Framework\App\State');

        $orderIds = $observer->getEvent()->getOrderIds(); // Get Current Order ID


        $coupon['name'] = '10% Off CouponCode';
        $coupon['desc'] = 'Discount On order';
        $coupon['start'] = date('Y-m-d');
        $coupon['end'] = '';
        $coupon['max_redemptions'] = 1;
        $coupon['discount_type'] ='by_percent';
        $coupon['discount_amount'] = 10;
        $coupon['flag_is_free_shipping'] = 'no';
        $coupon['redemptions'] = 1;

        $random_id_length = 6;

        $rnd_id = @crypt( uniqid( rand(),1 ) );
        $rnd_id = strip_tags(stripslashes($rnd_id)); 
        $rnd_id = str_replace(".","",$rnd_id); 
        $rnd_id = strrev(str_replace("/","",$rnd_id));  
        $rnd_id = substr($rnd_id,0,$random_id_length);
        $prefix_length = 3; 
        $prefix = substr('CHO',0,$prefix_length);

        $coupon['code'] = $prefix . '-' . $rnd_id . ( count($orderIds) ? '-' . $orderIds[0] : null ); // autogenetated but i am hard coding for testing purposes

        $this->__checkoutSession->setCoupon( $coupon['code'] );

        $shoppingCartPriceRule = $objectManager->create('Magento\SalesRule\Model\Rule');
        $shoppingCartPriceRule->setName($coupon['name'])
            ->setDescription($coupon['desc'])
            ->setFromDate($coupon['start'])
            ->setToDate($coupon['end'])
            ->setUsesPerCustomer($coupon['max_redemptions'])
            ->setCustomerGroupIds(array('0','1','2','3',))
            ->setIsActive(1)
            ->setSimpleAction($coupon['discount_type'])
            ->setDiscountAmount($coupon['discount_amount'])
            ->setDiscountQty(1)
            ->setApplyToShipping($coupon['flag_is_free_shipping'])
            ->setTimesUsed($coupon['redemptions'])
            ->setWebsiteIds(array('1'))
            ->setCouponType(2)
            ->setCouponCode( $this->__checkoutSession->getCoupon() )
            ->setUsesPerCoupon(NULL);
        $shoppingCartPriceRule->save();

        return $this->__checkoutSession->getCoupon();
    }
}