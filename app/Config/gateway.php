<?php
/**
 * 网关配置
 * User: chengjinsheng
 * Date: 2019-07-08
 * Time: 12:08
 */
return [
    '/coupon/<version>/'=> ['App\\Controllers\\V1\\Gateway\\CouponController',
                            'indexAction'], //优惠券服务网关
    '/order/<version>/'=> ['App\\Controllers\\V1\\Gateway\\OrderController',
                            'indexAction'], //订单服务网关

];