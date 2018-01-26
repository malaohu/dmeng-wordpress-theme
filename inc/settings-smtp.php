<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */
 
/*
 * 主题设置页面 - SMTP @author 多梦 at 2014.06.23 
 * 
 */

function dmeng_options_smtp_page(){
	
  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

	update_option('dmeng_smtp',json_encode(array(
		'option' => $_POST['dmeng_smtp_option'],
		'host' => $_POST['dmeng_smtp_host'],
		'ssl' => $_POST['dmeng_smtp_ssl'],
		'port' => $_POST['dmeng_smtp_port'],
		'user' => $_POST['dmeng_smtp_user'],
		'pass' => $_POST['dmeng_smtp_pass'],
		'name' => $_POST['dmeng_smtp_name'],
	)));

	dmeng_settings_error('updated');
	  
  endif;

	$smtp = json_decode(get_option('dmeng_smtp','{"option":"0","host":"","ssl":"0","port":"25","user":"","pass":"","name":""}'));
	$open = intval($smtp->option);
	$host = sanitize_text_field($smtp->host);
	$ssl = intval($smtp->ssl);
	$port = intval($smtp->port);
	$user = sanitize_text_field($smtp->user);
	$pass = sanitize_text_field($smtp->pass);
	$name = empty($smtp->name) ? get_bloginfo('name') : sanitize_text_field($smtp->name);

	?>
<div class="wrap">
	<h2><?php _e('多梦主题设置','dmeng');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php 
		
		dmeng_admin_tabs('smtp');
		
		$option = new DmengOptionsOutput();
		$option->table( array(
			array(
				'type' => 'select',
				'th' => __('启用SMTP','dmeng'),
				'key' => 'dmeng_smtp_option',
				'value' => array(
					'default' => array(intval($open)),
					'option' => array(
						1 => __( '启用', 'dmeng' ),
						0 => __( '禁用', 'dmeng' )
					)
				)
			),
			array(
				'type' => 'input',
				'th' => __('发信服务器','dmeng'),
				'key' => 'dmeng_smtp_host',
				'value' => $host
			),
			array(
				'type' => 'select',
				'th' => __('启用SSL','dmeng'),
				'key' => 'dmeng_smtp_ssl',
				'value' => array(
					'default' => array($ssl),
					'option' => array(
						1 => __( '启用', 'dmeng' ),
						0 => __( '禁用', 'dmeng' )
					)
				)
			),
			array(
				'type' => 'input',
				'th' => __('端口号','dmeng'),
				'key' => 'dmeng_smtp_port',
				'value' => $port
			),
			array(
				'type' => 'input',
				'th' => __('发信账号','dmeng'),
				'key' => 'dmeng_smtp_user',
				'value' => $user
			),
			array(
				'type' => 'input-password',
				'th' => __('账号密码','dmeng'),
				'key' => 'dmeng_smtp_pass',
				'value' => $pass
			),
			array(
				'type' => 'input',
				'th' => __('显示昵称','dmeng'),
				'key' => 'dmeng_smtp_name',
				'value' => $name
			)
		) );
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'dmeng' );?>"></p>
	</form>
</div>
	<?php
}
