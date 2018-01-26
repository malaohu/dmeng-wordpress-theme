<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 积分换礼设置页面
 * 
 */

function dmeng_options_gift_page(){
	
  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

	if( intval($_POST['is_gift_open'])==1 ){
		global $wp_rewrite;   
		$wp_rewrite->flush_rules();
	}
	update_option( 'dmeng_is_gift_open', intval($_POST['is_gift_open']) );
	update_option( 'dmeng_gift_filter', sanitize_text_field($_POST['gift_filter']) );
	update_option( 'dmeng_is_gift_future', intval($_POST['is_gift_future']) );
	update_option( 'dmeng_gift_num', intval($_POST['gift_num']) );
	update_option( 'dmeng_gift_tips', sanitize_text_field($_POST['gift_tips']) );
	update_option( 'dmeng_gift_notice', htmlspecialchars($_POST['gift_notice']) );

    dmeng_settings_error('updated');
	  
  endif;
  
  $dmeng_is_gift_open = intval(get_option('dmeng_is_gift_open', 0));
  $dmeng_gift_filter = get_option('dmeng_gift_filter', '0-100,100-1000,1000-10000,10000-0');
  $dmeng_is_gift_future = intval(get_option('dmeng_is_gift_future', 1));
  $dmeng_gift_num = intval(get_option('dmeng_gift_num', 12));
  $dmeng_gift_tips = get_option('dmeng_gift_tips', __('兑换成功后请留意信息通知，如有兑换后可见的内容可直接查看。', 'dmeng'));
  $dmeng_gift_notice = get_option('dmeng_gift_notice', '');

	?>
<div class="wrap">
	<h2><?php _e('多梦主题设置','dmeng');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php
		
		dmeng_admin_tabs('gift');

		$gift_link = $dmeng_is_gift_open ? __( '积分换礼地址是：', 'dmeng' ).get_post_type_archive_link( 'gift' ) : '';

		$option = new DmengOptionsOutput();
		$option->table( array(
			array(
				'type' => 'select',
				'th' => __('开启积分换礼','dmeng'),
				'before' => '<p>'.__('启用积分换礼功能','dmeng').'</p>',
				'after' => '<p>'.$gift_link.'</p>',
				'key' => 'is_gift_open',
				'value' => array(
					'default' => array($dmeng_is_gift_open),
					'option' => array(
						1 => __( '开启', 'dmeng' ),
						0 => __( '关闭', 'dmeng' )
					)
				)
			),
			array(
				'type' => 'input',
				'th' => __('积分范围','dmeng'),
				'before' => '<p>'.__('上方筛选条件中的积分筛选，以英文 , 号分割，0-100代表100以下，100-1000代表100至1000，10000-0代表10000以上','dmeng').'</p>',
				'key' => 'gift_filter',
				'value' => $dmeng_gift_filter
			),
			array(
				'type' => 'select',
				'th' => __('定时发布','dmeng'),
				'before' => '<p>'.__('显示定时发布的礼品','dmeng').'</p>',
				'key' => 'is_gift_future',
				'value' => array(
					'default' => array($dmeng_is_gift_future),
					'option' => array(
						1 => __( '开启', 'dmeng' ),
						0 => __( '关闭', 'dmeng' )
					)
				)
			),
			array(
				'type' => 'input',
				'th' => __('显示礼品数量','dmeng'),
				'before' => '<p>'.__('列表页一页显示的礼品数量。因为一排3列，所以推荐设置可以被3整除的数字，默认是12','dmeng').'</p>',
				'key' => 'gift_num',
				'value' => $dmeng_gift_num
			),
			array(
				'type' => 'input',
				'th' => __('温馨提示','dmeng'),
				'before' => '<p>'.__('礼品详细信息下的提示语','dmeng').'</p>',
				'key' => 'gift_tips',
				'value' => $dmeng_gift_tips
			),
			array(
				'type' => 'editor',
				'th' => __('兑换须知','dmeng'),
				'before' => '<p>'.__('单独显示在礼品信息下的兑换须知','dmeng').'</p>',
				'key' => 'gift_notice',
				'value' => stripslashes(htmlspecialchars_decode($dmeng_gift_notice))
			)
		) );
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'dmeng' );?>"></p>
	</form>
</div>
	<?php
}
