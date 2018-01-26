<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

$key = get_search_query();

//~ 搜索关键词为空时跳转到 0 的搜索结果
if($key==''){
	wp_redirect( add_query_arg( 's', 0 ), 301 ); exit;
}

global $wp_query;
						
get_header(); ?>
<?php get_header('masthead'); ?>
<div id="main" class="container">
	<div class="row">
		<div id="content" class="col-lg-8 col-md-8 archive search-archive" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/SearchResultsPage">
			<?php echo dmeng_adsense('archive','top');?>
			<div class="panel panel-default panel-archive">
				<div class="panel-body">
				<h1 class="h3 page-header panel-archive-title"><span itemprop="name"><?php echo $key; ?></span> <small> <span class="glyphicon glyphicon-list-alt"></span> <?php printf( '%s个相关结果', '<span itemprop="interactionCount">'.$wp_query->found_posts.'</span>' );?> <span class="glyphicon glyphicon-signal"></span> <?php printf( __( '%s次搜索', 'dmeng' ) , get_dmeng_traffic('search',$key) ); ?></small>
				</h1>

			<?php 
				while ( have_posts() ) : the_post();
					get_template_part('content','archive');
				endwhile; // end of the loop. 
				dmeng_paginate();
			?>
				</div>
			</div>
			<?php echo dmeng_adsense('archive','bottom');?>
		 </div><!-- #content -->
		<?php get_sidebar();?>
	</div>
 </div><!-- #main -->
<?php get_footer('colophon'); ?>
<?php get_footer(); ?>
