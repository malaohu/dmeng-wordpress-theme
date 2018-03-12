<?php
/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

$header_content = '';

$custom_header = dmeng_custom_header();

if( $custom_header ){
	$profile_li = '';

	$current_user = wp_get_current_user();

	if( $current_user->exists() ){
		$author_url = get_edit_profile_url($current_user->ID);
		$avatar_url = dmeng_get_avatar( $current_user->ID , '54' , dmeng_get_avatar_type($current_user->ID), false );
		
		$profile_li .= '<li class="clearfix">'.sprintf(__('<a href="%1$s" class="name" title="%2$s">%2$s</a>，你好！', 'dmeng'), get_edit_profile_url($current_user->ID), $current_user->display_name) . 
			'<a href="javascript:;" class="friend">推广 &raquo;</a>' . 
			( current_user_can( 'manage_options' ) ? '<a href="'.admin_url().'" target="_blank">'.__('管理','dmeng').' &raquo;</a>' : '') . 
			'<a href="'.wp_logout_url(dmeng_get_current_page_url()).'" title="'.esc_attr__('退出当前账号').'">' .
			__('退出 &raquo;') . 
			'</a></li>';
			
		$unread_count = intval(get_dmeng_message($current_user->ID, 'count', "( msg_type='unread' OR msg_type='unrepm' )"));
		$unread_count = $unread_count ? sprintf(__('(%s)', 'dmeng'), $unread_count) : '';
		
		$profile_tabs = array(
			'post' => __('文章', 'dmeng'),
			'comment' => __('评论', 'dmeng'),
			'like' => __('赞', 'dmeng'),
			'credit' => __('积分', 'dmeng'),
			'gift' => __('礼品', 'dmeng'),
			'message' => __('消息', 'dmeng').$unread_count
		);
		
		$profile_tabs_output = '';
		foreach( $profile_tabs as $tab_key=>$tab_title ){
			$tab_attr_title = sprintf(__('查看我的%s', 'dmeng'), $tab_title);
			$profile_tabs_output .= sprintf('<a href="%1$s" title="%2$s">%3$s</a>', dmeng_get_user_url($tab_key), $tab_attr_title, $tab_title);
		}

		$profile_li .= '<li class="tabs">'.$profile_tabs_output.'</li>';
	}else{
		
		$weekname = current_time('l');
		$weekarray = array(
			'Monday' => __('一', 'dmeng'),
			'Tuesday' => __('二', 'dmeng'),
			'Wednesday' => __('三', 'dmeng'),
			'Thursday' => __('四', 'dmeng'),
			'Friday' => __('五', 'dmeng'),
			'Saturday' => __('六', 'dmeng'),
			'Sunday' => __('天', 'dmeng'),
		);
		
		$profile_li .= '<li>'.sprintf( __('%1$s，星期%2$s', 'dmeng'), current_time(__('Y年m月d日', 'dmeng')), $weekarray[$weekname]).'</li>';
		
		$author_url = 'javascript:;';
		$avatar_url = '';
		
		$login_methods[] = array(
			'key' => 'wordpress',
			'name' => __( '本地账号' , 'dmeng' ),
			'url' => wp_login_url(dmeng_get_current_page_url())
		);
		
		if(dmeng_is_open_qq()){
			$login_methods[] = array(
				'key' => 'qq',
				'name' => __( 'QQ账号' , 'dmeng' ),
				'url' => home_url('/qqlogin?&action=login&redirect='.urlencode(dmeng_get_current_page_url()))
			);
		}
		if(dmeng_is_open_weibo()){
			$login_methods[] = array(
				'key' => 'weibo',
				'name' => __( '微博账号' , 'dmeng' ),
				'url' => home_url('/?connect=weibo&action=login&redirect='.urlencode(dmeng_get_current_page_url()))
			);
		}
		
		$login_methods_output = '';
		foreach( $login_methods as $login_method ){
			$login_methods_output .= sprintf('<a href="%1$s" class="%2$s" rel="nofollow">%3$s</a>', $login_method['url'], $login_method['key'], $login_method['name']);
		}

		$profile_li .= '<li class="tabs">'.__('登录：', 'dmeng').$login_methods_output.'</li>';

	}
	
	$email_tips = filter_var($current_user->user_email, FILTER_VALIDATE_EMAIL) ? '' : 'data-toggle="tooltip" title="'.__('请添加正确的邮箱以保证账户安全','dmeng').'"';

	$avatar_html = $avatar_url ? sprintf('<a href="%s" class="thumbnail avatar"%s>%s</a>', $author_url, $email_tips, $avatar_url) : '';

	$profile_html = '<ul class="user-profile">'.$profile_li.'</ul>';

	$header_content = '<div class="container header-content"><div class="row">';
	$header_content .= '<div class="col-lg-8 col-md-7 col-sm-6 col-xs-12">'.$custom_header.'</div>';
	$header_content .= '<div class="col-lg-4 col-md-5 col-sm-6 col-xs-12"><div class="header-profile">'.$avatar_html . $profile_html.'</div></div>';
	$header_content .= '</div></div>';

}

 ?>
<header id="masthead" itemscope itemtype="http://schema.org/WPHeader">
	<?php echo $header_content;?>
	<div class="navbar navbar-default navbar-static-top" role="banner">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".header-navbar-collapse"><span class="sr-only"><?php _e('切换菜单','dmeng');?></span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
				<?php
				$brand_class[] = 'navbar-brand';
				if(!$custom_header){
					$brand_class[] = 'show';
				}
				$blogname = get_option('blogname');
				$blogname =  ( is_home() || is_front_page() ) ? '<h1>'.$blogname.'</h1>' : $blogname;
				printf('<a class="%1$s" href="%2$s" rel="home" itemprop="headline">%3$s</a>', join(' ', $brand_class), esc_url(home_url('/')), $blogname);
				?>
			</div>
			<nav id="navbar" class="collapse navbar-collapse header-navbar-collapse" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
			<?php
				// 载入菜单，设置深度为2，因为Bootstrap最多只支持二级菜单
				//  wp_bootstrap_navwalker 是 /inc/wp_bootstrap_navwalker.php 的类，已在functions.php载入
				
				// 主菜单
				if ( has_nav_menu( 'header_menu' ) ) {
					wp_nav_menu( array(
						'menu'              => 'header_menu',
						'theme_location'    => 'header_menu',
						'depth'             => 0,
						'container'         => '',
						'container_class'   => '',
						'menu_class'        => 'nav navbar-nav',
						'items_wrap' 		=> '<ul class="%2$s">%3$s</ul>',
						'walker'            => new Dmeng_Bootstrap_Menu()
					)	);
				}

				// 右侧菜单
				if ( has_nav_menu( 'header_right_menu' ) ) {
					wp_nav_menu( array(
						'menu'              => 'header_right_menu',
						'theme_location'    => 'header_right_menu',
						'depth'             => 2,
						'container'         => '',
						'container_class'   => '',
						'menu_class'        => 'nav navbar-nav navbar-right',
						'items_wrap' 		=> '<ul class="%2$s">%3$s</ul>',
						'walker'            => new Dmeng_Bootstrap_Menu()
					)	);
				}
			?>
			<form class="navbar-form navbar-left" role="search" method="get" id="searchform" action="<?php echo home_url('/');?>">
				<div class="form-group">
					<input type="text" class="form-control" placeholder="<?php _e('搜索 &hellip;','dmeng');?>" name="s" id="s" required>
				</div>
				<button type="submit" class="btn btn-default" id="searchsubmit"><span class="glyphicon glyphicon-search"></span></button>
			</form>
			</nav><!-- #navbar -->
		</div>
	</div>
</header><!-- #masthead -->
