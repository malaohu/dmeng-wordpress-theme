<?php
/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */
 ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" >
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Cache-Control" content="no-transform">
<?php echo stripslashes(htmlspecialchars_decode(get_option('dmeng_head_code')));?>
<link rel="Bookmark" href="/favicon.ico" /><link rel="shortcut icon" href="/favicon.ico" />
<title><?php wp_title( '&#45;', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link rel="canonical" href="<?php echo dmeng_canonical_url();?>">
<link href="<?php echo get_bloginfo('template_url').'/css/bootstrap.min.css';?>" rel="stylesheet">
<link href="<?php echo $GLOBALS['dmeng_css_path'];?>" rel="stylesheet">
<!--[if lt IE 9]><script src="<?php echo get_bloginfo('template_url').'/js/html5shiv-3.7.0.js';?>"></script><script src="<?php echo get_bloginfo('template_url').'/js/respond-1.4.2.min.js';?>"></script><![endif]-->
<?php wp_enqueue_script('jquery');wp_head();?>
<script type="text/javascript"><?php
$script[] = sprintf("var ajaxurl = '%s'", addcslashes(admin_url( '/admin-ajax.php' ), '/') );
$script[] = sprintf("var isUserLoggedIn = %s", intval(is_user_logged_in()) );
if(!is_user_logged_in()){
	$script[] = sprintf("var loginUrl = '%s'", addcslashes(wp_login_url(dmeng_get_current_page_url()), '/') );
}else{
	$script[] = sprintf("var dmengFriend = %s", json_encode(array('title'=>__('请同时按下 Ctrl + C 复制推广链接', 'dmeng'), 'url'=>add_query_arg('fid', get_current_user_id(),dmeng_canonical_url()) )) );
}
$script[] = sprintf("var dmengPath = '%s/'", addcslashes(get_bloginfo('template_url'), '/') );
if( !is_admin() && !is_preview() ) $script[] =  sprintf("var dmengTracker = %s", json_encode(dmeng_tracker_param()) );

echo join(';', $script).";"
?></script><script src="<?php echo get_bloginfo('template_url').'/js/bootstrap.min.js';?>"></script><script src="<?php echo get_bloginfo('template_url').'/js/dmeng-2.0.7.1.js';?>"></script>
</head>
<body <?php body_class(); ?>>
