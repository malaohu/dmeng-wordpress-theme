<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

$title = esc_html(get_the_title());
$excerpt = apply_filters( 'the_excerpt', get_the_excerpt() );

if( is_search() ){
	$keyword = get_search_query();
	$title = dmeng_highlight_keyword($keyword, $title);
	$excerpt = dmeng_highlight_keyword($keyword, $excerpt);
}

?>
		<article id="post-<?php the_ID(); ?>" class="panel panel-default archive<?php if( $post->post_status!='publish' ) echo ' text-muted';?>" data-post-id="<?php the_ID(); ?>" role="article" itemscope itemtype="http://schema.org/Article">
					<?php
					$thumbnail_html = $has_thumbnail_class = '';
					$thumbnail = dmeng_get_the_thumbnail('post-thumbnail');
					if($thumbnail){
						$thumbnail_html =  '<div class="entry-thumbnail"><a href="'.get_permalink().'" title="'.get_the_title().'"><img src="'.get_bloginfo('template_url').'/images/grey.png" data-original="'.$thumbnail.'" alt="'.get_the_title().'"></a></div>';
						$has_thumbnail_class = ' has_post_thumbnail';
					}
					?>
				<div class="panel-body<?php echo $has_thumbnail_class;?>">
					<?php if($thumbnail_html) echo $thumbnail_html;?>
					<div class="entry-header page-header">
						<h3 class="entry-title h4">
							<a href="<?php the_permalink();?>" rel="bookmark" itemprop="url"><span itemprop="name"><?php echo $title;?></a></span> <?php if(is_sticky()) echo '<span class="label label-danger">'.__('置顶','dmeng').'</span>'; ?>
						</h3>
						<?php 
						if( $post->post_status!='publish' ){
							
							$meta_output = '<div class="entry-meta">';
							
								if( $post->post_status==='pending' ) $meta_output .= sprintf(__('正在等待审核，你可以 <a href="%1$s">预览</a> 或 <a href="%2$s">重新编辑</a> 。','dmeng'), get_permalink(), get_edit_post_link() );
								
								if( $post->post_status==='draft' ) $meta_output .= sprintf(__('这是一篇草稿，你可以 <a href="%1$s">预览</a> 或 <a href="%2$s">继续编辑</a> 。','dmeng'), get_permalink(), get_edit_post_link() );
								
							$meta_output .= '</div>';
							
							echo $meta_output;
								
						}else{
							dmeng_post_meta();
						}
						?>
					</div>
					<div class="entry-content" itemprop="description"><?php echo $excerpt;?></div>
				</div>
		 </article><!-- #content -->
