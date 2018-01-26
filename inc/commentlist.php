<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 评论列表 @author 多梦 at 2014.06.22  
 * 
 * 
 */

function dmeng_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);
	
	$sticky = get_comment_meta( $comment->comment_ID, 'dmeng_sticky_comment', true );

	$tag = 'li';
	$add_below = $sticky&&$args['echo']==0 ? 'sticky-comment' : 'comment';
	
	$add_comment_class = empty( $args['has_children'] ) ? 'list-group-item' : 'list-group-item parent';
	if ( $comment->comment_approved == 0 ) $add_comment_class .= ' children';
	if ( $comment->comment_parent == 0 ) $add_comment_class .= ' top';

	$avatar_size = 30;
	if($depth<=1) $avatar_size = 50;
	
	$attr_id = $sticky&&$args['echo']==0 ? 'sticky-comment-'.$comment->comment_ID : 'comment-'.$comment->comment_ID;
	$attr_class = $sticky&&$args['echo']==0 ? 'class="comment sticky-comment list-group-item"' : comment_class( $add_comment_class, $comment->comment_ID, $comment->comment_post_ID , false );
	
	$author_url = $comment->comment_author_url;
	$user_exists = false;
	$display_name = $comment->user_id ? get_the_author_meta( 'display_name', $comment->user_id ) : '';
	if($display_name){
		$author_url = get_author_posts_url( $comment->user_id );
		$author_link = get_the_author_meta( 'user_url', $comment->user_id );
		$author_link = $author_link ? $author_link : $author_url;
		$author_link = '<a href="'.$author_link.'" rel="external nofollow" target="_blank">'.$display_name.'</a>';
		$user_exists = true;
	}else{
		$author_link = $comment->comment_author_url ? '<a href="'.$comment->comment_author_url.'" rel="external nofollow" target="_blank">'.$comment->comment_author.'</a>' : $comment->comment_author;
	}
	
	if(empty($author_link)) $author_link = __('匿名','dmeng');
	$author_link = apply_filters( 'get_comment_author_link', $author_link );
	$author_name = '<cite ';
	if($user_exists) $author_name .= 'class="fn" ';
	$author_name .= '>'.$author_link.'</cite>';

	if( current_time('timestamp') - get_comment_time('U') < 86400 ){
		$comment_time = sprintf( __('%s前','dmeng'), human_time_diff( get_comment_time('U'), current_time('timestamp') ) );
	}else{
		$comment_time = sprintf( __('%1$s at %2$s'), get_comment_date('Y-m-d'),  get_comment_time('H:i') );
	}

	global $comment_depth;
	$comment_parent = '';
	if($comment_depth>=$args['max_depth']&&$args['echo']!=0){
		$comment_parent = '<span class="top-level">'.__( 'Reply' ).get_comment($comment->comment_parent)->comment_author.' :</span> ';
		//~ $comment_parent = '<a href="'.htmlspecialchars( get_comment_link( $comment->comment_parent ) ).'" class="top-level">'.__( 'Reply' ).get_comment($comment->comment_parent)->comment_author.' :</a> ';
	}

?><li <?php echo $attr_class;?> id="<?php echo $attr_id;?>" data-comment-id="<?php echo $comment->comment_ID;?>">
<?php

if ( $comment->comment_type == 'pingback' ) {
	
	echo get_comment_date('Ymd') . __(' PING 通告 : ','dmeng') . $author_name; 

}else{
	
if ( $comment->comment_parent == 0 && $args['echo'] ) dmeng_vote_html('comment',$comment->comment_ID);

if ( $comment->user_id != 0 && $args['echo']!=0 ) { ?>
	<a id="comment-author" href="<?php echo $author_url ? $author_url : 'javascript:;'; ?>">
		<?php echo dmeng_get_avatar( $comment->user_id , $avatar_size , dmeng_get_avatar_type($comment->user_id) ); ?>
	</a>
<?php } ?>
	<div id="comment-body">
		<?php if ( $comment->comment_parent == 0 && $args['echo'] ) echo $author_name; ?>
		<div id="comment-content"><?php echo $comment_parent;?><?php echo wpautop(get_comment_text());?></div>
		<div id="comment-meta">
			<?php 
			if ( $comment->comment_parent != 0 || $args['echo']==0 ){
				echo $author_name;
			}
			
			?>
			<time datetime="<?php comment_date('Y-m-d'); ?>" title="<?php printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time() );?>"><?php echo $comment_time; ?></time>
			<?php 
			//~ 回复链接
			comment_reply_link( array_merge( $args, array( 'reply_text' => '<span class="glyphicon glyphicon-transfer"></span>' . __( 'Reply' ),  'login_text' => '<span class="glyphicon glyphicon-transfer"></span>' . __('Log in to Reply'), 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => 999999999 ) ) );

			//~ 私信链接
			if($user_exists && get_current_user_id()!=$comment->user_id){
				echo '<a class="pm" href="'.add_query_arg('tab', 'message', $author_url).'" title="'.__('私信','dmeng').'" target="_blank"><span class="glyphicon glyphicon-share-alt"></span>'.__('私信','dmeng').'</a>';
			}
			
			if( is_user_logged_in() && ( get_current_user_id()==get_the_author_meta('ID') || current_user_can('moderate_comments') ) ) {
				printf('<a href="javascript:;" class="comment-sticky %s"><span class="glyphicon glyphicon-eject"></span>%s</a>', $comment->comment_ID.($sticky ? ' active' : ''), get_option('dmeng_sticky_comment_button_txt',__('置顶','dmeng')) );
			}
			
			//~ 编辑链接
			 edit_comment_link(  '<span class="glyphicon glyphicon-edit"></span>' . __( 'Edit' ), '  ', '' );	
			 ?>
		</div>
	<?php if ( $comment->comment_approved == '0' ) : ?>
		<span class="text-danger"><?php _e( 'Your comment is awaiting moderation.' ); ?></span>
	<?php endif; ?>
	</div>
<?php } 

?></li>
<?php
}
