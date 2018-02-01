<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 模板函数 @author 多梦 at 2014.06.19 
 * 
 */
 
$dmeng_css_path = get_bloginfo('template_url').'/css/dmeng-2.0.7.1.css';

/*
 * 移除WordPress版本信息和默认的canonical链接 @author 多梦 at 2014.06.19 
 * 
 */
remove_action( 'wp_head', 'wp_generator' ); 
remove_action( 'wp_head', 'rel_canonical' );
 
 /* 
 * 通过 after_setup_theme 添加启用多梦主题后要执行的动作 @author 多梦 at 2014.06.19 
 * 
 */
function dmeng_setup() {
	//~ 载入本地化语言文件
	load_theme_textdomain( 'dmeng', get_template_directory() . '/languages' );
	//~ 注册菜单
	register_nav_menus( array(
		'header_menu' => __( '头部菜单', 'dmeng' ),
		'header_right_menu' => __( '头部右侧菜单', 'dmeng' ),
		'link_menu' => __( '链接菜单', 'dmeng' ),
	) );
}
add_action( 'after_setup_theme', 'dmeng_setup' );

//~ 添加文章缩略图 @author 多梦 at 2014.09.04
add_theme_support( 'post-thumbnails', array( 'post', 'gift' ) );
set_post_thumbnail_size( 220, 146, true );

function dmeng_get_the_thumbnail($size = '300') {
	$post_thumbnail = (array)json_decode(get_option('dmeng_post_thumbnail','{"on":"1","suffix":"?imageView2/1/w/220/h/146/q/100"}'));
	$post_thumbnail_on = intval($post_thumbnail['on']);
	$post_thumbnail_suffix = $post_thumbnail['suffix'];
	if(!in_array($post_thumbnail_on,array(1,2))) return;
	$image_url = '';
	if ( has_post_thumbnail() ) {
		$image_url = wp_get_attachment_image_src( get_post_thumbnail_id() , $size);
		$image_url = $image_url[0];
	} else {
		if($post_thumbnail_on==2){
			global $post, $posts;
			$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
			if($output) $image_url = $matches[1][0];
		}
	}
	if($image_url){
		$image_url = $post_thumbnail_suffix ? $image_url.$post_thumbnail_suffix : $image_url;
		return apply_filters('dmeng_post_thumbnail', $image_url);
	}
}

//~ 自定义头部
add_theme_support( 'custom-header', array(
	'default-image'          => get_bloginfo('template_url').'/images/screenshot_64.png',
	'random-default'         => false,
	'width'                  => 64,
	'height'                 => 64,
	'flex-height'            => true,
	'flex-width'             => true,
	'default-text-color'     => '444444',
	'header-text'            => true,
	'uploads'                => true,
	'admin-preview-callback' => 'dmeng_custom_header_admin_preview'
) );
//~ 自定义背景
add_theme_support( 'custom-background' );

function dmeng_custom_header_admin_preview(){
		echo '<style>
#masthead{font: normal 14px/24px "Microsoft Yahei","冬青黑体简体中文 w3","宋体";float:left;width:100%;}
#masthead .header-logo{margin:0 15px 0 0;}
#masthead .header-logo{float:left;}
#masthead .header-text{color:#444}
#masthead .header-text .name{margin:5px 0 5px;font-size: 24px;font-weight: 500;line-height: 1.1;}
#masthead .header-text .name a{color:#444;text-decoration:none;}
#masthead .header-text .name a:hover{text-decoration:underline;}
#masthead .header-text .description{opacity: 0.9;}
</style>
<div id="masthead">' . dmeng_custom_header().'</div>';
}

function dmeng_custom_header(){
	$logo_html = $header_text = '';
	 
	if( get_header_image() ){
		$custom_header = get_custom_header();

		$logo_data = array();
		$logo_data['url'] = $custom_header->url ? $custom_header->url : get_theme_support( 'custom-header', 'default-image');
		$logo_data['width'] = $custom_header->width ? $custom_header->width : get_theme_support( 'custom-header', 'width');
		$logo_data['height'] = $custom_header->height ? $custom_header->height : get_theme_support( 'custom-header', 'height');

		$logo_html = sprintf(
									'<a href="%4$s" rel="home"><img src="%1$s" width="%2$s" height="%3$s" alt="%5$s" /></a>',
									$logo_data['url'],
									$logo_data['width'],
									$logo_data['height'],
									esc_url(home_url('/')),
									get_bloginfo('name')
								);

		$logo_html = '<div class="header-logo">'.$logo_html.'</div>';
	}

	if(display_header_text()){
		$textcolor = get_header_textcolor();
		$textcolor = in_array($textcolor, array('444', '444444')) ? '' : ' style="color:#'.$textcolor.'"';
		$header_text = '<div class="header-text">';
		$header_text .= sprintf('<div class="name"><a href="%1$s" rel="home" id="name"%2$s>%3$s</a></div>', esc_url( home_url( '/' ) ), $textcolor, get_bloginfo( 'name' ));
		$header_text .= '<div class="description" id="desc"'.$textcolor.'>'.get_bloginfo('description').'</div>';
		$header_text .= '</div>';
	}
	
	return $logo_html . $header_text;
}

//~ 登录用户浏览站点时不显示工具栏 @author 多梦 at 2014.06.19 
add_filter('show_admin_bar', '__return_false');

/*
 * 通过 widgets_init 动作定义侧边栏和小工具 @author 多梦 at 2014.06.19 
 * 
 */
function dmeng_widgets_init() {
	register_sidebar( array(
		'name' => __( '主侧边栏', 'dmeng' ),
		'id' => 'sidebar-1',
		'description' => __( '主要的侧边栏', 'dmeng' ),
		'before_widget' => '<aside id="%1$s" class="panel panel-default widget clearfix %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="panel-heading widget-title">',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( '底部边栏', 'dmeng' ),
		'id' => 'sidebar-2',
		'description' => __( '显示在底部', 'dmeng' ),
		'before_widget' => '<aside id="%1$s" class="widget clearfix footer-widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="panel-heading widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'dmeng_widgets_init' );

//~ 去除功能小工具的WordPress版权链接
function dmeng_widget_meta_poweredby($link){
	return;
}
add_filter('widget_meta_poweredby','dmeng_widget_meta_poweredby');
 
function dmeng_get_current_page_url(){
	$ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true:false;
    $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
    $port  = $_SERVER['SERVER_PORT'];
    $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
    $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    return $protocol . '://' . $host . $port . $_SERVER['REQUEST_URI'];
}

function dmeng_get_url($url, $post='', $method='GET'){
	$content = is_array($post) ? http_build_query($post) : $post;
	$content_length = strlen($content);
	$options = array(
            'http' => array(
                'method' => $method,
                'header' =>
                "Content-type: application/x-www-form-urlencoded\r\n" .
                "Content-length: $content_length\r\n",
                'content' => $content
            )
        );
	return file_get_contents($url, false, stream_context_create($options));
}

function dmeng_get_http_response_code($theURL) {
	@$headers = get_headers($theURL);
	return substr($headers[0], 9, 3);
}

//~ 保存提示
function dmeng_settings_error($type='updated',$message=''){
	$type = $type=='updated' ? 'updated' : 'error';
	if(empty($message)) $message = $type=='updated' ?  __('设置已保存。','dmeng') : __('保存失败，请重试。，','dmeng');
    add_settings_error(
        'dmeng_settings_message',
        esc_attr( 'dmeng_settings_updated' ),
		$message,
		$type
    );
    settings_errors( 'dmeng_settings_message' );
}

//~ 载入 Bootstrap 菜单类
require_once( get_template_directory() . '/inc/bootstrap_navwalker.php' );
//~ 载入用户页面
require_once( get_template_directory() . '/inc/user-page.php' );
//~ 载入文章/页面相关信息面板
require_once( get_template_directory() . '/inc/post-meta.php' );
//~ 载入自定义小工具
require_once( get_template_directory() . '/inc/widget.php' );
//~ 载入积分
require_once( get_template_directory() . '/inc/credit.php' );
//~ 载入评论列表
require_once( get_template_directory() . '/inc/commentlist.php' );
//~ 载入评论meta
require_once( get_template_directory() . '/inc/comment-meta.php' );
//~ 载入安全验证码
require_once( get_template_directory() . '/inc/nonce.php' );
//~ 载入流量统计
require_once( get_template_directory() . '/inc/tracker.php' );
//~ 载入meta（主要用于投票）
require_once( get_template_directory() . '/inc/meta.php' );
//~ 载入投票
require_once( get_template_directory() . '/inc/vote.php' );
//~ 载入提示信息
require_once( get_template_directory() . '/inc/message.php' );
//~ 载入设置页面
require_once( get_template_directory() . '/inc/settings.php' );
//~ 载入开放平台登录
require_once( get_template_directory() . '/inc/open.php' );
//~ 载入邮件
require_once( get_template_directory() . '/inc/mail.php' );
//~ 载入最近用户
require_once( get_template_directory() . '/inc/recent-user.php' );
//~ 载入短代码
require_once( get_template_directory() . '/inc/shortcode.php' );
//~ 载入SEO
require_once( get_template_directory() . '/inc/seo.php' );
//~ 载入广告
require_once( get_template_directory() . '/inc/adsense.php' );
//~ 载入版本
require_once( get_template_directory() . '/inc/version.php' );
//~ 载入积分换礼
if( intval(get_option('dmeng_is_gift_open', 0)) ){
	require_once( get_template_directory() . '/inc/gift.php' );
}

//~ 载入缓存
require_once( get_template_directory() . '/inc/cache.php' );

function dmeng_get_avatar( $id , $size='40' , $type='' , $lazy=true ){

	if($type==='qq'){
		
		$O = array(
			'ID'=>get_option('dmeng_open_qq_id'),
			'KEY'=>get_option('dmeng_open_qq_key')
		);
		
		$U = array(
			'ID'=>get_user_meta( $id, 'dmeng_qq_openid', true ),
			'TOKEN'=>get_user_meta( $id, 'dmeng_qq_access_token', true )
		);
		
		if( $O['ID'] && $O['KEY'] && $U['ID'] && $U['TOKEN'] ){
			$avatar_url = 'https://q.qlogo.cn/qqapp/'.$O['ID'].'/'.$U['ID'].'/100';
		}
		
	}else if($type==='weibo'){
		
		$O = array(
			'KEY'=>get_option('dmeng_open_weibo_key'),
			'SECRET'=>get_option('dmeng_open_weibo_secret')
		);

		$U = array(
			'ID'=>get_user_meta( $id, 'dmeng_weibo_openid', true ),
			'TOKEN'=>get_user_meta( $id, 'dmeng_weibo_access_token', true )
		);
		
		if( $O['KEY'] && $O['SECRET'] && $U['ID'] && $U['TOKEN'] ){
			$avatar_url = 'https://tp3.sinaimg.cn/'.$U['ID'].'/180/1.jpg';
		}
		
	}else{

		preg_match("/src='(.*?)'/i", get_avatar( $id, $size ), $matches);
		$avatar_url = $matches[1];
	
	}
	
	return $lazy ? '<img src="'.get_bloginfo('template_url').'/images/grey.png" data-original="'.$avatar_url.'" class="avatar" width="'.$size.'" height="'.$size.'" />' :  '<img src="'.$avatar_url.'" class="avatar" width="'.$size.'" height="'.$size.'" />';
}

function dmeng_get_avatar_type($user_id){
	$id = (int)$user_id;
	if($id===0) return;
	$avatar = get_user_meta($id,'dmeng_avatar',true);
	if( $avatar=='qq' && dmeng_is_open_qq($id) ) return 'qq';
	if( $avatar=='weibo' && dmeng_is_open_weibo($id) ) return 'weibo';
	return 'default';
}

/*
 * 作者/发布时间/评论/分类等相关信息 @author 多梦 at 2014.06.20 
 * 
 */
function dmeng_post_meta(){
		?>
<div class="entry-meta">
<?php
//~ 如果是文章或页面输出字体设置按钮
if( is_single() || is_page() ) { ?>
	<div class="entry-set-font"><span id="set-font-small" class="disabled">A<sup>-</sup></span><span id="set-font-big">A<sup>+</sup></span></div>
<?php }  //~ 字体设置按钮判断结束 ?>
	<span class="glyphicon glyphicon-user"></span> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" itemprop="author"><?php echo get_the_author();?></a>
	<span class="glyphicon glyphicon-time"></span> <time class="entry-date" datetime="<?php echo get_the_date( 'c' );?>"  itemprop="datePublished"><?php echo get_the_date();?></time>
	<span class="glyphicon glyphicon-comment"></span> <a href="<?php the_permalink();?>#comments" itemprop="discussionUrl" itemscope itemtype="http://schema.org/Comment"><span itemprop="interactionCount"><?php comments_number( '0', '1', '%' );?></span></a>
	<span class="glyphicon glyphicon-eye-open"></span> <?php printf( __( '%s 次浏览', 'dmeng' ) , get_dmeng_traffic('single',get_the_ID()) ); ?>
<?php 
//~ 如果是文章页则输出分类和标签，因为只有文章才有～
if( get_post_type()=='post' ) {
	$categories = get_the_category();
	if($categories){
		foreach($categories as $category) {
			$cats[] = '<a href="'.get_category_link( $category->term_id ).'" rel="category" itemprop="articleSection">'.$category->name.'</a>';
		}
		echo '<span class="glyphicon glyphicon-folder-open"></span> ' . join(' | ',$cats);
	}
	$tags = get_the_tag_list('<span class="glyphicon glyphicon-tags"></span> ',' | ');
	if($tags) echo '<span itemprop="keywords">'.$tags.'</span>';
}  //~ 文章页判断结束
?>
</div>
		<?php
}

function dmeng_post_footer(){
	global $post;
	$post_excerpt = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
	$post_excerpt = str_replace(array("\t", "\r\n", "\r", "\n"), "", strip_tags($post_excerpt)); 
?>
				<div class="entry-footer clearfix" role="toolbar">
					<div class="bd-share">
						<?php 
							$bdshare_output = '<div class="bdsharebuttonbox"><a class="bds_qzone" data-cmd="qzone"></a><a class="bds_tsina" data-cmd="tsina"></a><a class="bds_weixin" data-cmd="weixin"></a><a class="bds_more" data-cmd="more"></a></div>';
							$bdshare_output .= '<script>';
							$bdshare_output .= sprintf( "var share_excerpt = '%s';", addslashes(mb_strcut(sprintf( '【%s】%s', esc_html(get_the_title()), $post_excerpt ), 0, 340, 'utf-8').'...' ));
							$bdshare_output .= sprintf( "var share_pic = '%s';", dmeng_get_the_thumbnail('post-thumbnail') );
							$bdshare_output .= sprintf( "var share_url = '%s';", add_query_arg('fid', get_current_user_id(), get_permalink()) );
							$bdshare_output .= sprintf( "var wkey = '%s';var qkey = '%s';", get_option('dmeng_open_weibo_key', ''), get_option('dmeng_open_qq_id', '') );
							$bdshare_output .= "window._bd_share_config = { common : { bdText : share_excerpt,bdDesc : share_excerpt,bdUrl : share_url, bdPic : share_pic, bdSnsKey : {'tsina':wkey, 'tqq':qkey,'qzone':qkey} }, share : [{ 'bdStyle' : 1, 'bdSize' : 24 }] };with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='/static/api/js/share.js?cdnversion='+~(-new Date()/36e5)];";
							$bdshare_output .= '</script>';
							echo $bdshare_output;
						?>
					</div>
					<?php dmeng_vote_html('post',get_the_ID()); dmeng_breadcrumb_html(get_the_ID(),' › ');?>
				</div>
<?php	
}

/*
 * 版权声明
 * 
 */

function dmeng_post_copyright($post_id){
	
	$post_id = (int)$post_id;
	if(!$post_id) return;

	if( (int)get_option('dmeng_copyright_status_all',1)===1 && (int)get_post_meta( $post_id, 'dmeng_copyright_status', true )!==9 ){
		$cc = get_post_meta( $post_id, 'dmeng_copyright_content', true );
		$cc = empty($cc) ? get_option('dmeng_copyright_content_default',sprintf(__('原文链接：%s，转发请注明来源！','dmeng'),'<a href="{link}" rel="author">{title}</a>')) : $cc;
		$cc = stripcslashes(htmlspecialchars_decode($cc));
		if($cc){
			
		?><div class="entry-details" itemprop="copyrightHolder" itemtype="http://schema.org/Organization" itemscope>
			<details>
				<summary><?php 
					if($cc){
						$cc = str_replace(array( '{name}', '{url}', '{title}', '{link}'), array(get_bloginfo('name'), home_url('/'), get_the_title($post_id), get_permalink($post_id)), $cc);
						echo $cc;
						}
				?></summary>
			</details>
	</div><?php
		}
	}
}

function dmeng_breadcrumb_output($url,$name){
	return '<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="'.esc_url($url).'" title="'.$name.'" itemprop="url"><span itemprop="title">'.$name.'</span></a></span>';
}

function dmeng_get_category_parents( $id, $separator='', $visited = array() ) {
	$chain = '';
	$parent = get_term( $id, 'category' );
	if ( is_wp_error( $parent ) )
		return $parent;

	$name = $parent->name;

	if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
		$visited[] = $parent->parent;
		$chain .= dmeng_get_category_parents( $parent->parent, $separator, $visited );
	}

	$chain .= dmeng_breadcrumb_output( get_category_link( $parent->term_id ), $name).$separator;

	return $chain;
}

function dmeng_breadcrumb_html($post_id,$separator){
	$path[] = dmeng_breadcrumb_output( home_url('/'), get_bloginfo('name'));
	if( get_post_type($post_id)=='post' ) {
		$cats_id = array();
		$categories = get_the_category($post_id);
		if($categories){
			foreach($categories as $category) {
				if(!in_array($category->term_id,$cats_id)){
					if ( $category->parent ){
						$path[] = dmeng_get_category_parents( $category->parent, $separator );
						$cats_id[] = $category->parent;
					}
					$path[] = dmeng_breadcrumb_output( get_category_link( $category->term_id ), $category->name);
					$cats_id[] = $category->term_id;
				}
			}
		}
	}
	if( is_singular() ){
		$post_type = get_post_type();
		$post_type_obj = get_post_type_object( $post_type );
		$path[] = dmeng_breadcrumb_output( get_post_type_archive_link( $post_type ), $post_type_obj->labels->singular_name);
	}
	$path[] = dmeng_breadcrumb_output( get_permalink($post_id), get_the_title($post_id));
	echo join( $separator ,$path);
}

//~ 编辑器样式
function dmeng_mce_css($mce_css){
	if ( ! empty( $mce_css ) ) $mce_css .= ',';
	$mce_css .= get_bloginfo('template_url').'/css/bootstrap.min.css,'.$GLOBALS['dmeng_css_path'];
	return $mce_css;
}
add_filter( 'mce_css', 'dmeng_mce_css');

//规定摘要字数
function dmeng_excerpt_length( $length ) {
	return 120;
}
add_filter( 'excerpt_length', 'dmeng_excerpt_length', 999 );
//改变摘要结束省略符号
function dmeng_excerpt_more( $more ) {
	return ' ...';
}
add_filter('excerpt_more', 'dmeng_excerpt_more'); 
function dmeng_password_form() {
    global $post;
    $label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
    $o = '<form action="' . esc_url( add_query_arg('action','postpass',wp_login_url()) ) . '" method="post" class="form-inline"><ul class="list-inline"><li><input name="post_password" id="' . $label . '" type="password" class="form-control" placeholder="'.__('请输入密码 …','dmeng').'"></li><li><button type="submit" class="btn btn-default" id="searchsubmit">'.__('提交','dmeng').'</button></li></ul><span class="help-block">' . __( '这是一篇受密码保护的文章，您需要提供访问密码。','dmeng' ) . '</span></form>';
    return $o;
}
add_filter( 'the_password_form', 'dmeng_password_form' );
//文章归档分页导航
function dmeng_paginate($wp_query=''){
	if(empty($wp_query)) global $wp_query;
	$pages = $wp_query->max_num_pages;
	if ( $pages >= 2 ):
		$big = 999999999;
		$paginate = paginate_links( array(
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' => '?paged=%#%',
			'current' => max( 1, get_query_var('paged') ),
			'total' => $pages,
			'type' => 'array'
		) );
		echo '<ul class="pagination" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">';
		foreach ($paginate as $value) {
			echo '<li itemprop="name">'.$value.'</li>';
		}
		echo '</ul>';
	endif;
}
//文章页上一篇下一篇导航
function dmeng_post_nav(){

	$previous = get_adjacent_post( false, '', true );
	$next = get_adjacent_post( false, '', false );

	if ( ( ! $next && ! $previous ) || is_attachment() ) {
		return;
	}

	?><nav class="pager" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
			<?php
				previous_post_link( '<li class="previous">%link</li>', __( '<span class="text-muted">上一篇：</span> <span itemprop="name">%title</span>', 'dmeng' ), true );
				next_post_link( '<li class="next">%link</li>', __( '<span class="text-muted">下一篇：</span> <span itemprop="name">%title</span>', 'dmeng' ), true );
			?>
	</nav><!-- .navigation --><?php
}
//文章内容分页导航
function dmeng_post_page_nav($echo=true){

	return wp_link_pages( array(
		'before'      => '<nav class="pager" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement"><span>'.__('分页','dmeng').'</span>',
		'after'       => '</nav><!-- .navigation -->',
		'link_before' => '<span itemprop="name">',
		'link_after'  => '</span>',
		'pagelink' => __('%','dmeng'),
		'echo' => $echo
	) );

}
//评论分页导航
function dmeng_paginate_comments($post_id='',$current='',$max=''){

	global $wp_rewrite;

	if ( !$post_id && ( !is_singular() || !get_option('page_comments') ) )
		return;

	$post_link = $post_id ? get_permalink($post_id) : get_permalink();
	$page = $current ? $current : get_query_var('cpage');
	if ( !$page )
		$page = 1;
	$max_page = $max ? $max : get_comment_pages_count();
	$defaults = array(
		'base' => add_query_arg( 'cpage', '%#%', $post_link ),
		'format' => '',
		'total' => $max_page,
		'current' => $page,
		'echo' => false,
		'add_fragment' => '#comments',
		'mid_size' => 4,
		'prev_next' => false,
	);
	if ( $wp_rewrite->using_permalinks() ){
		$defaults['base'] = user_trailingslashit(trailingslashit($post_link) . 'comment-page-%#%', 'commentpaged');
	}
	
	$page_links = paginate_links( $defaults );

	if ( $max_page >= 2 )
		return '<ul id="pagination-comments" role="navigation" data-max-page="'.$max_page.'">'.$page_links.'</ul>';
	
}
// 文章目录
function dmeng_article_index($content) {
	$post_index = (int)get_option('dmeng_post_index_all',1);
	if( in_array($post_index,array(1,2,3) ) && ( is_single() || is_page() ) ){
		if( $post_index===2 && is_page() ) return $content;
		if( $post_index===3 && is_single() ) return $content;
		$matches = array();  
		$index_li = $ol = $depth_num = '';
		if(preg_match_all("/<h([2-6]).*?\>(.*?)<\/h[2-6]>/is", $content, $matches)) {

			//~ $matches[0] 是原标题，包括标签，如<h2>标题</h2>
			//~ $matches[1] 是标题层级，如<h2>就是“2”
			//~ $matches[2] 是标题内容，如<h2>标题</h2>就是“标题”
			
			foreach( $matches[1] as $key=>$level ) {

				if( $ol && intval($ol)<$level){
					$index_li .= '<ul>';
					$depth_num = intval($depth_num)+1;
				}

				if( $ol && intval($ol)>$level ){
					$index_li .= '</li>'.str_repeat('</ul></li>', intval($depth_num));
					$depth_num = 0;
				}
				
				$content = str_replace($matches[0][$key], '<h'.$level.' id="'.($key+1).'">'.$matches[2][$key].'</h'.$level.'>', $content);
				if( $ol && intval($ol)==$level) $index_li .= '</li>';
				$index_li .= '<li><a href="#'.($key+1).'">'.$matches[2][$key].'</a>';

				if(($key+1)==count($matches[1])) $index_li .= '</li>'.str_repeat('</ul></li>', intval($depth_num));

				$ol = $level;
			}
			$content = '<div class="article_index"><h5>'.__('文章目录','dmeng').'<span class="caret"></span></h5><ul>' . $index_li . '</ul></div>' . $content;
		}
	}
    return $content;
}
add_filter( "the_content", "dmeng_article_index" );

// canonical
function dmeng_canonical_url(){

	switch(TRUE){
		
		case is_home() :
		case is_front_page() :
			$url = home_url('/');
		break;
		
		case is_single() :
			$url = get_permalink();
		break;
		
		case is_tax() :
		case is_tag() :
		case is_category() :
			$term = get_queried_object(); 
			$url = get_term_link( $term, $term->taxonomy ); 
		break;
		
		case is_post_type_archive() :
			$url = get_post_type_archive_link( get_post_type() ); 
		break;
		
		case is_author() : 
			$url = get_author_posts_url( get_query_var('author'), get_query_var('author_name') ); 
		break;
		
		case is_year() : 
			$url = get_year_link( get_query_var('year') ); 
		break;
		
		case is_month() : 
			$url = get_month_link( get_query_var('year'), get_query_var('monthnum') ); 
		break;
		
		case is_day() : 
			$url = get_day_link( get_query_var('year'), get_query_var('monthnum'), get_query_var('day') ); 
		break;
		
		default :
			$url = dmeng_get_current_page_url();
	}
	
    if ( get_query_var('paged') > 1 ) { 
		global $wp_rewrite; 
		if ( $wp_rewrite->using_permalinks() ) { 
			$url = user_trailingslashit( trailingslashit( $url ) . trailingslashit( $wp_rewrite->pagination_base ) . get_query_var('paged'), 'archive' ); 
		} else { 
			$url = add_query_arg( 'paged', get_query_var('paged'), $url ); 
		}
	}
	
	return $url;

}

function dmeng_get_redirect_uri(){
	if( isset($_GET['redirect_uri']) ) return urldecode($_GET['redirect_uri']);
	if( isset($_GET['redirect_to']) ) return urldecode($_GET['redirect_to']);
	if( isset($_GET['redirect']) ) return urldecode($_GET['redirect']);
	if( isset($_SERVER['HTTP_REFERER']) ) return urldecode($_SERVER['HTTP_REFERER']);
	return home_url();
}

//~ 投稿文章发表时给作者添加积分和发送邮件通知
function dmeng_pending_to_publish( $post ) {

	$rec_post_num = (int)get_option('dmeng_rec_post_num','5');
	$rec_post_credit = (int)get_option('dmeng_rec_post_credit','50');
	$rec_post = (int)get_user_meta( $post->post_author, 'dmeng_rec_post', true );
	
	if( $rec_post<$rec_post_num && $rec_post_credit ){
	
		update_dmeng_credit( $post->post_author , $rec_post_credit , 'add' , 'dmeng_credit' , sprintf(__('获得文章投稿奖励%1$s积分','dmeng') ,$rec_post_credit) );

		//~ 10秒后发送邮件
		$user_email = get_user_by( 'id', $post->post_author )->user_email;
		if( filter_var( $user_email , FILTER_VALIDATE_EMAIL)){
			$email_title = sprintf(__('你在%1$s上有新的文章发表','dmeng'),get_bloginfo('name'));
			$email_content = sprintf(__('<h3>%1$s，你好！</h3><p>你的文章%2$s已经发表，快去看看吧！</p>','dmeng'), get_user_by( 'id', $post->post_author )->display_name, '<a href="'.get_permalink($post->ID).'" target="_blank">'.$post->post_title.'</a>');
			//~ wp_schedule_single_event( time() + 10, 'dmeng_send_email_event', array( $user_email , $email_title, $email_content ) );
			dmeng_send_email( $user_email , $email_title, $email_content );
		}
	}
	
	update_user_meta( $post->post_author, 'dmeng_rec_post', $rec_post+1);

}
add_action( 'pending_to_publish',  'dmeng_pending_to_publish', 10, 1 );

//~ 发表评论时给作者添加积分
function dmeng_comment_add_credit($comment_id, $comment_object){
	
	$user_id = $comment_object->user_id;
	
	if($user_id){
		
		$rec_comment_num = (int)get_option('dmeng_rec_comment_num','50');
		$rec_comment_credit = (int)get_option('dmeng_rec_comment_credit','5');
		$rec_comment = (int)get_user_meta( $user_id, 'dmeng_rec_comment', true );
		
		if( $rec_comment<$rec_comment_num && $rec_comment_credit ){
			update_dmeng_credit( $user_id , $rec_comment_credit , 'add' , 'dmeng_credit' , sprintf(__('获得评论回复奖励%1$s积分','dmeng') ,$rec_comment_credit) );
			update_user_meta( $user_id, 'dmeng_rec_comment', $rec_comment+1);
		}
	}
}
add_action('wp_insert_comment', 'dmeng_comment_add_credit' , 99, 2 );

function dmeng_before_delete_post( $post_id ){
	
	global $wpdb;
	$table_tracker = $wpdb->prefix . 'dmeng_tracker';
	
	//~ 删除该文章的浏览数据
	$wpdb->query( " DELETE FROM $table_tracker WHERE type='single' AND pid='$post_id' " );
}
add_action( 'before_delete_post', 'dmeng_before_delete_post' );

function dmeng_delete_user( $user_id ) {
	
	global $wpdb;
	$table_message = $wpdb->prefix . 'dmeng_message';
	$table_meta = $wpdb->prefix . 'dmeng_meta';
	
	//~ 删除该用户的消息数据
	$wpdb->query( " DELETE FROM $table_message WHERE user_id='$user_id' " );
	
	//~ 更新投票数据为游客投票
	$wpdb->query( " UPDATE $table_meta SET user_id = 0 WHERE user_id='$user_id' " );
	
		//~ 10秒后发送邮件通知
		$user_email = get_user_by( 'id', $user_id )->user_email;
		if( filter_var( $user_email , FILTER_VALIDATE_EMAIL)){
			$email_title = sprintf(__('你在%1$s上的账号已被注销','dmeng'), get_bloginfo('name'));
			$email_content = sprintf(__('<h3>%1$s，你好！</h3><p>你在%2$s上的账号已被注销！</p>','dmeng'), get_user_by( 'id', $user_id )->display_name, get_bloginfo('name'));
			//~ wp_schedule_single_event( time() + 10, 'dmeng_send_email_event', array( $user_email , $email_title, $email_content ) );
			dmeng_send_email( $user_email , $email_title, $email_content );
		}
	
}
add_action( 'delete_user', 'dmeng_delete_user' );

function dmeng_strip_tags($data){
		return esc_html($data);
}
add_filter( "pre_comment_content", "dmeng_strip_tags" );

function dmeng_get_look(){
	$text = array('[呵呵]', '[嘻嘻]', '[哈哈]', '[可爱]', '[可怜]', '[挖鼻屎]', '[吃惊]', '[害羞]', '[挤眼]', '[闭嘴]', '[鄙视]', '[爱你]', '[泪]', '[偷笑]', '[亲亲]', '[生病]', '[太开心]', '[懒得理你]', '[右哼哼]', '[左哼哼]', '[嘘]', '[衰]', '[委屈]', '[吐]');
	$file = array('hehe.gif', 'xixi.gif', 'haha.gif', 'keai.gif', 'kelian.gif', 'wabishi.gif', 'chijing.gif', 'haixiu.gif', 'jiyan.gif', 'bizui.gif', 'bishi.gif', 'aini.gif', 'lei.gif', 'touxiao.gif', 'qinqin.gif', 'shengbing.gif', 'taikaixin.gif', 'landelini.gif', 'youhengheng.gif', 'zuohengheng.gif', 'xu.gif', 'shuai.gif', 'weiqu.gif', 'tu.gif');
	return array( 'text'=>$text, 'file'=>$file);
}

function dmeng_replace_comment_text($content){

	$look = dmeng_get_look();
	$images_path = get_bloginfo('template_url').'/images';
	
	$format = ( is_admin() && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) ? '<img class="look" src="%2$s" width="22" height="22" />' : '<img class="look" src="%1$s" data-original="%2$s" width="22" height="22" />';

	foreach( $look['file'] as $file ){
		$html[] = sprintf($format, $images_path.'/grey.png', $images_path.'/look/'.$file);
	}

	$content = str_replace($look['text'], $html, $content);
	return $content;
}
add_filter('get_comment_text', 'dmeng_replace_comment_text');

function dmeng_remove_open_sans_from_wp_core() {
	wp_deregister_style( 'open-sans' );
	wp_register_style( 'open-sans', false );
	wp_enqueue_style('open-sans','');
}
add_action( 'init', 'dmeng_remove_open_sans_from_wp_core' );

//~ 上一页下一页和页码的分页导航
function dmeng_pager($current, $max){

	$paged = intval($current);
	$pages = intval($max);
	if($pages<2) return '';

	$pager = '<div class="dmeng-pager clearfix">';

		$pager .= '<div class="btn-group">';
		
			if($paged>1) $pager .= '<a class="btn btn-default" href="' . add_query_arg('page',$paged-1) . '">'.__('上一页','dmeng').'</a>';
			if($paged<$pages) $pager .= '<a class="btn btn-default" href="' . add_query_arg('page',$paged+1) . '">'.__('下一页','dmeng').'</a>';
			
		$pager .= '</div>';
	
		if ($pages>2 ){
			$pager .= '<div class="btn-group pull-right"><select class="form-control pull-right" onchange="document.location.href=this.options[this.selectedIndex].value;">';
				for( $i=1; $i<=$pages; $i++ ){
					$class = $paged==$i ? 'selected="selected"' : '';
					$pager .= sprintf('<option %s value="%s">%s</option>', $class, add_query_arg('page',$i), sprintf(__('第 %s 页', 'dmeng'), $i));
				}
			$pager .= '</select></div>';
		}
	
	$pager .= '</div>';
	
	return $pager;
}

//~ 高亮关键词
function dmeng_highlight_keyword($key, $content){
	$key = addcslashes(trim($key),'\/');
	if(!empty($key)){
		$keys = implode('|', explode(' ', $key));
		$content = preg_replace('/(' . $keys .')/iu', '<em>\0</em>', $content);
	}
	return $content;
}

add_filter( 'widget_text', 'do_shortcode' );



add_filter( 'the_content', 'nofollow_a_links');
function nofollow_a_links ($content) {
    $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
    if (preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
        $srcUrl = $_SERVER['SERVER_NAME'];
        $now_pos =0;
        for ($i = 0; $i < count($matches); $i++) {
            //不包含本站域名，同时不是href="/dm/p/5" 这种形式
            if ( strpos($matches[$i][0], $srcUrl) === false && strpos($matches[$i][0], '//') !== false && strripos($srcUrl,"&seolink=") < 0) {
                $tag = $matches[$i][0];
                $tag0 = $matches[$i][0];
                $noFollow = '';
                //添加nofollow
                $pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
                preg_match($pattern, $tag2, $match);
                if (count($match) < 1) $noFollow.= ' rel="external nofollow" ';
                //写入
                if($noFollow != ''){
                    //只进行单次替换，且使用$now_pos 记录$content的处理进度
                    $now_pos = strpos($content, $tag0, $now_pos);
                    $tag = rtrim($tag, '>'); $tag.= $noFollow . '>';
                    $content = substr_replace( $content, $tag, $now_pos, strlen($tag0) );
                }
            }
        }
    }
    return $content;
}
