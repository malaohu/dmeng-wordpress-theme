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
		<div id="content" class="col-lg-8 col-md-8" role="main">
			<article class="panel panel-default panel-archive" role="main">
					<div class="panel-body">
						<h1 class="h3 page-header"><?php _e('未找到页面','dmeng');?> <small><?php _e('404 NO FOUND','dmeng');?></small></h1>
	<ul>
		<li><h4><?php _e('可能导致的原因','dmeng');?></h4>
		<ol>
			<li><?php _e('输入的链接有误','dmeng');?></li>
			<li><?php _e('请求的页面不存在','dmeng');?></li>
		</ol>
		</li>
	</ul>
						<h3 class="page-header"><?php _e('看看别的吧','dmeng');?> <small><?php _e('最近更新','dmeng');?></small></h3>
			<?php 
				query_posts( array( 'ignore_sticky_posts' => true, 'posts_per_page' => 5, 'orderby' => 'modified') );
				while ( have_posts() ) : the_post();
					get_template_part('content','archive');
				endwhile; // end of the loop. 
				wp_reset_query();
			?>
					</div>
			 </article>
		 </div><!-- #content -->
		 <?php get_sidebar();?>
	</div>
 </div><!-- #main -->

<?php get_footer('colophon'); ?>
<?php get_footer(); ?>
