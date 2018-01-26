<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

global $wp_query;
$curauth = $wp_query->get_queried_object();
$user_name = filter_var($curauth->user_url, FILTER_VALIDATE_URL) ? '<a href="'.$curauth->user_url.'" target="_blank" rel="external">'.$curauth->display_name.'</a>' : $curauth->display_name;

$posts_count =  $wp_query->found_posts;
$comments_count = get_comments( array('status' => '1', 'user_id'=>$curauth->ID, 'count' => true) );
$user_info = get_userdata($curauth->ID);
$credit = intval($user_info->dmeng_credit);
$credit_void = intval($user_info->dmeng_credit_void);

$is_gift_open = intval(get_option('dmeng_is_gift_open', 0));
$gifts_num = $is_gift_open ? get_dmeng_user_gifts($curauth->ID, true) : 0;

$up_posts_count = intval(get_dmeng_user_vote($curauth->ID, true, 'post', 'up'));
$up_comments_count = intval(get_dmeng_user_vote($curauth->ID, true, 'comment', 'up'));
$up_count = $up_posts_count+$up_comments_count;

$current_user = wp_get_current_user();

$oneself = $current_user->ID==$curauth->ID || current_user_can('edit_users') ? 1 : 0;

$tabs = array(
		'post' => __('文章', 'dmeng')."($posts_count)",
		'comment' => __('评论', 'dmeng')."($comments_count)",
		'like' => __('赞', 'dmeng')."($up_count)",
		'credit' => __('积分', 'dmeng')."($credit)",
		'gift' => __('礼品', 'dmeng')."($gifts_num)",
		'message' => __('消息', 'dmeng'),
		'profile' => __('资料', 'dmeng')
);

foreach( $tabs as $tab_key=>$tab_value ){
	if( $tab_key=='gift' && $is_gift_open==0 ) $tab_key = '';
	if( $tab_key ) $tab_array[] = $tab_key;
}

$get_tab = isset($_GET['tab']) && in_array($_GET['tab'], $tab_array) ? $_GET['tab'] : 'post';

	//~ 提示
	$message = $pages = '';
	
	if($get_tab=='profile' && ($current_user->ID!=$curauth->ID && current_user_can('edit_users')) ) $message = sprintf(__('你正在查看的是%s的资料，修改请慎重！', 'dmeng'), $curauth->display_name);
	
	//~ 积分start
	
	if( isset($_POST['creditNonce']) && current_user_can('edit_users') ){
		if ( ! wp_verify_nonce( $_POST['creditNonce'], 'credit-nonce' ) ) {
			$message = __('安全认证失败，请重试！','dmeng');
		}else{
			$c_user_id =  $curauth->ID;
			if( isset($_POST['creditChange']) && sanitize_text_field($_POST['creditChange'])=='add' ){
				$c_do = 'add';
				$c_do_title = __('增加','dmeng');
			}else{
				$c_do = 'cut';
				$c_do_title = __('减少','dmeng');
			}

			$c_num =  intval($_POST['creditNum']);
			$c_desc =  sanitize_text_field($_POST['creditDesc']);
			
			$c_desc = empty($c_desc) ? '' : __('备注','dmeng') . ' : '. $c_desc;

			update_dmeng_credit( $c_user_id , $c_num , $c_do , 'dmeng_credit' , sprintf(__('%1$s将你的积分%2$s %3$s 分。%4$s','dmeng') , $current_user->display_name, $c_do_title, $c_num, $c_desc) );
			
			$message = sprintf(__('操作成功！已将%1$s的积分%2$s %3$s 分。','dmeng'), $user_name, $c_do_title, $c_num);
		}
	}
	
	//~ 积分end
	
	//~ 私信start
	$get_pm = isset($_POST['pm']) ? trim($_POST['pm']) : '';
	if( isset($_POST['pmNonce']) && $get_pm && is_user_logged_in() ){
		if ( ! wp_verify_nonce( $_POST['pmNonce'], 'pm-nonce' ) ) {
			$message = __('安全认证失败，请重试！','dmeng');
		}else{
			$pm_title = json_encode(array(
				'pm' => $curauth->ID,
				'from' => $current_user->ID
			));
			if( add_dmeng_message( $curauth->ID, 'unrepm', '', $pm_title, $get_pm ) ){
				//~ 发邮件通知
				if(filter_var($curauth->user_email, FILTER_VALIDATE_EMAIL)){
					$m_headline = sprintf(__('%1$s给你发来了私信','dmeng'), $current_user->display_name);
					$m_content = '<h3>'.sprintf( __('%1$s，你好！','dmeng'), $curauth->display_name ).'</h3><p>'.sprintf( __('%1$s给你发来了<a href="%2$s" target="_blank">私信</a>，快去看看吧：<br> %3$s','dmeng'), $current_user->display_name, htmlspecialchars( add_query_arg('tab', 'message', get_author_posts_url( $curauth->ID )) ), $get_pm).'</p>';
					dmeng_send_email( $curauth->user_email, $m_headline, $m_content );
				}
				$message = __('发送成功！','dmeng');
			}
		}
	}
	
	//~ 私信end

//~ 页码start
$paged = max( 1, get_query_var('page') );
$number = get_option('posts_per_page', 10);
$offset = ($paged-1)*$number;
//~ 页码end

$item_html = '<li class="tip">'.__('没有找到记录','dmeng').'</li>';

	//~ 个人资料
if( $oneself ){
	
	$user_id = $curauth->ID;
	$avatar = $user_info->dmeng_avatar;
	$qq = dmeng_is_open_qq();
	$weibo = dmeng_is_open_weibo();

	if( isset($_POST['update']) && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) {
		
		$message = __('没有发生变化','dmeng');
		
		$update = sanitize_text_field($_POST['update']);
		
		if($update=='info'){
			$update_user_id = wp_update_user( array(
				'ID' => $user_id, 
				'nickname' => sanitize_text_field($_POST['display_name']),
				'display_name' => sanitize_text_field($_POST['display_name']),
				'user_url' => esc_url($_POST['url']),
				'description' => $_POST['description']
			 ) );
			$update_user_avatar = update_user_meta( $user_id , 'dmeng_avatar', sanitize_text_field($_POST['avatar']) );
			if ( ! is_wp_error( $update_user_id ) || $update_user_avatar ) $message = __('基本信息已更新','dmeng');
		}
		
		if($update=='pass'){
			$data = array();
			$data['ID'] = $user_id;
			$data['user_email'] = sanitize_text_field($_POST['email']);
			if( !empty($_POST['pass1']) && !empty($_POST['pass2']) && $_POST['pass1']===$_POST['pass2'] ) $data['user_pass'] = sanitize_text_field($_POST['pass1']);
			$user_id = wp_update_user( $data );
			if ( ! is_wp_error( $user_id ) ) $message = __('安全信息已更新','dmeng');
		}
		
		$message .= ' <a href="'.dmeng_get_current_page_url().'">'.__('点击刷新','dmeng').'</a>';
		
		$user_info = get_userdata($curauth->ID);

	}
}
	
	//~ 个人资料end
	
	
//~ 投稿start

if( isset($_GET['action']) && in_array($_GET['action'], array('new', 'edit')) && $oneself ){
	
	if( isset($_GET['id']) && is_numeric($_GET['id']) && get_post($_GET['id']) && intval(get_post($_GET['id'])->post_author) === get_current_user_id() ){
		$action = 'edit';
		$the_post = get_post($_GET['id']);
		$post_title = $the_post->post_title;
		$post_content = $the_post->post_content;
		foreach((get_the_category($_GET['id'])) as $category) { 
			$post_cat[] = $category->term_id; 
		}
	}else{
		$action = 'new';
		$post_title = !empty($_POST['post_title']) ? $_POST['post_title'] : '';
		$post_content = !empty($_POST['post_content']) ? $_POST['post_content'] : '';
		$post_cat = !empty($_POST['post_cat']) ? $_POST['post_cat'] : array();
	}

	if( isset($_POST['action']) && trim($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) {
		
		$title = sanitize_text_field($_POST['post_title']);
		$content = $_POST['post_content'];
		$cat = (!empty($_POST['post_cat'])) ? $_POST['post_cat'] : '';
		
		if( $title && $content ){
			
			if( mb_strlen($content,'utf8')<140 ){
				
				$message = __('提交失败，文章内容至少140字。','dmeng');
				
			}else{
				
				$status = sanitize_text_field($_POST['post_status']);
				
				if( $action==='edit' ){

					$new_post = wp_update_post( array(
						'ID' => intval($_GET['id']),
						'post_title'    => $title,
						'post_content'  => $content,
						'post_status'   => ( $status==='pending' ? 'pending' : 'draft' ),
						'post_author'   => get_current_user_id(),
						'post_category' => $cat
					) );

				}else{

					$new_post = wp_insert_post( array(
						  'post_title'    => $title,
						  'post_content'  => $content,
						  'post_status'   => ( $status==='pending' ? 'pending' : 'draft' ),
						  'post_author'   => get_current_user_id(),
						  'post_category' => $cat
						) );

				}
				
				if( is_wp_error( $new_post ) ){
					$message = __('操作失败，请重试或联系管理员。','dmeng');
				}else{
					
					update_post_meta( $new_post, 'dmeng_copyright_content', htmlspecialchars($_POST['post_copyright']) );
					
					wp_redirect(dmeng_get_user_url('post'));
				}

			}
		}else{
			$message = __('投稿失败，标题和内容不能为空！','dmeng');
		}
	}
}
//~ 投稿end
	
	

get_header();

get_header('masthead'); 

?>
<div id="main" class="container">
	<div class="row">
		<div id="content" class="col-lg-8 col-md-8 archive" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
			<?php echo dmeng_adsense('author','top');?>
			<div class="panel panel-default panel-archive">
				<div class="panel-body">
			<div class="media page-header panel-archive-title" id="author-page-header" itemscope itemtype="http://schema.org/Person">
				<a class="pull-left" href="<?php echo esc_url( get_author_posts_url( $curauth->ID ) ) ;?>">
					<?php echo dmeng_get_avatar( $curauth->ID , '80' , dmeng_get_avatar_type($curauth->ID) ); ?>
				</a>
			  <div class="media-body text-muted">
				<h1 class="h4 media-heading user-display-name"><?php echo '<span itemprop="name">'.$user_name.'</span>';?><?php
				if( $current_user->ID && $current_user->ID!=$curauth->ID ) echo '<small class="pm"><a href="'.add_query_arg('tab', 'message', get_author_posts_url( $curauth->ID )).'">'.__('私信TA','dmeng').'</a></small>';
				?></h1>
				<p class="small user-register-time"><?php
				 echo date( __('Y年m月d号','dmeng'), strtotime( $user_info->user_registered ) ) .  '<span>'.__('注册','dmeng').'</span>';
				 if($user_info->dmeng_latest_login) echo date( __('Y年m月d号','dmeng'), strtotime( $user_info->dmeng_latest_login ) ) . '<span>'.__('最后登录','dmeng').'</span>';
				 ?></p><?php 
						$description = $curauth->description;
						echo $description ? $description : __('没有个人说明','dmeng'); ?>
			  </div>
			</div>
<?php

	$tab_output = '';
	foreach( $tab_array as $tab_term ){
		$class = $get_tab==$tab_term ? ' class="active" ' : '';
		$tab_output .= sprintf('<li%s><a href="%s">%s</a></li>', $class, add_query_arg('tab', $tab_term, esc_url( get_author_posts_url( $curauth->ID ) )), $tabs[$tab_term]);
	}
	echo '<ul class="author-tab clearfix">'.$tab_output.'</ul>';
	if($message) echo '<div class="alert alert-success" role="alert">'.$message.'</div>'; 

//~ 礼品列表start
if( $get_tab=='gift' ) {
	
	$item_html = '<li class="tip">' . sprintf(__('成功兑换了 %1$s 件礼品','dmeng'), $gifts_num) . '</li>';

	if($oneself){

		$pages = ceil($gifts_num/$number);
		
		$giftLog = get_dmeng_user_gifts($curauth->ID, false, $number,$offset);

		if($giftLog){
			foreach( $giftLog as $log ){
				$gift_id = explode('_', $log->meta_key);
				$item_html .= '<li>'.$log->meta_value.' <span class="message-content">'.sprintf( __('兑换了%s', 'dmeng'), get_the_title($gift_id[1])).'</span></li>';
			}
			if($pages>1) $item_html .= '<li class="tip">' . sprintf(__('第 %1$s 页，共 %2$s 页，每页显示 %3$s 条。','dmeng'),$paged, $pages, $number). '</li>';
		}
	}
	
	echo '<ul id="author-message">'.$item_html.'</ul>';
	
	if($oneself) echo dmeng_pager($paged, $pages);
	
}
//~ 礼品列表end

//~ 积分列表start
if( $get_tab=='credit' ) {

	//~ 积分变更
	if ( current_user_can('edit_users') ) {

	?>
				<div class="panel panel-danger">
					<div class="panel-heading"><?php echo $curauth->display_name.__('积分变更（仅管理员可见）','dmeng');?></div>
					<div class="panel-body">
	<form id="creditform" role="form"  method="post">
		<input type="hidden" name="creditNonce" value="<?php echo  wp_create_nonce( 'credit-nonce' );?>" >
		<p>
			<label class="radio-inline"><input type="radio" name="creditChange" value="add" aria-required='true' required checked><?php _e('增加积分','dmeng');?></label>
			<label class="radio-inline"><input type="radio" name="creditChange" value="cut" aria-required='true' required><?php _e('减少积分','dmeng');?></label>
		</p>
		<div class="form-inline">
		  <div class="form-group">
			<div class="input-group">
			  <div class="input-group-addon"><?php _e('积分','dmeng');?></div>
			  <input class="form-control" type="text" name="creditNum" aria-required='true' required>
			</div>
		  </div>
		  <div class="form-group">
			<div class="input-group">
			  <div class="input-group-addon"><?php _e('备注','dmeng');?></div>
			  <input class="form-control" type="text" name="creditDesc" aria-required='true' required>
			</div>
		  </div>
		   <button class="btn btn-default" type="submit"><?php _e('提交','dmeng');?></button>
		</div>
		<p class="help-block"><?php _e('请谨慎操作！积分数只能填写数字，备注将显示在用户的积分记录中。','dmeng');?></p>
	</form>
				  </div>
				</div>

	<?php
	} //~ if ( current_user_can('edit_users') ) 

	$item_html = '<li class="tip">' . sprintf(__('共有 %1$s 个积分，其中 %2$s 个已消费， %3$s 个可用。','dmeng'), ($credit+$credit_void), $credit_void, $credit) . '</li>';

	if($oneself){
		$all = get_dmeng_message($curauth->ID, 'count', "msg_type='credit'");
		$pages = ceil($all/$number);
		
		$creditLog = get_dmeng_credit_message($curauth->ID, $number,$offset);

		if($creditLog){
			foreach( $creditLog as $log ){
				$item_html .= '<li>'.$log->msg_date.' <span class="message-content">'.$log->msg_title.'</span></li>';
			}
			if($pages>1) $item_html .= '<li class="tip">' . sprintf(__('第 %1$s 页，共 %2$s 页，每页显示 %3$s 条。','dmeng'),$paged, $pages, $number). '</li>';
		}
	}
	
	echo '<ul id="author-message">'.$item_html.'</ul>';
	
	if($oneself) echo dmeng_pager($paged, $pages);

	?>
    <table class="table table-bordered credit-table">
      <thead>
        <tr class="active">
          <th><?php _e('积分方法','dmeng');?></th>
          <th><?php _e('一次得分','dmeng');?></th>
          <th><?php _e('可用次数','dmeng');?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php _e('注册奖励','dmeng');?></td>
          <td><?php printf( __('%1$s 分','dmeng'), get_option('dmeng_reg_credit','50'));?></td>
          <td><?php _e('只有 1 次','dmeng');?></td>
        </tr>
        <tr>
          <td><?php _e('文章投稿','dmeng');?></td>
          <td><?php printf( __('%1$s 分','dmeng'), get_option('dmeng_rec_post_credit','50'));?></td>
          <td><?php printf( __('每天 %1$s 次','dmeng'), get_option('dmeng_rec_post_num','5'));?></td>
        </tr>
        <tr>
          <td><?php _e('评论回复','dmeng');?></td>
          <td><?php printf( __('%1$s 分','dmeng'), get_option('dmeng_rec_comment_credit','5'));?></td>
          <td><?php printf( __('每天 %1$s 次','dmeng'), get_option('dmeng_rec_comment_num','50'));?></td>
        </tr>
        <tr>
          <td><?php _e('访问推广','dmeng');?></td>
          <td><?php printf( __('%1$s 分','dmeng'), get_option('dmeng_rec_view_credit','5'));?></td>
          <td><?php printf( __('每天 %1$s 次','dmeng'), get_option('dmeng_rec_view_num','50'));?></td>
        </tr>
        <tr>
          <td><?php _e('注册推广','dmeng');?></td>
          <td><?php printf( __('%1$s 分','dmeng'), get_option('dmeng_rec_reg_credit','50'));?></td>
          <td><?php printf( __('每天 %1$s 次','dmeng'), get_option('dmeng_rec_reg_num','5'));?></td>
        </tr>
      </tbody>
    </table>
	<?php

} 
//~ 积分列表end

//~ 默认文章列表start
if( $get_tab=='post' ) {
	
	$can_post_cat = json_decode(get_option('dmeng_can_post_cat'));
	$cat_count = count($can_post_cat);

	if( isset($_GET['action']) && in_array($_GET['action'], array('new', 'edit')) && $cat_count && is_user_logged_in() && $oneself && current_user_can('edit_posts') ){
		
		echo '<ul id="author-message"><li class="tip">'.__('请发表你自己的文章', 'dmeng').'</ul></li>';
?>
			<article class="panel panel-default archive" role="main">
					<div class="panel-body">
						<h3 class="page-header"><?php _e('投稿','dmeng');?> <small><?php _e('POST NEW','dmeng');?></small></h3>
						<form role="form" method="post">
							<div class="form-group">
								<input type="text" class="form-control" name="post_title" placeholder="<?php _e('在此输入标题','dmeng');?>" value="<?php echo $post_title;?>" aria-required='true' required>
							</div>
							<div class="form-group">
								<?php wp_editor(  wpautop($post_content), 'post_content', array('media_buttons'=>false, 'quicktags'=>false, 'editor_class'=>'form-control', 'editor_css'=>'<style>.wp-editor-container{border:1px solid #ddd;}</style>' ) ); ?>
							</div>
							<div class="form-group">
						<?php
							$can_post_cat = json_decode(get_option('dmeng_can_post_cat'));
							if($can_post_cat){
								$post_cat_output = '<p class="help-block">'.__('选择文章分类', 'dmeng').'</p>';
								$post_cat_output .= '<select name="post_cat[]" class="form-control">';
								foreach ( $can_post_cat as $term_id ) {
									$category = get_category( $term_id );
									//~ if( (!empty($post_cat)) && in_array($category->term_id,$post_cat)) 
									$post_cat_output .= '<option value="'.$category->term_id.'">'. $category->name.'</option>';
								}
								$post_cat_output .= '</select>';
								echo $post_cat_output;
							}
						?>
							</div>
							<div class="form-group">
								<p class="help-block"><?php _e('文章来源或版权信息，{link}代表文章链接，{title}代表文章标题，{url}代表本站首页地址，{name}代表本站名称', 'dmeng');?></p>
								<?php
								$cc = '';
								if(isset($_GET['id'])) $cc = get_post_meta( intval($_GET['id']), 'dmeng_copyright_content', true );
								$copyright_content = $cc ? $cc : get_option('dmeng_copyright_content_default',sprintf(__('原文链接：%s，转发请注明来源！','dmeng'),'<a href="{link}" rel="author">{title}</a>'));
								?>
								<textarea name="post_copyright" rows="1" cols="50" class="form-control"><?php echo stripcslashes(htmlspecialchars_decode($copyright_content));?></textarea>
							</div>
							<div class="form-group text-right">
								<select name="post_status">
									<option value ="pending"><?php _e('提交审核','dmeng');?></option>
									<option value ="draft"><?php _e('保存草稿','dmeng');?></option>
								</select>
								<input type="hidden" name="action" value="update">
								<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
								<button type="submit" class="btn btn-success"><?php _e('确认操作','dmeng');?></button>
							</div>	
						</form>
					</div>
			 </article>
<?php
		
	}else{

		if($cat_count){

			$item_html = sprintf( __('现有%s个分类接受投稿。', 'dmeng'), $cat_count );
			
			if( is_user_logged_in() && !current_user_can('edit_posts') ){
					
					$item_html .= __('遗憾的是，你现在登录的账号没有投稿权限！', 'dmeng');
					
				}else{
					
					$item_html .= '<a href="'.( is_user_logged_in() ? add_query_arg(array('tab'=>'post','action'=>'new'), get_author_posts_url($current_user->ID)) : wp_login_url() ).'">'.__('点击投稿', 'dmeng').'</a>';
			}
		}else{

			if( have_posts() ) $item_html = sprintf( __('发表了 %s 篇文章', 'dmeng'), $posts_count );
		}
		
		echo '<ul id="author-message"><li class="tip">'.$item_html.'</ul></li>';
	
	global $wp_query;
	$args = is_user_logged_in() ? array_merge( $wp_query->query_vars, array( 'post_status' => array( 'publish', 'pending', 'draft' ) ) ) : $wp_query->query_vars;
	query_posts( $args );
	
		while ( have_posts() ) : the_post();
			get_template_part('content','archive');
		endwhile; // end of the loop. 
		dmeng_paginate();

	wp_reset_query();
	}
}
//~ 默认文章列表end

//~ 评论start
if( $get_tab=='comment' ) {

	$comments_status = $oneself ? '' : 'approve';
	
	$all = get_comments( array('status' => '', 'user_id'=>$curauth->ID, 'count' => true) );
	$approve = get_comments( array('status' => '1', 'user_id'=>$curauth->ID, 'count' => true) );
	
	$pages = $oneself ? ceil($all/$number) : ceil($approve/$number);

	$comments = get_comments(array(
		'status' => $comments_status,
		'order' => 'DESC',
		'number' => $number,
		'offset' => $offset,
		'user_id' => $curauth->ID
	));

	if($comments){
		$item_html = '<li class="tip">' . sprintf(__('共有 %1$s 条评论，其中 %2$s 条已获准， %3$s 条正等待审核。','dmeng'),$all, $approve, $all-$approve) . '</li>';
		foreach( $comments as $comment ){
			$item_html .= ' <li>';
			if($comment->comment_approved!=1) $item_html .= '<small class="text-danger">'.__( '这条评论正在等待审核','dmeng' ).'</small>';
			$item_html .= '<div class="message-content">'.$comment->comment_content . '</div>';
			$item_html .= '<a class="info" href="'.htmlspecialchars( get_comment_link( $comment->comment_ID) ).'">'.sprintf(__('%1$s 发表在 %2$s','dmeng'),$comment->comment_date,get_the_title($comment->comment_post_ID)).'</a>';
			$item_html .= '</li>';
		}
		if($pages>1) $item_html .= '<li class="tip">'.sprintf(__('第 %1$s 页，共 %2$s 页，每页显示 %3$s 条。','dmeng'),$paged, $pages, $number).'</li>';
	}

	echo '<ul id="author-message">'.$item_html.'</ul>'.dmeng_pager($paged, $pages);
}
//~ 评论end

//~ 赞start
if( $get_tab=='like' ) {

	if($up_count){

		$item_html = '<li class="tip">' . sprintf(__('共有 %1$s 次赞，其中包括 %2$s 篇文章， %3$s 条评论。','dmeng'),$up_count, $up_posts_count, $up_comments_count) . '</li>';
	
		$up_result = get_dmeng_user_vote($curauth->ID, false, '', 'up', $number, $offset);
		
		$up_data = array();
		
		foreach( $up_result as $up_meta ){
			$up_key = explode('_', $up_meta->meta_key);
			
			if($up_key[1]=='post'){
					
				query_posts( 'p='.$up_key[2] );
				
				if( have_posts() ) {
				
					while ( have_posts() ) : the_post();

						$up_data[] = array(
							'title' => get_the_title(),
							'url' => get_permalink(),
							'excerpt' => apply_filters( 'the_excerpt', get_the_excerpt() ),
							'type' => 'post',
							'type_label' => __('文章', 'dmeng'),
							'id' => get_the_ID()
						);
						
					endwhile;
				}
				
				wp_reset_query();
					
			}
			
			if($up_key[1]=='comment'){
				
				$comment = get_comment( $up_key[2] ); 
				
						$up_data[] = array(
							'title' => sprintf( __('%s的评论', 'dmeng'), $comment->comment_author),
							'url' => get_comment_link($comment->comment_ID),
							'excerpt' => $comment->comment_content,
							'type' => 'comment',
							'type_label' => __('评论', 'dmeng'),
							'id' => $comment->comment_ID
						);
				
			}

		}
		
		if( $up_data ){
			foreach( $up_data as $up_term ){
				$item_html .= '<li class="up"><div class="up_content">'.$up_term['excerpt'].'</div><p class="info">['.$up_term['type_label'].']<a class="up_title" href="'.$up_term['url'].'">'.$up_term['title'].'</a> <span class="glyphicon glyphicon-thumbs-up"></span>'.intval(get_metadata($up_term['type'], $up_term['id'], 'dmeng_votes_up', true)).'<span class="glyphicon glyphicon-thumbs-down"></span>'.intval(get_metadata($up_term['type'], $up_term['id'], 'dmeng_votes_down', true)).'</p></li>';
			}
		}
	
	}
	
	$pages = ceil($up_count/$number);
	
	if($pages>1) $item_html .= '<li class="tip">'.sprintf(__('第 %1$s 页，共 %2$s 页，每页显示 %3$s 条。','dmeng'),$paged, $pages, $number).'</li>';
	
	echo '<ul id="author-message">'.$item_html.'</ul>'.dmeng_pager($paged, $pages);
	
}
//~ 赞end

//~ 消息start
if( $get_tab=='message' ) {

	if($current_user->ID==$curauth->ID){
		
		$all_sql = "( msg_type='read' OR msg_type='unread' OR msg_type='repm' OR msg_type='unrepm' )";

		$all = get_dmeng_message($curauth->ID, 'count', $all_sql);
		$pages = ceil($all/$number);
		
		$unread = intval(get_dmeng_message($curauth->ID, 'count', "msg_type='unread'"));

		$mLog = get_dmeng_message($curauth->ID, '', $all_sql, $number,$offset);
		
		if($mLog){
			$item_html = '<li class="tip">' . sprintf(__('共有 %1$s 条消息，其中 %2$s 条是新消息（绿色标注）。','dmeng'), $all, $unread) . '</li>';
			foreach( $mLog as $log ){
				$unread_tip = $unread_class = '';
				if(in_array($log->msg_type, array('unread', 'unrepm'))){
					$unread_tip = '<span class="tag">'.__('新！', 'dmeng').'</span>';
					$unread_class = ' class="unread"';
					update_dmeng_message_type( $log->msg_id, $curauth->ID , ltrim($log->msg_type, 'un') );
				}
				$msg_title =  $log->msg_title;
				if(in_array($log->msg_type, array('repm', 'unrepm'))){
					$msg_title_data = json_decode($log->msg_title);
					$msg_title = get_the_author_meta('display_name', intval($msg_title_data->from));
					$msg_title = sprintf(__('%s发来的私信','dmeng'), $msg_title).' <a href="'.add_query_arg('tab', 'message', get_author_posts_url(intval($msg_title_data->from))).'#'.$log->msg_id.'">'.__('查看对话','dmeng').'</a>';
				}
				$item_html .= '<li'.$unread_class.'><div class="message-content">'.htmlspecialchars_decode($log->msg_content).' </div><p class="info">'.$unread_tip.'  '.$msg_title.'  '.$log->msg_date.'</p></li>';
			}
			if($pages>1) $item_html .= '<li class="tip">'.sprintf(__('第 %1$s 页，共 %2$s 页，每页显示 %3$s 条。','dmeng'),$paged, $pages, $number).'</li>';
		}
		
	}else{
		
		if( is_user_logged_in() ){
			
			$item_html = '<li class="tip">'.sprintf(__('与 %s 对话','dmeng'), $user_info->display_name).'</li><li><form id="pmform" role="form" method="post"><input type="hidden" name="pmNonce" value="'.wp_create_nonce( 'pm-nonce' ).'" ><p><textarea class="form-control" rows="3" name="pm" required></textarea></p><p class="clearfix"><a class="btn btn-link pull-left" href="'.add_query_arg('tab', 'message', get_author_posts_url($current_user->ID)).'">'.__('查看我的消息','dmeng').'</a><button type="submit" class="btn btn-primary pull-right">'.__('确定发送','dmeng').'</button></p></form></li>';
			
			$all = get_dmeng_pm( $curauth->ID, $current_user->ID, true );
			$pages = ceil($all/$number);
			
			$pmLog = get_dmeng_pm( $curauth->ID, $current_user->ID, false, false, $number, $offset );
			if($pmLog){
				foreach( $pmLog as $log ){
					$pm_data = json_decode($log->msg_title);
					if( $pm_data->from==$curauth->ID ){
						update_dmeng_message_type( $log->msg_id, $curauth->ID , 'repm' );
					}
					$item_html .= '<li id="'.$log->msg_id.'"><div class="message-content clearfix"><a class="'.( $pm_data->from==$current_user->ID ? 'pull-right' : 'pull-left' ).'" href="'.get_author_posts_url($pm_data->from).'">'.dmeng_get_avatar( $pm_data->from , '34' , dmeng_get_avatar_type($pm_data->from), false ).'</a><div class="pm-box"><div class="pm-content'.( $pm_data->from==$current_user->ID ? '' : ' highlight' ).'">'.htmlspecialchars_decode($log->msg_content).'</div><p class="pm-date">'.date_i18n( get_option( 'date_format' ).' '.get_option( 'time_format' ), strtotime($log->msg_date)).'</p></div></div></li>';
				}
			}
			
			if($pages>1) $item_html .= '<li class="tip">'.sprintf(__('第 %1$s 页，共 %2$s 页，每页显示 %3$s 条。','dmeng'),$paged, $pages, $number).'</li>';

		}else{
			$item_html = '<li class="tip">'.sprintf(__('私信功能需要<a href="%s">登录</a>才可使用！'), wp_login_url() ).'</li>';
		}
	}
	
	echo '<ul id="author-message">'.$item_html.'</ul>'.dmeng_pager($paged, $pages);

}
//~ 消息end

//~ 资料start
if( $get_tab=='profile' ) {

		$avatar_type = array(
			'default' => __('默认头像', 'dmeng'),
			'qq' => __('腾讯QQ头像', 'dmeng'),
			'weibo' => __('新浪微博头像', 'dmeng'),
		);
		
		$author_profile = array(
			__('头像来源','dmeng') => $avatar_type[dmeng_get_avatar_type($user_info->ID)],
			__('用户名','dmeng') => $user_info->user_login,
			__('昵称','dmeng') => $user_info->display_name,
			__('站点','dmeng') => $user_info->user_url,
			__('个人说明','dmeng') => $user_info->description
		);
		
		$profile_output = '';
		foreach( $author_profile as $pro_name=>$pro_content ){
			$profile_output .= '<tr><td class="title">'.$pro_name.'</td><td>'.$pro_content.'</td></tr>';
		}
		
		$days_num = round(( strtotime(date('Y-m-d')) - strtotime( $user_info->user_registered ) ) /3600/24);
		
		echo '<ul id="author-message"><li class="tip">'.sprintf(__('%s来%s已经%s天了', 'dmeng') , $user_info->display_name, get_bloginfo('name'), ( $days_num>1 ? $days_num : 1 ) ).'</li></ul>'.'<table id="author-profile"><tbody>'.$profile_output.'</tbody></table>';
		
	if( $oneself ){
		
	?>

<form id="info-form" class="form-horizontal" role="form" method="post">
	<input type="hidden" name="update" value="info">
	<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
			<div class="page-header">
				<h3 id="info"><?php _e('基本信息','dmeng');?> <small><?php _e('公开资料','dmeng');?></small></h>
			</div>

	<div class="form-group">
		<label class="col-sm-3 control-label"><?php _e('头像','dmeng');?></label>
		<div class="col-sm-9">

<div class="radio">
<?php echo dmeng_get_avatar( $user_info->ID , '40' ); ?>
  <label>
	<input type="radio" name="avatar"  value="default" <?php if( ($avatar!='qq' || dmeng_is_open_qq($user_info->ID)===false) && ($avatar!='weibo' || dmeng_is_open_weibo($user_info->ID)===false) ) echo 'checked';?>> 默认头像
  </label>
</div>

<?php if(dmeng_is_open_qq($user_info->ID)){ ?>
<div class="radio">
<?php echo dmeng_get_avatar( $user_info->ID , '40' , 'qq' ); ?>
  <label>
    <input type="radio" name="avatar" value="qq" <?php if($avatar=='qq') echo 'checked';?>> <?php _e('QQ头像', 'dmeng');?>
  </label>
</div>
<?php } ?>

<?php if(dmeng_is_open_weibo($user_info->ID)){ ?>
<div class="radio">
<?php echo dmeng_get_avatar( $user_info->ID , '40' , 'weibo' ); ?>
  <label>
    <input type="radio" name="avatar" value="weibo" <?php if($avatar=='weibo') echo 'checked';?>> <?php _e('微博头像', 'dmeng');?>
  </label>
</div>
<?php } ?>

		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label"><?php _e('用户名','dmeng');?></label>
		<div class="col-sm-9">
			<p class="form-control-static"><?php echo $user_info->user_login;?></p>
		</div>
	</div>
	
	<div class="form-group">
		<label for="display_name" class="col-sm-3 control-label"><?php _e('昵称','dmeng');?></label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="display_name" name="display_name" value="<?php echo $user_info->display_name;?>">
		</div>
	</div>

	<div class="form-group">
		<label for="url" class="col-sm-3 control-label"><?php _e('站点','dmeng');?></label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="url" name="url" value="<?php echo $user_info->user_url;?>">
		</div>
	</div>
	
	<div class="form-group">
		<label for="description" class="col-sm-3 control-label"><?php _e('个人说明','dmeng');?></label>
		<div class="col-sm-9">
			<textarea class="form-control" rows="3" name="description" id="description"><?php echo $user_info->description;?></textarea>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-primary"><?php _e('保存更改','dmeng');?></button>
		</div>
	</div>
</form>
<?php if( $qq || $weibo ) { ?>
<form id="open-form" class="form-horizontal" role="form" method="post">
			<div class="page-header">
				<h3 id="open"><?php _e('绑定账号','dmeng');?> <small><?php _e('可用于直接登录','dmeng');?></small></h>
			</div>
			
	<?php if($qq){ ?>
		<div class="form-group">
			<label class="col-sm-3 control-label"><?php _e('QQ账号','dmeng');?></label>
			<div class="col-sm-9">
		<?php  if(dmeng_is_open_qq($user_info->ID)) { ?>
			<span class="help-block"><?php _e('已绑定','dmeng');?> <a href="<?php echo home_url('/?connect=qq&action=logout'); ?>"><?php _e('点击解绑','dmeng');?></a></span>
			<?php echo dmeng_get_avatar( $user_info->ID , '100' , 'qq' ); ?>
		<?php }else{ ?>
			<a class="btn btn-primary" href="<?php echo home_url('/?connect=qq&action=login&redirect='.urlencode(get_edit_profile_url())); ?>"><?php _e('绑定QQ账号','dmeng');?></a>
		<?php } ?>
			</div>
		</div>
	<?php } ?>

	<?php if($weibo){ ?>
		<div class="form-group">
			<label class="col-sm-3 control-label"><?php _e('微博账号','dmeng');?></label>
			<div class="col-sm-9">
		<?php if(dmeng_is_open_weibo($user_info->ID)) { ?>
			<span class="help-block"><?php _e('已绑定','dmeng');?> <a href="<?php echo home_url('/?connect=weibo&action=logout'); ?>"><?php _e('点击解绑','dmeng');?></a></span>
			<?php echo dmeng_get_avatar( $user_info->ID , '100' , 'weibo' ); ?>
		<?php }else{ ?>
			<a class="btn btn-danger" href="<?php echo home_url('/?connect=weibo&action=login&redirect='.urlencode(get_edit_profile_url())); ?>"><?php _e('绑定微博账号','dmeng');?></a>
		<?php } ?>
			</div>
		</div>
	<?php } ?>
</form>
<?php } ?>
<form id="pass-form" class="form-horizontal" role="form" method="post">
	<input type="hidden" name="update" value="pass">
	<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
			<div class="page-header">
				<h3 id="pass"><?php _e('账号安全','dmeng');?> <small><?php _e('仅自己可见','dmeng');?></small></h>
			</div>
	<div class="form-group">
		<label for="email" class="col-sm-3 control-label"><?php _e('电子邮件 (必填)','dmeng');?></label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="email" name="email" value="<?php echo $user_info->user_email;?>" aria-required='true' required>
		</div>
	</div>
	<div class="form-group">
		<label for="pass1" class="col-sm-3 control-label"><?php _e('新密码','dmeng');?></label>
		<div class="col-sm-9">
			<input type="password" class="form-control" id="pass1" name="pass1" >
			<span class="help-block"><?php _e('如果您想修改您的密码，请在此输入新密码。不然请留空。','dmeng');?></span>
		</div>
	</div>
	<div class="form-group">
		<label for="pass2" class="col-sm-3 control-label"><?php _e('重复新密码','dmeng');?></label>
		<div class="col-sm-9">
			<input type="password" class="form-control" id="pass2" name="pass2" >
			<span class="help-block"><?php _e('再输入一遍新密码。 提示：您的密码最好至少包含7个字符。为了保证密码强度，使用大小写字母、数字和符号（例如! " ? $ % ^ & )）。','dmeng');?></span>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-primary"><?php _e('保存更改','dmeng');?></button>
		</div>
	</div>
</form>
	<?php
	}
} 
//~ 资料end

			?>
				</div>
			</div>
			<?php echo dmeng_adsense('author','bottom');?>
		 </div><!-- #content -->
		<?php get_sidebar();?>
	</div>
 </div><!-- #main -->
<?php get_footer('colophon'); ?>
<?php get_footer(); ?>
