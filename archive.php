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
			<?php echo dmeng_adsense('archive','top');?>
			<div class="panel panel-default panel-archive">
				<div class="panel-body">
				<h1 class="h3 page-header panel-archive-title"><?php
					
						$separator = ' › ';
						
						if ( is_category() ){
							
							printf( __( '分类 : %s', 'dmeng' ), dmeng_get_category_parents( get_queried_object_id(), $separator) );
							
						}elseif ( is_tag() ){
							
							printf( __( '标签 : %s', 'dmeng' ), dmeng_breadcrumb_output( get_tag_link(get_queried_object_id()), single_tag_title( '', false )).$separator );
							
						}elseif ( is_date() ){

							$day = get_the_date('d');
							$month = get_the_date('m');
							$year = get_the_date('Y');

							$output[] =  dmeng_breadcrumb_output( get_year_link($year), $year);
							if ( !is_year() ) $output[] =  dmeng_breadcrumb_output( get_month_link($year, $month), $month);
							if ( !is_year() && !is_month() ) $output[] =  dmeng_breadcrumb_output( get_day_link($year, $month, $day), $day);

							printf( __( '日期 : %s', 'dmeng' ), join( $separator, $output ).$separator );

						}else{
							
							_e( '归档', 'dmeng' );
							
						}
						
						global $wp_query;
						$tracker = dmeng_tracker_param();
						
					?>
					<small> <span class="glyphicon glyphicon-list-alt"></span> <?php printf( '%s个相关结果', '<span itemprop="interactionCount">'.$wp_query->found_posts.'</span>' );?> <span class="glyphicon glyphicon-signal"></span> <?php printf( __( '%s次浏览', 'dmeng' ) , get_dmeng_traffic($tracker['type'],$tracker['pid']));?></small>
				</h1>
				<?php
					// Show an optional term description.
					$term_description = term_description();
					if ( ! empty( $term_description ) ) :
						printf( '<div class="taxonomy-description" itemprop="description">%s</div>', $term_description );
					endif;
				?>
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
