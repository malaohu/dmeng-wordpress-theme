<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */
 
/*
 * 主题设置页面 - 积分 @author 多梦 at 2014.06.23 
 * 
 * @option dmeng_reg_credit 注册奖励积分，默认是50分
 * @option dmeng_rec_view_credit 访问推广一次可得积分，默认是5分
 * @option dmeng_rec_reg_credit 注册推广一次可得积分，默认是50分
 * @option dmeng_rec_post_credit 投稿一次可得积分，默认是50分
 * @option dmeng_rec_comment_credit 评论一次可得积分，默认是5分
 * 
 * @option dmeng_rec_view_num 每天可得积分访问推广次数，默认是50次
 * @option dmeng_rec_reg_num 每天可得积分注册推广次数，默认是5次
 * @option dmeng_rec_post_num 每天可得积分投稿次数，默认是5次
 * @option dmeng_rec_comment_num 每天可得积分评论次数，默认是50次
 * 
 */

function dmeng_options_credit_page(){

  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

	$setting_array = array(
		'dmeng_reg_credit',
		'dmeng_rec_view_credit',
		'dmeng_rec_reg_credit',
		'dmeng_rec_post_credit',
		'dmeng_rec_comment_credit',
		'dmeng_rec_view_num',
		'dmeng_rec_reg_num',
		'dmeng_rec_post_num',
		'dmeng_rec_comment_num'
	);
	
	foreach ( $setting_array as $s ){
		update_option( $s,  intval($_POST[$s]) );
	}

    dmeng_settings_error('updated');
    
  endif;

	$option = new DmengOptionsOutput();
	
	?>
<div class="wrap">
	<h2><?php _e('多梦主题设置','dmeng');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php dmeng_admin_tabs('credit');?>
		<p><?php _e('把次数设置为0即全部没有积分奖励。','dmeng');?></p>
		<?php
		$option->table( array(
			array(
				'type' => 'input',
				'th' => __('新用户注册奖励','dmeng'),
				'key' => 'dmeng_reg_credit',
				'value' => get_option('dmeng_reg_credit','50')
			),
			array(
				'type' => 'input',
				'th' => __('投稿一次得分','dmeng'),
				'key' => 'dmeng_rec_post_credit',
				'value' => get_option('dmeng_rec_post_credit','50')
			),
			array(
				'type' => 'input',
				'th' => __('每天投稿次数','dmeng'),
				'key' => 'dmeng_rec_post_num',
				'value' => get_option('dmeng_rec_post_num','5')
			),
			array(
				'type' => 'input',
				'th' => __('评论一次得分','dmeng'),
				'key' => 'dmeng_rec_comment_credit',
				'value' => get_option('dmeng_rec_comment_credit','5')
			),
			array(
				'type' => 'input',
				'th' => __('每天评论次数','dmeng'),
				'key' => 'dmeng_rec_comment_num',
				'value' => get_option('dmeng_rec_comment_num','50')
			)
		) );
		?>
		<h3 class="title"><?php _e('推广','dmeng');?></h3>
		<?php
		$option->table( array(
			array(
				'type' => 'input',
				'th' => __('注册推广一次得分','dmeng'),
				'key' => 'dmeng_rec_reg_credit',
				'value' => get_option('dmeng_rec_reg_credit','50')
			),
			array(
				'type' => 'input',
				'th' => __('每天注册推广次数','dmeng'),
				'key' => 'dmeng_rec_reg_num',
				'value' => get_option('dmeng_rec_reg_num','5')
			),
			array(
				'type' => 'input',
				'th' => __('访问推广一次得分','dmeng'),
				'key' => 'dmeng_rec_view_credit',
				'value' => get_option('dmeng_rec_view_credit','5')
			),
			array(
				'type' => 'input',
				'th' => __('每天访问推广次数','dmeng'),
				'key' => 'dmeng_rec_view_num',
				'value' => get_option('dmeng_rec_view_num','50')
			)
		) );
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'dmeng' );?>"></p>
	</form>
</div>
	<?php
}
