<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 流量统计 @author 多梦 at 2014.06.29 
 * 
 */
 
/*
 * 添加数据库表 @author 多梦 at 2014.06.29 
 * 
 * ID 自动增长主键
 * type 页面类型
 * pid 页面唯一身份
 * traffic 流量
 * 
 */

function dmeng_tracker_install_callback(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'dmeng_tracker';   
    if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) :   
		$sql = " CREATE TABLE `$table_name` (
			`ID` int NOT NULL AUTO_INCREMENT, 
			PRIMARY KEY(ID),
			`type` varchar(20),
			`pid` tinytext,
			`traffic` int
		) CHARSET=utf8;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');   
			dbDelta($sql);   
    endif;
}
function dmeng_tracker_install(){
    global $pagenow;   
    if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) )
        dmeng_tracker_install_callback();
}
add_action( 'load-themes.php', 'dmeng_tracker_install' );   

/*
 * 
 * 获取流量数据
 * 
 */

function get_dmeng_traffic_all( $type='' ){

	$type = sanitize_text_field($type);

	global $wpdb;
	$table_name = $wpdb->prefix . 'dmeng_tracker';
	
	if( $type && in_array($type , array('single', 'attachment', 'cat', 'tag', 'search', 'author') ) )
		$sql = "SELECT sum(traffic) FROM $table_name WHERE type='$type'";
	else
		$sql = "SELECT sum(traffic) FROM $table_name";
	
	$check = $wpdb->get_var( $sql );

	if($check) return $check;
	
	return 0;
}
 
function get_dmeng_traffic( $type , $pid ){

	if( $type=='' || $pid=='' ) return;

	$type = sanitize_text_field($type);
	$pid = sanitize_text_field($pid);
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'dmeng_tracker';
	
	$check = $wpdb->get_var( "SELECT traffic FROM $table_name WHERE type='$type' AND pid='$pid'" );

	if(isset($check)){
	
		return (int)$check;
			
	}else{

		return 0;
			
	}
}

/*
 * 
 * 更新流量数据
 * 
 */

function update_dmeng_traffic( $type , $pid ){

	if( $type=='' || $pid=='' ) return;

	$type = sanitize_text_field($type);
	$pid = sanitize_text_field($pid);
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'dmeng_tracker';
	
	$check = $wpdb->get_var( "SELECT traffic FROM $table_name WHERE type='$type' AND pid='$pid'" );

	if(isset($check)){
	
		$traffic = (int)$check+1;
		if($wpdb->query( "UPDATE $table_name SET traffic='$traffic' WHERE type='$type' AND pid='$pid'" ))
			return $traffic;
			
	}else{
	
		if($wpdb->query( "INSERT INTO $table_name (type,pid,traffic) VALUES ('$type', '$pid', 1)" ))
			return 1;
			
	}
}

/*
 * 
 * 删除流量数据
 * 
 */

function delete_dmeng_traffic( $type , $pid ){

	if( !$type || !$pid ) return;

	$type = sanitize_text_field($type);
	$pid = sanitize_text_field($pid);
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'dmeng_tracker';

    if ( $wpdb->get_var( "SELECT traffic FROM $table_name WHERE type='$type' AND pid='$pid'" ) ) {
        return $wpdb->query( "DELETE FROM $table_name WHERE type='$type' AND pid='$pid'" );
    }
    return true;
}

function delete_dmeng_traffic_post( $pid ){
	delete_dmeng_traffic('single',$pid);
}

function delete_dmeng_traffic_category( $pid ){
	delete_dmeng_traffic('cat',$pid);
}

function delete_dmeng_traffic_tag( $pid ){
	delete_dmeng_traffic('tag',$pid);
}

function delete_dmeng_traffic_user( $pid ){
	delete_dmeng_traffic('author',$pid);
}

function delete_dmeng_traffic_attachment( $pid ){
	delete_dmeng_traffic('attachment',$pid);
}

function delete_dmeng_traffic_term( $term, $tt_id, $taxonomy ){
	delete_dmeng_traffic('tax_'.$taxonomy,$term);
}

/*
 * 
 * 在删除文章、分类法、作者、附件等内容时同时删除相关流量数据
 * 
 */
 
add_action( 'admin_init', 'dmeng_traffic_init' );
function dmeng_traffic_init(){
    add_action( 'delete_post', 'delete_dmeng_traffic_post', 10 );
	add_action( 'delete_category', 'delete_dmeng_traffic_category', 10);
	add_action( 'delete_post_tag', 'delete_dmeng_traffic_tag', 10);
	add_action( 'delete_user', 'delete_dmeng_traffic_user', 10);
	add_action( 'delete_attachment', 'delete_dmeng_traffic_attachment', 10);
	add_action( 'delete_term', 'delete_dmeng_traffic_term', 10);
}

/*
 * 
 * 统计代码参数
 * @param $tracker['type'] 页面类型，如 home / search，默认是unknown
 * @param $tracker['pid'] 页面唯一身份，默认为1，是search时为搜索关键词，是post时为post id，以此类推
 * @author 多梦 at 2014.06.29 
 */

function dmeng_tracker_param(){
	
	global $wp_query;
	
	$object = $wp_query->get_queried_object();
	
	$tracker = array( 'type' => 'unknown' , 'pid' => 1 );
	
	switch(TRUE){
			
			case $wp_query->is_home() : $tracker['type'] = 'home';
			break;
			
			case $wp_query->is_front_page() : $tracker = array( 'type' => 'single' , 'pid' => get_option('page_on_front') );
			break;
			
			case $wp_query->is_single() : case $wp_query->is_page() : $tracker = array( 'type' => $wp_query->is_attachment() ? 'attachment' : 'single' , 'pid' => $object->ID );
			break;
			
			case $wp_query->is_category() : $tracker = array( 'type' => 'cat' , 'pid' => $object->term_id );
			break;
			
			case $wp_query->is_tag() : $tracker = array( 'type' => 'tag' , 'pid' => $object->term_id );
			break;
			
			case $wp_query->is_search() : $tracker = array( 'type' => 'search' , 'pid' => $wp_query->get('s') );
			break;
			
			case $wp_query->is_author() : $tracker = array( 'type' => 'author' , 'pid' => $object->ID );
			break;

			case $wp_query->is_date() : $tracker['type'] = 'date';

				switch(TRUE){
							
					case $wp_query->is_year() :
						$tracker['type'] .= '_year';
						$tracker['pid'] = $wp_query->get('m') ? $wp_query->get('m') : $wp_query->get('year');
					break;
							
					case $wp_query->is_month() :
						$tracker['type'] .= '_month';
						$tracker['pid'] = $wp_query->get('m') ? $wp_query->get('m') : $wp_query->get('year') . sprintf ( "%02d", $wp_query->get('monthnum'));
					break;
							
					case $wp_query->is_day() :
						$tracker['type'] .= '_day';
						$tracker['pid'] = $wp_query->get('m') ? $wp_query->get('m') : $wp_query->get('year') . sprintf ( "%02d", $wp_query->get('monthnum')) . sprintf ( "%02d", $wp_query->get('day'));
					break;
	
				}
						
			break;  //~ date end

			case $wp_query->is_post_type_archive() : $tracker['type'] = 'post_type_'.$wp_query->get('post_type');
			break;
					
			case $wp_query->is_tax() : $tracker = array( 'type' => 'tax_'.$object->taxonomy , 'pid' => $object->term_id );
			break;
			
			case $wp_query->is_archive() : $tracker['type'] = 'archive';
			break;

			case $wp_query->is_404() : $tracker['type'] = '404';
			break;
		
	}

	return $tracker;
	
}

/*
 * 
 * 流量排行
 * 
 */
 function dmeng_tracker_rank($type='single',$limit=10){
	 
	global $wpdb;
	
	$table_tracker = $wpdb->prefix . 'dmeng_tracker';
	
	$results = array();
	
	if( $type=='search' ){
		
		$results = $wpdb->get_results( "SELECT pid,traffic FROM $table_tracker WHERE type = 'search' ORDER BY -traffic ASC LIMIT $limit ");
		
	}
	
	if( $type=='single' ){
		
		$table_posts = $wpdb->posts;
		$results = $wpdb->get_results( "SELECT $table_tracker.pid AS pid,$table_tracker.traffic AS traffic FROM $table_tracker JOIN $table_posts ON $table_posts.ID = $table_tracker.pid WHERE $table_posts.post_status = 'publish' AND $table_tracker.type = '$type' GROUP BY $table_posts.ID ORDER BY -traffic ASC LIMIT $limit ");
	
	}
	
	return $results;
}

/*
 * 
 * 更新流量数据AJAX
 * 
 */
 
function dmeng_tracker_ajax_callback(){

	if ( ! wp_verify_nonce( trim($_POST['wp_nonce']), 'check-nonce' ) ){
		echo 'NonceIsInvalid';
		die();
	}

	if( $_POST['type']=='' || $_POST['pid']=='' ) return;
	
	$type = sanitize_text_field($_POST['type']);
	$pid = sanitize_text_field($_POST['pid']);

	echo update_dmeng_traffic($type,$pid);
	
	do_action( 'dmeng_tracker_ajax_callback', $type, $pid ); 

	die();
}
add_action( 'wp_ajax_dmeng_tracker_ajax', 'dmeng_tracker_ajax_callback' );
add_action( 'wp_ajax_nopriv_dmeng_tracker_ajax', 'dmeng_tracker_ajax_callback' );
