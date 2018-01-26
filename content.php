<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */
 
?>
		<article id="content" class="col-lg-8 col-md-8 single" data-post-id="<?php the_ID(); ?>" role="article" itemscope itemtype="http://schema.org/Article">
		<?php echo dmeng_adsense('single','top');?>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="entry-header page-header">
						<h1 class="entry-title h3" itemprop="name"><?php the_title();?><?php if( is_preview() ) edit_post_link(__('Edit This'), ' <small>', '</small> '); ?></h1>
						<?php dmeng_post_meta();?>
					</div>
					<div class="entry-content"  itemprop="articleBody">
						<?php the_content();?>
						<?php dmeng_post_page_nav(); ?>
					</div>
					<?php dmeng_post_copyright(get_the_ID());?>
				</div>
				<?php dmeng_post_footer();?>
				<div class="panel-footer profile clearfix" itemprop="publisher" itemscope itemtype="http://schema.org/Organization">
					<a class="author-avatar" href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>">
						<?php echo dmeng_get_avatar( get_the_author_meta( 'ID' ) , '50' , dmeng_get_avatar_type(get_the_author_meta( 'ID' )) ); ?>
					</a>
					<div class="author-description">
						<div class="author-name"><?php printf( ' %1$s : %2$s ', __('Author') , '<span itemprop="name">'.get_the_author_link().'</span>' );?></div>
						<div itemprop="description"><?php 
						$description = get_the_author_meta('description');
						echo $description ? $description : __('没有个人说明','dmeng'); 
						?></div>
					</div>
				</div>
			</div>
			<?php dmeng_post_nav();?>
			<?php echo dmeng_adsense('single','comment');?>
			<?php comments_template( '', true ); ?>
			<?php echo dmeng_adsense('single','bottom');?>
		 </article><!-- #content -->
