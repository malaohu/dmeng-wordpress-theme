<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 广告 @author 多梦 at 2014.09.09
 * 
 */

function dmeng_adsense($type='single',$local='top'){

	$content = $adsense = '';
	
	if($type=='single') $adsense = json_decode(get_option('dmeng_adsense_single','{"top":"","comment":"","bottom":""}'));
	if($type=='archive') $adsense = json_decode(get_option('dmeng_adsense_archive','{"top":"","bottom":""}'));
	if($type=='author') $adsense = json_decode(get_option('dmeng_adsense_author','{"top":"","bottom":""}'));

	if($adsense) $content = stripslashes(htmlspecialchars_decode($adsense->$local));
	
	if($content) return '<div class="panel panel-default"><div class="panel-body adsense">'.trim($content).'</div></div>';

}
