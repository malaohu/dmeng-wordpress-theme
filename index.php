<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com
 */

get_header(); ?>
<?php get_header('masthead'); ?>
<div id="main" class="container">
	<div class="row">
	<div id="content" class="col-lg-8 col-md-8 archive" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
<?php

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

$home_setting = json_decode(get_option('dmeng_home_setting','{"cat":"[]","cat_list":"2","cat_desc":"","post":"1","post_title":"","ignore_sticky_posts":"1","sticky_posts_title":"{title}","post_exclude":"[]"}'));

$home_ignore_sticky_posts = intval($home_setting->ignore_sticky_posts);

//~ 只在第一页显示幻灯片和分类列表等。
if( $paged<2 ){

	//~ 首页幻灯片
	$slide_home = intval(get_option('dmeng_slide_home',0));
	if( $slide_home ) echo dmeng_slide_to_html($slide_home);
	
	//~ 把置顶文章单独显示在分类列表之上
	if($home_ignore_sticky_posts===2){
		$sticky_posts = get_option( 'sticky_posts' );
		if($sticky_posts){
			$home_sticky_posts_title = $home_setting->sticky_posts_title;
			query_posts( array( 'post__in'=>$sticky_posts ) );
				while ( have_posts() ) : the_post();
				
					$title = str_replace('{title}', get_the_title(), $home_sticky_posts_title);
					
					$headline = '<article id="post-'.get_the_ID().'" class="panel panel-default panel-headline archive" data-post-id="'.get_the_ID().'" role="article" itemscope itemtype="http://schema.org/Article">';
						$headline .= '<div class="panel-body">';
							$headline .= '<div class="entry-header page-header">';
								$headline .= '<h3 class="entry-title"><a href="'.get_permalink().'" title="'.$title.'" rel="bookmark" itemprop="url"><span itemprop="name">'.$title.'</span></a></h3>';
							$headline .= '</div>';
							$headline .= '<div class="entry-content" itemprop="description">'.get_the_excerpt().'</div>';
						$headline .= '</div>';
					$headline .= '</article><!-- #headline -->';
					
					echo $headline;
					
				endwhile; // end of the loop.
			wp_reset_query();
		}
	}

	//~ 分类列表
	$home_cat = (array)$home_setting->cat;
	$home_cat_list = intval($home_setting->cat_list);
	$home_cat_desc = intval($home_setting->cat_desc);
	$cat_col = $home_cat_list===1 ? '12' : '6';

	if($home_cat){
		
		$cat_cache_key = 'dmeng_hc_'.md5(serialize($home_setting));
		$cat_cache = get_transient( $cat_cache_key );
		if ( false !== $cat_cache ) {
			
			$cat_output = $cat_cache;
			
		}else{
		
			$cat_output = '<div class="row">';
			foreach($home_cat as $cat_id){
				$category = get_category($cat_id,false);
				if($category){
					$cat_output .= '<div class="col-lg-'.$cat_col.' col-md-'.$cat_col.' col-sm-'.$cat_col.'"><div class="panel panel-default home-posts-list">';
					$cat_output .= '<div class="panel-heading"><a href="' . get_category_link( $category->term_id ) . '">'.$category->cat_name.'</a>';
					if($home_cat_desc) $cat_output .= '<small class="text-muted"> <span class="glyphicon glyphicon-list-alt"></span> '.sprintf( __( '%s篇文章', 'dmeng' ) , $category->count ).'</small>';
					$cat_output .= '</div><div class="list-group">';

					query_posts( array( 'ignore_sticky_posts' => 1, 'posts_per_page' => 5, 'cat' => $category->term_id ) );
						while ( have_posts() ) : the_post();
								$cat_output .= '<a href="'.get_permalink().'" title="'.get_the_title().'" class="list-group-item">'.get_the_title().'</a>';
						endwhile;
					wp_reset_query();

					$cat_output .= '</div></div></div>';
				}
			}
			$cat_output .= '</div>';
			
			set_transient( $cat_cache_key, $cat_output.sprintf(__('<!-- cached %s -->', 'dmeng'), current_time('mysql')), 3600 );
		}
		echo $cat_output;
	}
	
}

//~ 文章列表
$home_post = intval($home_setting->post);
if($home_post){
	
	$home_post_exclude = (array)$home_setting->post_exclude;

	$query_args = array();
	
	$query_args['ignore_sticky_posts'] = $home_ignore_sticky_posts;

	$query_args['paged'] = $paged;
	
	if($home_post_exclude) $query_args['category__not_in'] = $home_post_exclude;
	if($home_post===1) $query_args['orderby'] = 'date';
	if($home_post===2) $query_args['orderby'] = 'modified';
	if($home_post===3) $query_args['orderby'] = 'comment_count';
	
	//~ 如果置顶文章单独显示在上面了，这下面的列表就排除置顶文章吧～
	if($home_ignore_sticky_posts===2) $query_args['post__not_in'] = get_option( 'sticky_posts' );
	
	$home_post_title = '';
	$home_post_title .= $home_setting->post_title;
	if( $paged>=2 ) $home_post_title .= sprintf( ' <small>' . __( '第 %s 页', 'dmeng' ) . '</small>', $paged );

	echo '<div class="panel panel-default panel-archive" role="main"><div class="panel-body">';
	
	if($home_post_title) echo '<h1 class="h3 page-header panel-archive-title">'.$home_post_title.'</h1>';
	
		query_posts( $query_args );
		
			while ( have_posts() ) : the_post();
				get_template_part('content','archive');
			endwhile; // end of the loop.
			
			dmeng_paginate();

		wp_reset_query();
	
	echo '</div></div>';
}

?>
	 </div><!-- #content -->
	<?php get_sidebar();?>
	</div>
 </div><!-- #main -->

<?php get_footer('colophon'); ?>
<?php get_footer(); ?>
