<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 用户资料卡 @author 多梦 at 2014.06.19 
 * 
 */
 
//~ 启动主题时清理固定链接缓存
function dmeng_rewrite_flush_rules(){
	global $pagenow,$wp_rewrite;   
	if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ){
		$wp_rewrite->flush_rules();
	}
}
add_action( 'load-themes.php', 'dmeng_rewrite_flush_rules' ); 

//~ 资料卡URL
function dmeng_get_user_url( $type='', $user_id=0 ){
	$user_id = intval($user_id);
	if( $user_id==0 ){
		$user_id = get_current_user_id();
	}
	$url = add_query_arg( 'tab', $type, get_author_posts_url($user_id) );
	return $url;
}

//~ 用户页资料页拒绝搜索引擎索引
function dmeng_author_tab_no_robots(){
	if( is_author() && isset($_GET['tab']) ) wp_no_robots();
}
add_action('wp_head', 'dmeng_author_tab_no_robots');

//~ 更改编辑个人资料链接
function dmeng_profile_page( $url ) {
    return is_admin() ? $url : dmeng_get_user_url('profile');
}
add_filter( 'edit_profile_url', 'dmeng_profile_page' );

//~ 拒绝普通用户访问后台
function dmeng_redirect_wp_admin(){
	if( is_admin() && is_user_logged_in() && !current_user_can('edit_users') && ( !defined('DOING_AJAX') || !DOING_AJAX )  ){
		wp_redirect( dmeng_get_user_url('profile') );
		exit;
	}
}
add_action( 'init', 'dmeng_redirect_wp_admin' );

//~ 普通用户编辑链接改为前台
function dmeng_edit_post_link($url, $post_id){
	if( !current_user_can('edit_users') ){
		$url = add_query_arg(array('action'=>'edit', 'id'=>$post_id), dmeng_get_user_url('post'));
	}
	return $url;
}
add_filter('get_edit_post_link', 'dmeng_edit_post_link', 10, 2);

//~ 登陆页LOGO
function dmeng_login_logo(){
	if( get_header_image() ){
		$custom_header = get_custom_header();
		$logo_data = array();
		$logo_data['url'] = $custom_header->url ? $custom_header->url : get_theme_support( 'custom-header', 'default-image');
		$logo_data['width'] = $custom_header->width ? $custom_header->width : get_theme_support( 'custom-header', 'width');
		$logo_data['height'] = $custom_header->height ? $custom_header->height : get_theme_support( 'custom-header', 'height');
		
		$css = sprintf('background-image:url(%1$s);-webkit-background-size:%2$spx %3$spx;background-size:%2$spx %3$spx;width:%2$spx;height:%3$spx;', $logo_data['url'], $logo_data['width'], $logo_data['height']);
	}else{
		$css = 'display:none;';
	}
	?>
    <style type="text/css">
        body.login div#login h1 a{
			<?php echo $css;?>
		}
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'dmeng_login_logo' );

function dmeng_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'dmeng_login_logo_url' );

function dmeng_login_logo_url_title() {
    return get_bloginfo('name');
}
add_filter( 'login_headertitle', 'dmeng_login_logo_url_title' );

//~ 在后台用户列表中显示昵称
function dmeng_display_name_column( $columns ) {
	$columns['dmeng_display_name'] = '显示名称';
	unset($columns['name']);
	return $columns;
}
add_filter( 'manage_users_columns', 'dmeng_display_name_column' );
 
function dmeng_display_name_column_callback( $value, $column_name, $user_id ) {

	if( 'dmeng_display_name' == $column_name ){
		$user = get_user_by( 'id', $user_id );
		$value = ( $user->display_name ) ? $user->display_name : '';
	}

	return $value;
}
add_action( 'manage_users_custom_column', 'dmeng_display_name_column_callback', 10, 3 );

//~ 添加邮箱登录
function dmeng_email_login_authenticate( $user, $username, $password ) {
	if ( is_a( $user, 'WP_User' ) )
		return $user;

	if ( !empty( $username ) ) {
		$username = str_replace( '&', '&amp;', stripslashes( $username ) );
		$user = get_user_by( 'email', $username );
		if ( isset( $user, $user->user_login, $user->user_status ) && 0 == (int) $user->user_status )
			$username = $user->user_login;
	}

	return wp_authenticate_username_password( null, $username, $password );
}
remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
add_filter( 'authenticate', 'dmeng_email_login_authenticate', 20, 3 );

function dmeng_username_or_email_login() {
	if ( 'wp-login.php' != basename( $_SERVER['SCRIPT_NAME'] ) )
		return;

	?><script type="text/javascript">
	if ( document.getElementById('loginform') )
		document.getElementById('loginform').childNodes[1].childNodes[1].childNodes[0].nodeValue = '<?php echo esc_js( __( '用户名或邮箱地址', 'email-login' ) ); ?>';
	if ( document.getElementById('login_error') )
		document.getElementById('login_error').innerHTML = document.getElementById('login_error').innerHTML.replace( '<?php echo esc_js( __( '用户名' ) ); ?>', '<?php echo esc_js( __( '用户名或邮箱地址' , 'email-login' ) ); ?>' );
	</script><?php
}
add_action( 'login_form', 'dmeng_username_or_email_login' );

//~ 侧边栏用户中心小工具
function dmeng_user_profile_widget(){

	$current_user = wp_get_current_user();
	
	$li_output = '';
	
	$li_output .= '<li>'.dmeng_get_avatar( $current_user->ID , '34' , dmeng_get_avatar_type($current_user->ID), false ) .
		sprintf(__('Logged in as <a href="%1$s">%2$s</a>.'), get_edit_profile_url($current_user->ID), $current_user->display_name) . 
		'<a href="'.wp_logout_url(dmeng_get_current_page_url()).'" title="'.esc_attr__('Log out of this account').'">' .
		__('Log out &raquo;') . 
		'</a></li>';

	if(!filter_var($current_user->user_email, FILTER_VALIDATE_EMAIL)){
		
		$li_output .= '<li><a href="'.dmeng_get_user_url('profile').'#pass">'.__('【重要】请添加正确的邮箱以保证账户安全','dmeng').'</a></li>';
		
	}

	$shorcut_links[] = array(
		'title' => __('个人主页','dmeng'),
		'url' => get_author_posts_url($current_user->ID)
	);
	
	if( current_user_can( 'manage_options' ) ) {
		$shorcut_links[] = array(
			'title' => __('管理后台','dmeng'),
			'url' => admin_url()
		);
	}
	
	$can_post_cat = json_decode(get_option('dmeng_can_post_cat'));
	if( count($can_post_cat) ) {
		$shorcut_links[] = array(
			'title' => __('文章投稿','dmeng'),
			'url' => add_query_arg('action','new',dmeng_get_user_url('post'))
		);
	}
	
	$shorcut_html = '<li class="active">';
	foreach( $shorcut_links as $shorcut ){
		 $shorcut_html .= '<a href="'.$shorcut['url'].'">'.$shorcut['title'].' &raquo;</a>';
	}
	 $shorcut_html .= '</li>';

	$credit = intval(get_user_meta( $current_user->ID, 'dmeng_credit', true ));
	$credit_void = intval(get_user_meta( $current_user->ID, 'dmeng_credit_void', true ));
	$unread_count = intval(get_dmeng_message($current_user->ID, 'count', "( msg_type='unread' OR msg_type='unrepm' )"));
	
	$info_array = array(
		array(
			'title' => __('文章','dmeng'),
			'url' => dmeng_get_user_url('post'),
			'count' => count_user_posts($current_user->ID)
		),
		array(
			'title' => __('评论','dmeng'),
			'url' => dmeng_get_user_url('comment'),
			'count' => get_comments( array('status' => '1', 'user_id'=>$current_user->ID, 'count' => true) )
		),
		array(
			'title' => __('赞','dmeng'),
			'url' => dmeng_get_user_url('like'),
			'count' => intval(get_dmeng_user_vote($current_user->ID, true, '', 'up'))
		),
		array(
			'title' => __('积分','dmeng'),
			'url' => dmeng_get_user_url('credit'),
			'count' => ($credit+$credit_void)
		)
	);
	
	if( intval(get_option('dmeng_is_gift_open', 0)) ){
		$info_array[] = array(
			'title' => __('礼品','dmeng'),
			'url' => dmeng_get_user_url('gift'),
			'count' => get_dmeng_user_gifts($current_user->ID, true)
		);
	}
	
	if($unread_count){
		$info_array[] = array(
				'title' => __('未读','dmeng'),
				'url' => dmeng_get_user_url('message'),
				'count' => $unread_count
			);
	}
	
	$info_html = '<li>';
	
	foreach( $info_array as $info ){
		$info_html .= $info['title'].'<a href="'.$info['url'].'">'.$info['count'].'</a>';
	}
	
	$info_html .= '</li>';
	
	$friend_html = '
	<li>
		<div class="input-group">
			<span class="input-group-addon">'.__('本页推广链接','dmeng').'</span>
			<input id="dmeng_friend_url" type="text" class="form-control" value="'.add_query_arg('fid',$current_user->ID,dmeng_canonical_url()).'">
		</div>
	</li>
	';

	return $li_output.$shorcut_html.$info_html.$friend_html;;

}
