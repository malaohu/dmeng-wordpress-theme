<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 最近登录 @author 多梦 at 2014.07.04
 * 
 */

function dmeng_update_latest_login( $login ) {
	$user = get_user_by( 'login', $login );
	update_user_meta( $user->ID, 'dmeng_latest_login', current_time( 'mysql' ) );
}
add_action( 'wp_login', 'dmeng_update_latest_login', 10, 1 );
 
function dmeng_latest_login_column( $columns ) {
	$columns['dmeng_latest_login'] = '上次登录';
	return $columns;
}
add_filter( 'manage_users_columns', 'dmeng_latest_login_column' );
 
function dmeng_latest_login_column_callback( $value, $column_name, $user_id ) {
	if('dmeng_latest_login' == $column_name){
		$user = get_user_by( 'id', $user_id );
		$value = ( $user->dmeng_latest_login ) ? $user->dmeng_latest_login : $value = __('没有记录','dmeng');
	}
	return $value;
}
add_action( 'manage_users_custom_column', 'dmeng_latest_login_column_callback', 10, 3 );

function dmeng_get_recent_user($number=10){
	$user_query = new WP_User_Query( array ( 'orderby' => 'meta_value', 'order' => 'DESC', 'meta_key' => 'dmeng_latest_login', 'number' => $number ) );
	if($user_query) return $user_query->results;
	return;
}
