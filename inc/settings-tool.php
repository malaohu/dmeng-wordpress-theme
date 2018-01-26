<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */
 
/*
 * 主题设置页面 - 高级工具 @author 多梦 at 2014.06.23 
 * 
 */

function dmeng_options_tool_page(){
	
	$themes = wp_get_themes(array( 'errors' => false , 'allowed' => null ));

  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='clear' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

	$nonce = explode("+", trim($_POST['nonce_title']));
	if( $nonce[0]==__('我确认操作','dmeng') && wp_verify_nonce( $nonce[1], 'check-captcha' ) ) {
		
		global $wpdb;

		//~ 删除主题自己建的表
		$table_message = $wpdb->prefix . 'dmeng_message';   
		$wpdb->query("DROP TABLE IF EXISTS ".$table_message);
		
		$table_meta = $wpdb->prefix . 'dmeng_meta';   
		$wpdb->query("DROP TABLE IF EXISTS ".$table_meta);
		
		$table_tracker = $wpdb->prefix . 'dmeng_tracker';   
		$wpdb->query("DROP TABLE IF EXISTS ".$table_tracker);

		//~ 清理在WordPress表格中的数据
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'dmeng_%'" );
		$wpdb->query( "DELETE FROM $wpdb->posts WHERE post_type LIKE 'gift'" );
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE 'dmeng_%'" );
		$wpdb->query( "DELETE FROM $wpdb->term_taxonomy WHERE taxonomy LIKE 'gift_tag'" );
		$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key LIKE 'dmeng_%'" );
		$wpdb->query( "DELETE FROM $wpdb->commentmeta WHERE meta_key LIKE 'dmeng_%'" );

		//~ 切换到其他主题
		if( isset($_POST['theme']) ) {
			foreach(	$themes as $theme_name=>$theme_data ){
				if( $theme_data->stylesheet == $_POST['theme'] ){
					switch_theme( $theme_name );
					printf("<script>window.location.href='%s';</script>", admin_url('themes.php?activated=true'));
					exit;
				}
			}
		}

	}else{
		
		dmeng_settings_error('error',__('验证码有误，请重试。','dmeng'));
		
	}

  endif;
  
	if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='refresh' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) {
		
		dmeng_refresh_all();
		
		dmeng_settings_error('updated',__('缓存已清理','dmeng'));
	}

	$tab = 'about';
	if( isset($_GET['tab'])){
		if(in_array($_GET['tab'], array('clear','about','refresh'))) $tab = $_GET['tab'];
	}
	$dmeng_tabs = array(
		'about' => __('关于', 'dmeng'),
		'refresh' => __('清理缓存', 'dmeng'),
		'clear' => __('主题数据清理', 'dmeng')
	);
	$tab_output = '<h2 class="nav-tab-wrapper">';
	foreach( $dmeng_tabs as $tab_key=>$tab_name ){
		$tab_output .= sprintf('<a href="%s" class="nav-tab%s">%s</a>', add_query_arg('tab', $tab_key), $tab_key==$tab ? ' nav-tab-active' : '', $tab_name);
	}
	$tab_output .= '</h2>';
	

	?>
<div class="wrap">
	<h2><?php _e('多梦主题设置','dmeng');?></h2>
	<form method="post">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php echo $tab_output;?>
<div style="border:1px solid #e5e5e5;padding:15px;background:#fff;margin:15px 0;">
		<?php if($tab=='clear'){ ?>
		<input type="hidden" name="action" value="clear">
		<p><?php _e('多梦主题数据包括版权声明、幻灯片、浏览次数、投票数据、消息、积分、礼品等。这些数据属于多梦主题私有。','dmeng');?></p>
		<p><?php _e('清理范围包括： 删除 dmeng_message、dmeng_meta、dmeng_tracker 三个表，删除 options、postmeta、usermeta、commentmeta 表以 dmeng_ 开头为 key 的数据和 gift 文章类型和 gift_tag  分类法。注：多梦主题在 wordpress table 中存储的全部数据的 key 都是以 dmeng_ 开头的。','dmeng');?></p>
		<p style="color:#d98500;"><?php _e('选择清理数据后要切换到的主题。如果选择的是多梦主题，则相当于清理现有数据，重新启用多梦主题。','dmeng');?></p>
<?php

		$themes_output = '<select name="theme">';
		foreach(	$themes as $theme_name=>$theme_data ){
			$themes_output .= '<option value="'.$theme_data->stylesheet.'">'.$theme_data->stylesheet.'</option>';
		}
		$themes_output .= '</select>';
		
		echo $themes_output;
?>
		<p style="color:#0074a2;"><?php _e('如果你确定清理并停用多梦主题，请按提示输入”我确认操作”+验证字符的组合（+号也要输入），然后点击清理并停用。','dmeng');?></p>
		<p><?php
		//~ 把一段中文这样分开是防止本地化之后无法验证文字
		_e('请输入：','dmeng');
		_e('我确认操作','dmeng');
		echo '+'.wp_create_nonce('check-captcha');?></p>
		<p><input name="nonce_title" type="text" id="nonce_title" value="" class="regluar-text ltr"> <span style="color:#dd3d36;"><?php _e('请先备份数据库，以防不测。','dmeng');?></span></p>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary confirm" value="<?php _e( '清理并停用', 'dmeng' );?>"></p>
		<p><?php _e('清理WordPress冗余数据（如修订版本、回收站中的文章/垃圾评论等），推荐使用 WP Clean Up  。','dmeng');?>
		
	</form>

<script type="text/javascript">
jQuery(document).ready(function($){
	$('#nonce_title').bind("paste", function(e) {
		alert('<?php _e('为了您的数据安全，请不要直接复制粘贴！','dmeng');?>');
		e.preventDefault();
	});
	jQuery('.confirm').live('click',function(event){
		var r = confirm( '<?php _e('确定要清理吗？你备份数据库了吗？本操作不可逆！','dmeng');?>' );
		if ( r == false ) return false;
    });
});
</script>
	<?php }
	
	if($tab=='about'){ ?>
	<h3>关于多梦</h3>
	<p>多梦是一个自由职业者，嗯，可以说是SOHO族。创建了名为“多梦网络”的WordPress学习站点并运营维护多梦主题。</p>

	<h3>联系多梦</h3>
	<p>QQ：1500213442 邮箱：chihyu@aliyun.com</p>

	<h3>关于主题协议</h3>
	<p>开源协议太长，多梦看不懂，所以没有基于任何协议开放。需要申明的几点是：</p>
	<ol>
		<li>多梦主题（简称：DMENG）是一款免费的WordPress主题</li>
		<li>任何人都可以免费使用和分享多梦主题</li>
		<li>出售主题版权只是为了帮助多梦主题更健康地发展</li>
	</ol>

	<h3>定制开发</h3>
	<p>以下是几个主题案例，插件和其他案例因保密协议关系不便公开。<a href="http://www.dmeng.net/wordpress/" target="_blank">详情了解 >></a></p>
	<ol>
		<li>多梦网络： <a href="http://www.dmeng.net/" target="_blank">http://www.dmeng.net/</a></li>
		<li>明基LIFE小剧场： <a href="http://blog.benq.com.cn/" target="_blank">http://blog.benq.com.cn/</a></li>
		<li>世界名校百科： <a href="http://www.overseasoriental.com/" target="_blank">http://www.overseasoriental.com/</a></li>
	</ol>

	<h3>主机优惠码</h3>
	<p>多梦与众多博客圈子主机商进行深度合作，为童鞋们争取到了多梦专属优惠码！登录即可领取。</p>
	<ol>
		<li>老薛主机<strong>6.5折</strong>优惠码：<a href="http://www.dmeng.net/gift/1323" target="_blank">http://www.dmeng.net/gift/1323</a></li>
		<li>优易主机<strong>6.9折</strong>优惠码：<a href="http://www.dmeng.net/gift/1321" target="_blank">http://www.dmeng.net/gift/1321</a></li>
		<li>维翔主机<strong>7折</strong>优惠码：<a href="http://www.dmeng.net/gift/1319" target="_blank">http://www.dmeng.net/gift/1319</a></li>
		<li>云左科技<strong>8折</strong>优惠码：<a href="http://www.dmeng.net/gift/1316" target="_blank">http://www.dmeng.net/gift/1316</a></li>
		<li>恒创主机<strong>8折</strong>优惠码：<a href="http://www.dmeng.net/gift/1325" target="_blank">http://www.dmeng.net/gift/1325</a></li>
		<li>三号主机<strong>9折</strong>优惠码：<a href="http://www.dmeng.net/gift/1314" target="_blank">http://www.dmeng.net/gift/1314</a></li>
	</ol>

	<h3>致谢</h3>
	<p>感谢以下小伙伴提供的支持</p>
	<ol>
		<li>Bootstrap <a href="http://getbootstrap.com/" target="_blank">http://getbootstrap.com/</a></li>
		<li>jQuery <a href="http://jquery.com/" target="_blank">http://jquery.com/</a></li>
		<li>Lazy Load Plugin for jQuery <a href="http://www.appelsiini.net/projects/lazyload" target="_blank">http://www.appelsiini.net/</a></li>
		<li>七牛云存储 <a href="https://portal.qiniu.com/signup?code=3ldifmmzc22qa" target="_blank">https://www.qiniu.com/</a></li>
	</ol>

	<?php }
	
	if($tab=='refresh'){
	?>
	<p style="color:#0074a2;"><?php _e('如果站点启用了内存对象缓存，会使用对象缓存缓存数据，否则保存成一个字段到数据库中以减少查询。建议配置 Memcached 对象缓存！','dmeng');?></p>
	<p><?php _e('多梦主题有以下几个自定义项目使用 Transients API 缓存数据。','dmeng');?></p>
	<ol>
		<li><?php _e('导航菜单','dmeng');?></li>
		<li><?php _e('首页分类列表','dmeng');?></li>
		<li><?php _e('小工具（最近登录用户、文章排行榜、积分排行榜、站点统计）','dmeng');?></li>
	</ol>
	<p><?php _e('一般情况下，导航菜单缓存在更新菜单时会更新，首页分类列表缓存在更新首页设置时更新，小工具缓存在更新该小工具时会更新（最近登录用户在有用户登录时也会更新缓存），除此之外，全站内容缓存会每隔一小时更新一次。所以，手动刷新缓存几乎是没有必要的，仅仅是备用。','dmeng');?></p>
	<input type="hidden" name="action" value="refresh">
	<p class="submit"><input type="submit" name="submit" id="submit" class="button" value="<?php _e( '点击清理缓存', 'dmeng' );?>"></p>
	<?php _e('清理范围包括对象缓存、 Transients、固定链接缓存等。谨慎操作！','dmeng');?>
	<?php
	}
	?>
</div>
</div>
	<?php
}
