<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */
 
/*
 * 主题设置页面 - 撰写 @author 多梦 at 2014.06.23 
 * 
 */

function dmeng_options_writing_page(){
	
  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

	update_option( 'dmeng_copyright_status_default', (int)$_POST['copyright_status_default'] );
	update_option( 'dmeng_copyright_content_default', htmlspecialchars($_POST['copyright_content_default']) );
	update_option( 'dmeng_post_index', (int)$_POST['post_index'] );
	
	$dmeng_can_post_cat = empty($_POST['can_post_cat']) ? array() : $_POST['can_post_cat'];
	update_option( 'dmeng_can_post_cat', json_encode($dmeng_can_post_cat) );

    dmeng_settings_error('updated');
	  
  endif;
  
  $copyright_status = (int)get_option('dmeng_copyright_status_default',1);
  $copyright_content = get_option('dmeng_copyright_content_default',sprintf(__('原文链接：%s，转发请注明来源！','dmeng'),'<a href="{link}" rel="author">{title}</a>'));
  $post_index = (int)get_option('dmeng_post_index',1);
  
 $can_post_cat = json_decode(get_option('dmeng_can_post_cat','[]'));
 
	$categories = get_categories( array('hide_empty' => 0) );
	foreach ( $categories as $category ) {
		$categories_array[$category->term_id] = $category->name;
	}

	$option = new DmengOptionsOutput();
	
	?>
<div class="wrap">
	<h2><?php _e('多梦主题设置','dmeng');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php 
		
		dmeng_admin_tabs('writing');

		$option->table( array(
			array(
				'type' => 'select',
				'th' => __('（默认）版权声明开关','dmeng'),
				'before' => '<p>'.__('在文章/页面内容下的版权声明','dmeng').'</p>',
				'key' => 'copyright_status_default',
				'value' => array(
					'default' => array($copyright_status),
					'option' => array(
						1 => __( '显示', 'dmeng' ),
						0 => __( '不显示', 'dmeng' )
					)
				)
			),
			array(
				'type' => 'textarea',
				'th' => __('（默认）版权声明内容','dmeng'),
				'before' => '<p>'.__('版权声明内容，文章链接用{link}表示，文章标题用{title}表示，站点地址用{url}表示，站点名称用{name}表示','dmeng').'</p>',
				'key' => 'copyright_content_default',
				'value' => stripcslashes(htmlspecialchars_decode($copyright_content))
			),
			array(
				'type' => 'select',
				'th' => __('（默认）锚点导航开关','dmeng'),
				'before' => '<p>'.__('选择是时将把文章页和页面内容中的H标题生成锚点导航目录','dmeng').'</p>',
				'key' => 'post_index',
				'value' => array(
					'default' => array($post_index),
					'option' => array(
						1 => __( '显示', 'dmeng' ),
						0 => __( '不显示', 'dmeng' )
					)
				)
			)
		) );
		?>
		<h3 class="title"><?php _e('投稿','dmeng');?></h3>
		<?php
		
		$option->table( array(
			array(
				'type' => 'checkbox',
				'th' => __('允许投稿的分类','dmeng'),
				'before' => '<p>'.__('不选择任何分类则不开放投稿','dmeng').'</p>',
				'key' => 'can_post_cat',
				'value' => array(
					'default' => $can_post_cat,
					'option' => $categories_array
				)
			)
		) );
		
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'dmeng' );?>"></p>
	</form>
</div>
	<?php
}
