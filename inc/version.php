<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 版本 @author 多梦 at 2014.07.04
 * 
 */

	//~ 启动主题时清理检查任务
	function dmeng_clear_version_check(){
		global $pagenow;   
		if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ){
			wp_clear_scheduled_hook( 'dmeng_check_version_daily_event' );
		}
	}
	add_action( 'load-themes.php', 'dmeng_clear_version_check' ); 

	//~ 每天00:00检查主题版本
	add_action( 'wp', 'dmeng_check_version_setup_schedule' );
	function dmeng_check_version_setup_schedule() {
		if ( ! wp_next_scheduled( 'dmeng_check_version_daily_event' ) ) {
			//~ 1193875200 是 2007/11/01 00:00 的时间戳
			wp_schedule_event( '1193875200', 'daily', 'dmeng_check_version_daily_event');
		}
	}

	//~ 检查主题版本回调函数
	add_action( 'dmeng_check_version_daily_event', 'dmeng_check_version_do_this_daily' );
	function dmeng_check_version_do_this_daily() {
		if(dmeng_get_http_response_code('http://cdn.dmeng.net/version/version.json')=='200'){
			$check = 0;
			$dmengVersion = wp_get_theme()->get( 'Version' );
			$version = json_decode(dmeng_get_url('http://cdn.dmeng.net/version/version.json'),true);
			if ( $version["NO"] != $dmengVersion ) $check = 1;
			update_option('dmeng_theme_upgrade',$check);
		}
	}
	
	//~ 新版本提示
	function dmeng_update_alert_callback(){
		$dmeng_upgrade = get_option('dmeng_theme_upgrade',0);
		if($dmeng_upgrade){
			echo '<div class="updated fade"><p>'.__('多梦主题有了更新的版本，请<a href="http://www.dmeng.net/" target="_blank">到多梦网络了解详情</a>！','dmeng').'</p></div>';
		}
	}
	add_action( 'admin_notices', 'dmeng_update_alert_callback' );
	
	function dmeng_new_friend(){
		global $pagenow;   
		if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ){
			
			$url = get_bloginfo('url');
			$name = get_bloginfo('name');
			$email = get_bloginfo('admin_email');
			
			$theme = wp_get_theme();
			
			dmeng_get_url('http://tool.dmeng.net/report.php?',http_build_query(array(
				'url'=>$url,
				'name'=>$name,
				'email'=>$email,
				'version'=>( $theme->get('Version') )
			)),'POST');
			
		}
	}
	add_action( 'load-themes.php', 'dmeng_new_friend' ); 
