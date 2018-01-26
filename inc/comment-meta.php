<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

function dmeng_get_os($user_agent){ 
	
    $os_platform    =   "Unknown OS Platform";

    $os_array       =   array(
                            '/windows nt 6.3/i'     =>  'Windows 8.1',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile'
                        );

    foreach ($os_array as $regex => $value) { 

        if (preg_match($regex, $user_agent)) {
            $os_platform    =   $value;
        }

    }   

    return $os_platform;

}

function dmeng_get_browser($user_agent) {

    $browser = "Unknown Browser";

    $browser_array  =   array(
                            '/msie/i'       =>  'Internet Explorer',
                            '/firefox/i'    =>  'Firefox',
                            '/safari/i'     =>  'Safari',
                            '/chrome/i'     =>  'Chrome',
                            '/opera/i'      =>  'Opera',
                            '/netscape/i'   =>  'Netscape',
                            '/maxthon/i'    =>  'Maxthon',
                            '/konqueror/i'  =>  'Konqueror',
                            '/mobile/i'     =>  'Handheld Browser'
                        );

    foreach ($browser_array as $regex => $value) { 

        if (preg_match($regex, $user_agent)) {
            $browser    =   $value;
        }

    }

    return $browser;

}

/*
 * 
 * 评论置顶 @author 多梦 at 2014.07.04
 * 
 */
 
function dmeng_comment_sticky_callback(){

	if ( ! wp_verify_nonce( trim($_POST['wp_nonce']), 'check-nonce' ) ){
		echo 'NonceIsInvalid';
		die();
	}
	
	$pid = (int)$_POST['post_id'];
	$id = (int)$_POST['comment_id'];
	$sticky = (int)$_POST['sticky'];

	if( $id && get_comment($id)->comment_post_ID==$pid && ( get_current_user_id()==get_post($pid)->post_author || current_user_can('moderate_comments') ) ) :
	
	if($sticky===0){
		delete_comment_meta( $id, 'dmeng_sticky_comment' );
	}else{
		update_comment_meta( $id, 'dmeng_sticky_comment', $sticky );
	}

	endif;
	
	die();
}
add_action( 'wp_ajax_dmeng_comment_sticky', 'dmeng_comment_sticky_callback' );

/*
 * 
 * 获取评论内容 @author 多梦 at 2014.07.04
 * 
 */
 
function dmeng_get_comments_callback(){

	if ( ! wp_verify_nonce( trim($_POST['wp_nonce']), 'check-nonce' ) ){
		echo 'NonceIsInvalid';
		die();
	}
	
	$id = (int)$_POST['post_id'];
	$cpage = (int)$_POST['cpage'];
	$max_page = (int)$_POST['max_page'];
	
	if( !$id || !$cpage ) return;

	$comments = get_comments(array(
		'post_id' => $id,
		'status' => 'approve',
		'order' => 'ASC',
	));

	$depth = get_option('thread_comments_depth');
	$depth = intval($depth)<2 ? 2 : $depth;
	
	$per_page = get_option('comments_per_page');
	
	if ( get_option('comment_order')=='asc' ){
		$top_level = false;
	}else{
		$top_level = true;
	}

	wp_list_comments( "type=comment&callback=dmeng_comment&max_depth=$depth&page=$cpage&per_page=$per_page&reverse_top_level=$top_level", $comments );
	
	$paginate = dmeng_paginate_comments($id,$cpage,$max_page);
	if($paginate) echo '<li class="list-group-item text-center">'.$paginate.'</li>';

	die();
}
add_action( 'wp_ajax_dmeng_get_comments', 'dmeng_get_comments_callback' );
add_action( 'wp_ajax_nopriv_dmeng_get_comments', 'dmeng_get_comments_callback' );
