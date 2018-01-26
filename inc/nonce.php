<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 安全验证码 nonce @author 多梦 at 2014.06.23 
 * 
 */

//~ 通过AJAX获取并保存到cookie是防止页面进行缓存加速后nonce不能及时更新
function dmeng_create_nonce_callback(){

	echo wp_create_nonce( 'check-nonce' );

   die();
}
add_action( 'wp_ajax_dmeng_create_nonce', 'dmeng_create_nonce_callback' );
add_action( 'wp_ajax_nopriv_dmeng_create_nonce', 'dmeng_create_nonce_callback' );
