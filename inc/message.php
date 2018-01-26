<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 通知和提示信息 @author 多梦 at 2014.06.23 
 * 
 */

//~ 后台提示信息
function dmeng_admin_notices_action() {
	global $pagenow;
	
	$message = __('多梦主题提示 : ','dmeng');
	
	if ( 'options-discussion.php' == $pagenow ){
		
		if(isset( $_GET['settings-updated'] )){
			update_option('thread_comments',1);
		}
		$message .= __('评论嵌套是必须选择的，无法改变！','dmeng');
		$type = 'error';
	}
	
	if( !empty($message) && !empty($type) ) add_settings_error( 'dmeng_message_admin', esc_attr( 'settings_updated' ), $message, $type );
}
add_action( 'admin_notices', 'dmeng_admin_notices_action' );

/*
 * 添加数据库表 @author 多梦 at 2014.07.04
 * 
 * msg_id 自动增长主键
 * user_id 用户ID
 * msg_type 类型
 * msg_date 日期
 * msg_title 标题
 * msg_content 内容
 * 
 */

function dmeng_message_install_callback(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'dmeng_message';   
    if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) :   
		$sql = " CREATE TABLE `$table_name` (
			`msg_id` int NOT NULL AUTO_INCREMENT, 
			PRIMARY KEY(msg_id),
			`user_id` int,
			`msg_type` varchar(20),
			`msg_date` datetime,
			`msg_title` tinytext,
			`msg_content` text
		) CHARSET=utf8;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');   
			dbDelta($sql);   
    endif;
}
function dmeng_message_install(){
    global $pagenow;   
    if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) )
        dmeng_message_install_callback();
}
add_action( 'load-themes.php', 'dmeng_message_install' ); 

/*
 * 
 * 添加消息
 * 
 */

function add_dmeng_message( $uid=0, $type='', $date='', $title='', $content='' ){

	$uid = intval($uid);
	$title = sanitize_text_field($title);
	
	if( !$uid || empty($title) ) return;

	$type = $type ? sanitize_text_field($type) : 'unread';
	$date = $date ? $date : current_time('mysql');
	$content = htmlspecialchars($content);
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'dmeng_message';

	if($wpdb->query( "INSERT INTO $table_name (user_id,msg_type,msg_date,msg_title,msg_content) VALUES ('$uid', '$type', '$date', '$title', '$content')" ))
		return 1;
	
	return 0;
	
}

//~ 添加消息的定时器
add_action( 'add_dmeng_message_event', 'add_dmeng_message', 10, 5 );

/*
 * 
 * 更新状态
 * 
 */

function update_dmeng_message_type( $id=0, $uid=0, $type='' ){

	$id = intval($id);
	$uid = intval($uid);

	if( ( !$id || !$uid) || empty($type) ) return;

	global $wpdb;
	$table_name = $wpdb->prefix . 'dmeng_message';

	if( $id===0 ){
		$sql = " UPDATE $table_name SET msg_type = '$type' WHERE user_id = '$uid' ";
	}else{
		$sql = " UPDATE $table_name SET msg_type = '$type' WHERE msg_id = '$id' ";
	}

	if($wpdb->query( $sql ))
		return 1;
	
	return 0;
	
}

//~ 更新状态的定时器
add_action( 'update_dmeng_message_type_event', 'update_dmeng_message_type', 10, 3 );

/*
 * 
 * 获取消息（积分消息除外）
 * 
 */

function get_dmeng_message( $uid=0 , $count=0, $where='', $limit=0, $offset=0 ){
	
	$uid = intval($uid);
	
	if( !$uid ) return;

	global $wpdb;
	$table_name = $wpdb->prefix . 'dmeng_message';
	
	if($count){
		if($where) $where = " AND $where";
		$check = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE user_id='$uid' $where" );
	}else{
		$check = $wpdb->get_results( "SELECT msg_id,msg_type,msg_date,msg_title,msg_content FROM $table_name WHERE user_id='$uid' AND $where ORDER BY (CASE WHEN msg_type LIKE 'un%' THEN 1 ELSE 0 END) DESC, msg_date DESC LIMIT $offset,$limit" );
	}
	if($check)	return $check;

	return 0;

}

/*
 * 
 * 获取用户的积分消息
 * 
 */

function get_dmeng_credit_message( $uid=0 , $limit=0, $offset=0 ){
	
	$uid = intval($uid);
	
	if( !$uid ) return;

	global $wpdb;
	$table_name = $wpdb->prefix . 'dmeng_message';
	
	$check = $wpdb->get_results( "SELECT msg_id,msg_date,msg_title FROM $table_name WHERE msg_type='credit' AND user_id='$uid' ORDER BY msg_date DESC LIMIT $offset,$limit" );

	if($check)	return $check;

	return 0;

}

/*
 * 
 * 获取私信
 * 
 */

function get_dmeng_pm( $pm=0, $from=0, $count=false, $single=false, $limit=0, $offset=0 ){
	
	$pm = intval($pm);
	$from = intval($from);
	
	if( !$pm || !$from ) return;

	global $wpdb;
	$table_name = $wpdb->prefix . 'dmeng_message';
	
	$title_sql = $single ? "msg_title='{\"pm\":$pm,\"from\":$from}'" : "( msg_title='{\"pm\":$pm,\"from\":$from}' OR msg_title='{\"pm\":$from,\"from\":$pm}' )";
	
	if($count){
		$check = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE ( msg_type='repm' OR msg_type='unrepm' ) AND $title_sql" );
	}else{
		$check = $wpdb->get_results( "SELECT msg_id,msg_date,msg_title,msg_content FROM $table_name WHERE ( msg_type='repm' OR msg_type='unrepm' ) AND $title_sql ORDER BY msg_date DESC LIMIT $offset,$limit" );
	}
	if($check)	return $check;

	return 0;

}
