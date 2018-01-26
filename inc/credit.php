<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 积分 @author 多梦 at 2014.06.21 
 * 
 * @user_meta dmeng_credit 当前可用积分
 * @user_meta dmeng_credit_void 无效（已消费）积分
 * 
 * @user_meta dmeng_rec_view 访问推广数据，每天0点清空
 * @user_meta dmeng_rec_reg 注册推广数据，每天0点清空
 * @user_meta dmeng_rec_post 投稿数据，每天0点清空
 * @user_meta dmeng_rec_comment 评论数据，每天0点清空
 * @user_meta dmeng_friend 注册推广人
 * 
 * @option dmeng_reg_credit 注册奖励积分，默认是50分
 * @option dmeng_rec_view_credit 访问推广一次可得积分，默认是5分
 * @option dmeng_rec_reg_credit 注册推广一次可得积分，默认是50分
 * @option dmeng_rec_post_credit 投稿一次可得积分，默认是50分
 * @option dmeng_rec_comment_credit 评论一次可得积分，默认是5分
 * 
 * @option dmeng_rec_view_num 每天可得积分访问推广次数，默认是50次
 * @option dmeng_rec_reg_num 每天可得积分注册推广次数，默认是5次
 * @option dmeng_rec_post_num 每天可得积分投稿次数，默认是5次
 * @option dmeng_rec_comment_num 每天可得积分评论次数，默认是50次
 * 
 */

/*
 * 
 * 更新用户积分
 * 
 */
 
 function update_dmeng_credit( $user_id , $num , $method='add' , $field='dmeng_credit' , $msg='' ){
	 
	if( !is_numeric($user_id)  ) return;

	$field = $field=='dmeng_credit' ? $field : 'dmeng_credit_void';
	
	$credit = (int)get_user_meta( $user_id, $field, true );
	$num = (int)$num;

	if( $method=='add' ){
		
		$add = update_user_meta( $user_id , $field, ( ($credit+$num)>0 ? ($credit+$num) : 0 ) );
		if( $add ){
			add_dmeng_message( $user_id ,  'credit' , current_time('mysql') , ($msg ? $msg : sprintf( __('获得%s积分','dmeng') , $num )) );
			return $add;
		}
	}
	
	if($method=='cut'){
		
		$cut = update_user_meta( $user_id , $field, ( ($credit-$num)>0 ? ($credit-$num) : 0 )  );
		if( $cut ){
			add_dmeng_message( $user_id ,  'credit' , current_time('mysql') , ($msg ? $msg : sprintf( __('消费%s积分','dmeng') , $num )) );
			return $cut;
		}
	}
	
	$update = update_user_meta( $user_id , $field, $num );
	if( $update ){
		add_dmeng_message( $user_id ,  'credit' , current_time('mysql') , ($msg ? $msg : sprintf( __('更新积分为%s','dmeng') , $num )) );
		return $update;
	}

}

 function dmeng_credit_to_void( $user_id , $num, $msg='' ){
	 
	if( !is_numeric($user_id) || !is_numeric($num) ) return;

	$credit = (int)get_user_meta( $user_id, 'dmeng_credit' , true );
	$num = (int)$num;
	
	if($credit<$num) return 'less';
	
	$cut = update_user_meta( $user_id , 'dmeng_credit' , ($credit-$num) );

	$credit_void = (int)get_user_meta( $user_id, 'dmeng_credit_void' , true );
	$add = update_user_meta( $user_id , 'dmeng_credit_void' , ($credit_void+$num) );
	
	add_dmeng_message( $user_id ,  'credit' , current_time('mysql') , ($msg ? $msg : sprintf( __('消费了%s积分','dmeng') , $num )) );
	
	return 0;
		
}

/*
 * 
 * 用户注册时添加推广人和奖励积分
 * 
 */

function user_register_update_dmeng_credit( $user_id ) {

    if( isset($_COOKIE['dmeng_friend']) && is_numeric($_COOKIE['dmeng_friend']) && get_user_option( 'show_admin_bar_front', $_COOKIE['dmeng_friend'] )!==false ){
		update_user_meta( $user_id, 'dmeng_friend', $_COOKIE['dmeng_friend'] );
		$rec_reg_num = (int)get_option('dmeng_rec_reg_num','5');
		$rec_reg = json_decode(get_user_meta( $_COOKIE['dmeng_friend'], 'dmeng_rec_reg', true ));
		$ua = $_SERVER["REMOTE_ADDR"].'&'.$_SERVER["HTTP_USER_AGENT"];
		if(!$rec_reg){
			$rec_reg = array();
			$new_rec_reg = array($ua);
		}else{
			$new_rec_reg = $rec_reg;
			array_push($new_rec_reg , $ua);
		}
		if( (count($rec_reg) < $rec_reg_num) &&  !in_array($ua,$rec_reg) ){
			update_user_meta( $_COOKIE['dmeng_friend'] , 'dmeng_rec_reg' , json_encode( $new_rec_reg ) );
			$reg_credit = (int)get_option('dmeng_rec_reg_credit','50');
			if($reg_credit) update_dmeng_credit( $_COOKIE['dmeng_friend'] , $reg_credit , 'add' , 'dmeng_credit' , sprintf(__('获得注册推广（来自%1$s的注册）奖励%2$s积分','dmeng') , get_the_author_meta('display_name', $user_id) ,$reg_credit) );
		}
	}
	
	$credit = get_option('dmeng_reg_credit','50');
	if($credit){
		update_dmeng_credit( $user_id , $credit , 'add' , 'dmeng_credit' , sprintf(__('获得注册奖励%s积分','dmeng') , $credit) );
	}

}
add_action( 'user_register', 'user_register_update_dmeng_credit');

/*
 * 
 * 访问推广检查
 * 
 */
function hook_dmeng_friend_check_to_tracker_ajax(){
	if( isset($_COOKIE['dmeng_friend']) && is_numeric($_COOKIE['dmeng_friend']) && get_user_option( 'show_admin_bar_front', $_COOKIE['dmeng_friend'] )!==false ){
		$rec_view_num = (int)get_option('dmeng_rec_view_num','50');
		$rec_view = json_decode(get_user_meta( $_COOKIE['dmeng_friend'], 'dmeng_rec_view', true ));
		$ua = $_SERVER["REMOTE_ADDR"].'&'.$_SERVER["HTTP_USER_AGENT"];
		if(!$rec_view){
			$rec_view = array();
			$new_rec_view = array($ua);
		}else{
			$new_rec_view = $rec_view;
			array_push($new_rec_view , $ua);
		}
		if( (count($rec_view) < $rec_view_num) &&  !in_array($ua,$rec_view) ){
			update_user_meta( $_COOKIE['dmeng_friend'] , 'dmeng_rec_view' , json_encode( $new_rec_view ) );
			$view_credit = (int)get_option('dmeng_rec_view_credit','5');
			if($view_credit) update_dmeng_credit( $_COOKIE['dmeng_friend'] , $view_credit , 'add' , 'dmeng_credit' , sprintf(__('获得访问推广奖励%1$s积分','dmeng') ,$view_credit) );
		}
	}
}
add_action( 'dmeng_tracker_ajax_callback', 'hook_dmeng_friend_check_to_tracker_ajax');

/*
 * 
 * 每天 00:00 清空推广数据
 * 
 */
add_action( 'wp', 'clear_dmeng_rec_setup_schedule' );
function clear_dmeng_rec_setup_schedule() {
	if ( ! wp_next_scheduled( 'clear_dmeng_rec_daily_event' ) ) {
		//~ 1193875200 是 2007/11/01 00:00 的时间戳
		wp_schedule_event( '1193875200', 'daily', 'clear_dmeng_rec_daily_event');
	}
}

add_action( 'clear_dmeng_rec_daily_event', 'clear_dmeng_rec_do_this_daily' );
function clear_dmeng_rec_do_this_daily() {
	global $wpdb;
	$wpdb->query( " DELETE FROM $wpdb->usermeta WHERE meta_key='dmeng_rec_view' OR meta_key='dmeng_rec_reg' OR meta_key='dmeng_rec_post' OR meta_key='dmeng_rec_comment' " );
}

//~ 在后台用户列表中显示积分
function dmeng_credit_column( $columns ) {
	$columns['dmeng_credit'] = '积分';
	return $columns;
}
add_filter( 'manage_users_columns', 'dmeng_credit_column' );
 
function dmeng_credit_column_callback( $value, $column_name, $user_id ) {

	if( 'dmeng_credit' == $column_name ){
		$credit = intval(get_user_meta($user_id,'dmeng_credit',true));
		$void = intval(get_user_meta($user_id,'dmeng_credit_void',true));
		$value = sprintf(__('总积分 %1$s 已消费 %2$s','dmeng'), ($credit+$void), $void );
	}

	return $value;
}
add_action( 'manage_users_custom_column', 'dmeng_credit_column_callback', 10, 3 );
