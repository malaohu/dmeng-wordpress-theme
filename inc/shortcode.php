<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 短代码 @author 多梦 at 2014.07.04
 * 
 */

//~ 在文本小工具不自动添加P标签
add_filter( 'widget_text', 'shortcode_unautop' );
//~ 在文本小工具也执行短代码
add_filter( 'widget_text', 'do_shortcode' );

function dmengslide_shortcode_callback( $atts ) {
    $s = shortcode_atts( array( 'id' => 0 ), $atts );
	if($s['id']) return dmeng_slide_to_html($s['id']);
}
add_shortcode( 'dmengslide', 'dmengslide_shortcode_callback' );

function dmengl2v_shortcode_callback( $atts, $content ){
	if( !is_null( $content ) && !is_user_logged_in() ) $content = sprintf('<div class="dmeng-alert"><span class="glyphicon glyphicon-exclamation-sign"></span>' . __(' 此处内容需要 <a href="%s">登录</a> 才可见','dmeng') . '</div>', wp_login_url(get_permalink()));
	return $content;
}
add_shortcode( 'dmengl2v', 'dmengl2v_shortcode_callback' );

function dmengr2v_shortcode_callback( $atts, $content ){

	if( !is_null( $content ) ) :

	$tips_url = '';

	if(!is_user_logged_in()){
		
		$tips_url = wp_login_url(get_permalink());
		
	}else{
		
		global $post;
		$user_id = get_current_user_ID();
		if( $user_id != $post->post_author && !user_can($user_id,'edit_others_posts') ){
			$comments = get_comments( array('status' => 'approve', 'user_id' => get_current_user_ID(), 'post_id' => $post->ID, 'count' => true ) );
			if(!$comments) {
				$tips_url = '#comments';
			}
		}
	}
	
	if($tips_url) $content = sprintf( '<div class="dmeng-alert"><span class="glyphicon glyphicon-comment"></span>' . __('此处内容需要登录并 <a href="%s">发表评论</a> 才可见','dmeng') . '</div>', $tips_url);
	
	endif;
	
	return $content;
}
add_shortcode( 'dmengr2v', 'dmengr2v_shortcode_callback' );

function dmeng_add_quicktags() {
    if (wp_script_is('quicktags')){
?>
    <script type="text/javascript">
		QTags.addButton( 'nextpage', '<?php _e('分页','dmeng');?>', '<!--nextpage-->', '' );
		QTags.addButton( 'dmeng_login_to_view', '<?php _e('登录可见','dmeng');?>', '[dmengl2v]', '[/dmengl2v]', '', '<?php _e('需要登录才可见','dmeng');?>', 200 );
		QTags.addButton( 'dmeng_repley_to_view', '<?php _e('登录并评论','dmeng');?>', '[dmengr2v]', '[/dmengr2v]', '', '<?php _e('需要登录发表评论才可见','dmeng');?>', 200 );
		QTags.addButton( 'alert-success', '<?php _e('alert-success','dmeng');?>', '<div class="alert alert-success" role="alert">', '</div>', '', '<?php _e('绿色警告框','dmeng');?>', 200 );
		QTags.addButton( 'alert-info', '<?php _e('alert-info','dmeng');?>', '<div class="alert alert-info" role="alert">', '</div>', '', '<?php _e('蓝色警告框','dmeng');?>', 200 );
		QTags.addButton( 'alert-warning', '<?php _e('alert-warning','dmeng');?>', '<div class="alert alert-warning" role="alert">', '</div>', '', '<?php _e('黄色警告框','dmeng');?>', 200 );
		QTags.addButton( 'alert-danger', '<?php _e('alert-danger','dmeng');?>', '<div class="alert alert-danger" role="alert">', '</div>', '', '<?php _e('红色警告框','dmeng');?>', 200 );
    </script>
<?php
    }
}
add_action( 'admin_print_footer_scripts', 'dmeng_add_quicktags' );

//~ 幻灯片数据转HTML
function dmeng_slide_to_html($slide_id=0){

	$slide_id = intval($slide_id) ? intval($slide_id) : 0;
	if(!$slide_id) return;
	
	$slide_data = json_decode(get_option('dmeng_slide_'.$slide_id));
	
	if($slide_data){
	$img = $slide_data->img;
	$url = $slide_data->url;
	$title = $slide_data->title;
	$desc = $slide_data->desc;
	
	$keys = array();
	
	foreach( $slide_data as $key=>$slide ){
			if($key=='img'){
					foreach( $slide as $k=>$s ){
							if($s) $keys[] = $k;
					}
			}
	}
	
	if( $keys ){
		$count = count($keys);
		$si = 0;
		
		$carousel_id = 'carousel-'.$slide_id;
		
		$output = '<div id="'. $carousel_id .'" class="carousel slide" data-ride="carousel">';

		if($count>1) {
			$output .= '<ol class="carousel-indicators">';
				for($ii=0;$ii<$count;$ii++) {
					$output .= '<li data-target="#' . $carousel_id . '" data-slide-to="' . $ii . '"';
					if($ii==0) $output .= ' class="active"';
					$output .= '></li> ';
				}
			$output .= '</ol>';
		}
		
		$output .= '<div class="carousel-inner">';

		foreach( $keys as $k ){

			$active = $si==0 ? ' active' : '';
			$item_html = '<div class="item'.$active.'">';
			
			if($url[$k])
				$item_html .= '<a href="'.$url[$k].'" title="'.$title[$k].'" target="_blank">';
				
			$item_html .= '<img src="'.$img[$k].'" alt="'.$title[$k].'">';
			
			if( $title[$k] or $desc[$k] ){
				$item_html .= '<div class="carousel-caption">';
				if($title[$k])
					$item_html .= '<h3>'.$title[$k].'</h3>';
				if($desc[$k])
					$item_html .= '<p>'.$desc[$k].'</p>';
				$item_html .= '</div>';
			}
			
			if($url[$k])
				$item_html .= '</a>';
				
			$item_html .= '</div>';
			
			$output .= $item_html ;
			
			$si++;
		} //~ end foreach $keys 

		$output .= '</div>';

		if($count>1) {
			$output .= '<a class="left carousel-control" href="#' . $carousel_id . '" role="button" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a><a class="right carousel-control" href="#' . $carousel_id . '" role="button" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>';
		}
		
		$output .= '</div><!-- #' . $carousel_id . ' -->';

		return $output;
		
	} //~ if $keys
	} //~ if have content	
}
