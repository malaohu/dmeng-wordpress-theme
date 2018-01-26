<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 首页设置页面
 * 
 */

function dmeng_options_home_page(){
	
  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

	update_option( 'dmeng_home_seo', json_encode(array(
		'keywords' => sanitize_text_field($_POST['home_keywords']),
		'description' => sanitize_text_field($_POST['home_description'])
	)));

	$dmeng_home_cat = empty($_POST['home_cat']) ? array() : $_POST['home_cat'];
	$dmeng_home_post_exclude = empty($_POST['home_post_exclude']) ? array() : $_POST['home_post_exclude'];

	update_option( 'dmeng_home_setting', json_encode(array(
		'cat' => $dmeng_home_cat,
		'cat_list' => intval($_POST['home_cat_list']),
		'cat_desc' => intval($_POST['home_cat_desc']),
		'post' => intval($_POST['home_post']),
		'post_title' => $_POST['home_post_title'],
		'ignore_sticky_posts' => intval($_POST['home_ignore_sticky_posts']),
		'sticky_posts_title' => $_POST['home_sticky_posts_title'],
		'post_exclude' => $dmeng_home_post_exclude
	)));

	dmeng_settings_error('updated');
	  
  endif;

	$home_seo = json_decode(get_option('dmeng_home_seo','{"keywords":"","description":""}'));
  
	$home_setting = json_decode(get_option('dmeng_home_setting','{"cat":"[]","cat_list":"2","cat_desc":"","post":"1","post_title":"","ignore_sticky_posts":"1","sticky_posts_title":"{title}","post_exclude":"[]"}'));
	$home_cat = (array)$home_setting->cat;
	$home_cat_list = intval($home_setting->cat_list);
	$home_cat_desc = intval($home_setting->cat_desc);
	$home_post = intval($home_setting->post);
	$home_post_title = $home_setting->post_title;
	$home_ignore_sticky_posts = intval($home_setting->ignore_sticky_posts);
	$home_sticky_posts_title = $home_setting->sticky_posts_title;
	$home_post_exclude = (array)$home_setting->post_exclude;
	
	$categories = get_categories( array('hide_empty' => 0) );
	foreach ( $categories as $category ) {
		$categories_array[$category->term_id] = $category->name;
	}
	
	?>
<div class="wrap">
	<h2><?php _e('多梦主题设置','dmeng');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php
		dmeng_admin_tabs('home');
		
		$option = new DmengOptionsOutput();
		$option->table( array(
			array(
				'type' => 'input',
				'th' => __('首页关键词','dmeng'),
				'before' => '<p>'.__('网站首页的网页关键词','dmeng').'</p>',
				'key' => 'home_keywords',
				'value' => $home_seo->keywords
			),
			array(
				'type' => 'textarea',
				'th' => __('首页描述','dmeng'),
				'before' => '<p>'.__('网站首页的网页描述，推荐200字以内','dmeng').'</p>',
				'key' => 'home_description',
				'value' => $home_seo->description
			),
			array(
				'type' => 'checkbox',
				'th' => __('分类列表','dmeng'),
				'before' => '<p>'.__('选择要显示的分类列表，留空则不显示任何分类','dmeng').'</p>',
				'key' => 'home_cat',
				'value' => array(
					'default' => $home_cat,
					'option' => $categories_array
				)
			),
			array(
				'type' => 'select',
				'th' => __('分类列表排版','dmeng'),
				'before' => '<p>'.__('首页分类列表显示方式','dmeng').'</p>',
				'key' => 'home_cat_list',
				'value' => array(
					'default' => array($home_cat_list),
					'option' => array(
						1 => __( '一列', 'dmeng' ),
						2 => __( '两列', 'dmeng' )
					)
				)
			),
			array(
				'type' => 'select',
				'th' => __('分类信息','dmeng'),
				'before' => '<p>'.__('显示分类的文章数量','dmeng').'</p>',
				'key' => 'home_cat_desc',
				'value' => array(
					'default' => array($home_cat_desc),
					'option' => array(
						1 => __( '显示', 'dmeng' ),
						0 => __( '不显示', 'dmeng' )
					)
				)
			),
			array(
				'type' => 'select',
				'th' => __('文章列表','dmeng'),
				'before' => '<p>'.__('首页文章列表','dmeng').'</p>',
				'key' => 'home_post',
				'value' => array(
					'default' => array($home_post),
					'option' => array(
						1 => __( '最新发表的', 'dmeng' ),
						2 => __( '最后更新的', 'dmeng' ),
						3 => __( '评论最多的', 'dmeng' ),
						0 => __( '不显示', 'dmeng' )
					)
				)
			),
			array(
				'type' => 'input',
				'th' => __('文章列表标题','dmeng'),
				'before' => '<p>'.__('如：最新文章。留空不显示','dmeng').'</p>',
				'key' => 'home_post_title',
				'value' => $home_post_title
			),
			array(
				'type' => 'select',
				'th' => __('排除置顶文章','dmeng'),
				'before' => '<p>'.__('不置顶显示置顶文章','dmeng').'</p>',
				'key' => 'home_ignore_sticky_posts',
				'value' => array(
					'default' => array($home_ignore_sticky_posts),
					'option' => array(
						0 => __( '可以置顶', 'dmeng' ),
						1 => __( '不置顶', 'dmeng' ),
						2 => __( '单独显示在分类列表之上', 'dmeng' )
					)
				)
			),
			array(
				'type' => 'input',
				'th' => __('置顶文章标题格式','dmeng'),
				'before' => '<p>'.__('如果让置顶文章单独显示在分类列表之上，那么可以设置一个标题格式，其中{title}代表原来的文章标题','dmeng').'</p>',
				'key' => 'home_sticky_posts_title',
				'value' => $home_sticky_posts_title
			),
			array(
				'type' => 'checkbox',
				'th' => __('文章列表排除分类','dmeng'),
				'before' => '<p>'.__('选择排除的分类，留空则不排除任何分类','dmeng').'</p>',
				'key' => 'home_post_exclude',
				'value' => array(
					'default' => $home_post_exclude,
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
