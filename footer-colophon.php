<?php
/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */
 ?>
<footer id="colophon" class="container" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">
	<div class="panel panel-default text-muted">
		<div class="panel-body">
<?php
/*
 * 底部边栏
 * @since DMENG 2.0
 * 
 */
if( is_active_sidebar( 'sidebar-2' ) ){
		dynamic_sidebar( 'sidebar-2' );
}

			// 链接菜单
			if ( has_nav_menu( 'link_menu' ) ) {
				wp_nav_menu( array(
					'menu'              => 'link_menu',
					'theme_location'    => 'link_menu',
					'depth'             => -1,
					'container'         => '',
					'container_class'   => '',
					'menu_id'        => 'link_menu',
					'menu_class'        => 'breadcrumb',
					'items_wrap' 		=> '<ul id="%1$s" class="%2$s"><li class="active"><span class="glyphicon glyphicon-list-alt"></span> '.__('链接','dmeng').'</li> %3$s</ul>',
					'walker'            => new Dmeng_Bootstrap_Menu()
				)	);
			}
?>
		</div>
		<div class="panel-footer clearfix">
			<?php
			$output = sprintf(
			'&copy; %s <a href="%s">%s</a> ',
			date('Y'),
			home_url('/'),
			get_bloginfo('name')
			 );
			 $output .=__('版权所有','dmeng').' '.get_option('zh_cn_l10n_icp_num', '').' '.stripslashes(htmlspecialchars_decode(get_option('dmeng_footer_code')));
			 /*$output .= '<span class="pull-right copyright">'.sprintf(__('<a href="%1$s" target="_blank">WordPress主题</a> 源自 <a href="%2$s" rel="generator" target="_blank">多梦网络</a>','dmeng'), 'http://www.dmeng.net/wordpress/' ,'http://www.dmeng.net/').'</span>';*/
			 echo $output;
			?>
		</div>
	</div>
</footer>
<?php
if(  (int)get_option('dmeng_float_button',1) ==1 ){

	$btn_array = array(
		array(
			'id' => 'goTop',
			'title' => __('去顶部','dmeng'),
			'html' => '<span class="glyphicon glyphicon-arrow-up"></span>'
		),
		array(
			'id' => 'refresh',
			'title' => __('刷新','dmeng'),
			'html' => '<span class="glyphicon glyphicon-repeat"></span>'
		)
	);
	if ( is_single() || is_page() ) {
		$btn_array[] = array(
				'id' => 'goComments',
				'title' => __('评论','dmeng'),
				'html' => '<span class="glyphicon glyphicon-align-justify"></span>'
		);
	}
	$btn_array[] = array(
		'id' => 'goBottom',
		'title' => __('去底部','dmeng'),
		'html' => '<span class="glyphicon glyphicon-arrow-down"></span>'
	);
	$btn_output = '<div class="btn-group-vertical floatButton">';
	foreach( $btn_array as $btn ){
		$btn_output .= sprintf( '<button type="button" class="btn btn-default" id="%s" title="%s">%s</button>', $btn['id'], $btn['title'], $btn['html']);
	}
	$btn_output .= '</div>';
	echo $btn_output;
}
?>
