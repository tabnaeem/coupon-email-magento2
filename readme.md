# Magento 2 - Coupon Email

This Extension will allow magento 2 Coupon Email send on order success, this extension hook OnePage Checkout Success overriding to showing user have successfully get the coupon and when user get thier order email the coupon code also add if you add this code into your email order new template: {{var CouponEmail|raw}}, this extension have generated coupon random codes.

### Installation
```
	- Add {{var CouponEmail|raw}} to these files in Magento Theme Section 
		- Magento_Sales/view/email/order_new.html
		- Magento_Sales/view/email/order_new_guest.html
		
	- php bin/magento module:status

	- php bin/magento module:enable Tabish_RelatedProductsByCategory
	
	- php bin/magento setup:upgrade
	
	- php bin/magento setup:di:compile 


```


