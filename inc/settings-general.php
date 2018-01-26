<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 主题设置页面
 * 
 */

function dmeng_options_general_page(){
	
  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

	update_option( 'zh_cn_l10n_icp_num', sanitize_text_field($_POST['zh_cn_l10n_icp_num']) );
	update_option( 'dmeng_head_code', htmlspecialchars($_POST['head_code']) );
	update_option( 'dmeng_footer_code', htmlspecialchars($_POST['footer_code']) );
	update_option( 'dmeng_float_button', (int)$_POST['float_button'] );

	dmeng_settings_error('updated');
	  
  endif;
  
	$float_button = (int)get_option('dmeng_float_button',1);

	?>
<div class="wrap">
	<h2><?php _e('多梦主题设置','dmeng');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php 
		
		dmeng_admin_tabs();
		
		$option = new DmengOptionsOutput();
		$option->table( array(
			array(
				'type' => 'input',
				'th' => __('ICP','dmeng'),
				'key' => 'zh_cn_l10n_icp_num',
				'value' => get_option('zh_cn_l10n_icp_num')
			),
			array(
				'type' => 'textarea',
				'th' => __('头部HEAD代码','dmeng'),
				'before' => '<p>'.__('如添加meta信息验证网站所有权','dmeng').'</p>',
				'key' => 'head_code',
				'value' => stripslashes(htmlspecialchars_decode(get_option('dmeng_head_code')))
			),
			array(
				'type' => 'textarea',
				'th' => __('脚部统计代码','dmeng'),
				'before' => '<p>'.__('放置CNZZ、百度统计或安全网站认证小图标等','dmeng').'</p>',
				'key' => 'footer_code',
				'value' => stripslashes(htmlspecialchars_decode(get_option('dmeng_footer_code')))
			),
			array(
				'type' => 'select',
				'th' => __('是否显示浮动按钮','dmeng'),
				'before' => '<p>'.__('选择是时显示到顶部、刷新、到底部等浮动按钮','dmeng').'</p>',
				'key' => 'float_button',
				'value' => array(
					'default' => array($float_button),
					'option' => array(
						1 => __( '显示', 'dmeng' ),
						0 => __( '不显示', 'dmeng' )
					)
				)
			)
		) );
		
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'dmeng' );?>"></p>
	</form>
</div>
	<?php
}
