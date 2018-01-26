<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */
 
/*
 * 主题设置页面 - 开放平台 @author 多梦 at 2014.06.23 
 * 
 */

function dmeng_options_open_page(){

  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

	update_option( 'dmeng_open_qq',  intval($_POST['open_qq']) );
	update_option( 'dmeng_open_qq_id',  sanitize_text_field($_POST['open_qq_id']) );
	update_option( 'dmeng_open_qq_key',  sanitize_text_field($_POST['open_qq_key']) );
	
	update_option( 'dmeng_open_weibo',  intval($_POST['open_weibo']) );
	update_option( 'dmeng_open_weibo_key',  sanitize_text_field($_POST['open_weibo_key']) );
	update_option( 'dmeng_open_weibo_secret',  sanitize_text_field($_POST['open_weibo_secret']) );
	
	update_option( 'dmeng_open_role',  sanitize_text_field($_POST['open_role']) );

   dmeng_settings_error('updated');
    
  endif;

	$roles = array();
	$editable_roles = array_reverse( get_editable_roles() );
	foreach ( $editable_roles as $role => $details ) {
		$name = translate_user_role($details['name'] );
		$role = esc_attr($role);
		$roles[$role] = $name;
	}
	?>
<div class="wrap">
	<h2><?php _e('多梦主题设置','dmeng');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php dmeng_admin_tabs('open');?>
		<p><?php _e('启用社会化登录需同时设置相关开放平台参数，否则无效。','dmeng');?></p>
		<?php
		$option = new DmengOptionsOutput();
		$option->table( array(
			array(
				'type' => 'select',
				'th' => __('启用QQ登录','dmeng').' [ <a href="http://www.dmeng.net/connect-qq.html" title="'.__('网站接入QQ登录申请','dmeng').'" target="_blank">?</a> ]',
				'key' => 'open_qq',
				'value' => array(
					'default' => array(intval(get_option('dmeng_open_qq',1))),
					'option' => array(
						1 => __( '启用', 'dmeng' ),
						0 => __( '关闭', 'dmeng' )
					)
				)
			),
			array(
				'type' => 'input',
				'th' => __('QQ ID','dmeng'),
				'key' => 'open_qq_id',
				'value' => get_option('dmeng_open_qq_id')
			),
			array(
				'type' => 'input',
				'th' => __('QQ KEY','dmeng'),
				'key' => 'open_qq_key',
				'value' => get_option('dmeng_open_qq_key')
			),
			array(
				'type' => 'select',
				'th' => __('启用微博登录','dmeng').' [ <a href="http://www.dmeng.net/connect-weibo.html" title="'.__('网站接入微博登录申请','dmeng').'" target="_blank">?</a> ]',
				'key' => 'open_weibo',
				'value' => array(
					'default' => array(intval(get_option('dmeng_open_weibo',1))),
					'option' => array(
						1 => __( '启用', 'dmeng' ),
						0 => __( '关闭', 'dmeng' )
					)
				)
			),
			array(
				'type' => 'input',
				'th' => __('WEIBO KEY','dmeng'),
				'key' => 'open_weibo_key',
				'value' => get_option('dmeng_open_weibo_key')
			),
			array(
				'type' => 'input',
				'th' => __('WEIBO SECRET','dmeng'),
				'key' => 'open_weibo_secret',
				'value' => get_option('dmeng_open_weibo_secret')
			),
			array(
				'type' => 'select',
				'th' => __('默认角色','dmeng'),
				'before' => '<p>'.__('新登录用户的角色，默认是投稿者','dmeng').'</p>',
				'key' => 'open_role',
				'value' => array(
					'default' => array(get_option('dmeng_open_role', 'contributor')),
					'option' => $roles
				)
			),
		) );
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'dmeng' );?>"></p>
	</form>
</div>
	<?php
}
