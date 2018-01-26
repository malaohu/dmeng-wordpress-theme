<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 
 * 邮件
 * 
 */


//~ SMTP发信 @author 多梦 at 2014.06.22 
//~ 详细 https://github.com/PHPMailer/PHPMailer
function dmeng_phpmailer( $mail ) {
	$smtp = json_decode(get_option('dmeng_smtp','{"option":"0","host":"","ssl":"0","port":"25","user":"","pass":"","name":""}'));
	if(intval($smtp->option)){
		$mail->IsSMTP();
		$mail->SMTPAuth = true; 
		$mail->isHTML(true);
		//~ 发信服务器
		$mail->Host = sanitize_text_field($smtp->host);
		//~ 端口
		$mail->Port = intval($smtp->port);
		//~ 发信用户
		$mail->Username = sanitize_text_field($smtp->user);
		//~ 密码
		$mail->Password = sanitize_text_field($smtp->pass);
		//~ SSL
		if(intval($smtp->ssl)) $mail->SMTPSecure = 'ssl';
		//~ 来源（显示发信用户）
		$mail->From = sanitize_text_field($smtp->user);
		//~ 昵称
		$mail->FromName = sanitize_text_field($smtp->name);
	}
}
add_action( 'phpmailer_init', 'dmeng_phpmailer' );

//~ 邮件签名尾巴
function dmeng_email_content_signature($content){
	return $content.'<br><p style="color: #777777;">'.sprintf(__('本邮件由<a href="%1$s" target="_blank">%2$s</a>发送','dmeng') , get_bloginfo('url') , get_bloginfo('name')).'</p>';
}
add_filter('dmeng_email_content', 'dmeng_email_content_signature');

//~ 发邮件
function dmeng_send_email( $email, $title, $content ){
	$content = trim(apply_filters( 'dmeng_email_content', $content ));
	wp_mail( $email, $title, $content );
	return;
}
add_action( 'dmeng_send_email_event', 'dmeng_send_email', 10, 3 );

//~ 评论回复邮件通知
function dmeng_comment_mail_notify($comment_id, $comment_object){
	
	if( $comment_object->comment_approved != 1 || !empty($comment_object->comment_type) ) return;
	
	$send_email = array();
	
	$post = get_post($comment_object->comment_post_ID);
	$post_author_email = get_user_by( 'id' , $post->post_author)->user_email;
	
	//~ 给文章作者的通知

		$send_email[] = array(
			'address' => $post_author_email,
			'uid' => $post->post_author,
			'title' => sprintf( __('%1$s在%2$s中回复你','dmeng'), $comment_object->comment_author, $post->post_title ),
			'headline' => sprintf( __('%1$s，你好！','dmeng'), get_user_by( 'id' , $post->post_author)->display_name ),
			'content' => sprintf( __('%1$s在文章<a href="%2$s" target="_blank">%3$s</a>中回复你了，快去看看吧：<br> %4$s','dmeng'), $comment_object->comment_author, htmlspecialchars( get_comment_link( $comment_id ) ), $post->post_title, $comment_object->comment_content )
		);

	
	//~ 给上级评论的通知
	if( $comment_object->comment_parent ) {
		$comment_parent = get_comment($comment_object->comment_parent);
		$comment_parent_email = $comment_parent->comment_author_email;
		if( $comment_object->comment_author_email!=$comment_parent->comment_author_email ){
			$send_email[] = array(
				'address' => $comment_parent_email,
				'uid' => $comment_object->comment_parent,
				'title' => sprintf( __('%1$s在%2$s中回复你','dmeng'), $comment_object->comment_author, $post->post_title ),
				'headline' => sprintf( __('%1$s，你好！','dmeng'), $comment_parent->comment_author ),
				'content' => sprintf( __('%1$s在文章<a href="%2$s" target="_blank">%3$s</a>中回复你了，快去看看吧：<br> %4$s','dmeng'), $comment_object->comment_author, htmlspecialchars( get_comment_link( $comment_id ) ), $post->post_title, $comment_object->comment_content )
			);
		}
	}
	
	//~ 给管理员的通知
	$admin_email = get_option('admin_email');
	if( $post_author_email!=$admin_email ){
		$send_email[] = array(
			'address' => $admin_email,
			'uid' => 0,
			'title' => sprintf( __('%1$s上的文章有了新的回复','dmeng'), get_bloginfo('name') ),
			'headline' => sprintf( __('%1$s管理员，你好！','dmeng'), get_bloginfo('name') ),
			'content' => sprintf( __('%1$s回复了文章<a href="%2$s" target="_blank">%3$s</a>，快去看看吧：<br> %4$s','dmeng'), $comment_object->comment_author, htmlspecialchars( get_comment_link( $comment_id ) ), $post->post_title, $comment_object->comment_content )
		);
	}
	
	if( $send_email ){
	
		foreach ( $send_email as $email ){
			$content = '<h3>'.$email['headline'].'</h3>';
			$content .= '<p>'.$email['content'].'</p>';
			
			//~ 添加消息通知
			if(intval($email['uid'])>0){
				 add_dmeng_message($email['uid'], 'unread', current_time('mysql'), $email['title'], $email['content']);
			}
			
			//~ 如果有设置邮箱就发送邮件通知
			if(filter_var( $email['address'], FILTER_VALIDATE_EMAIL)){
				dmeng_send_email( $email['address'], $email['title'], $content );
			}
		}
		
	}

}
add_action('wp_insert_comment', 'dmeng_comment_mail_notify' , 99, 2 );

function dmeng_retrieve_password_message($message, $key){

	if ( strpos( $_POST['user_login'], '@' ) ) {
		$user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );
	} else {
		$login = trim($_POST['user_login']);
		$user_data = get_user_by('login', $login);
	}
	
	$user_login = $user_data->user_login;
	
	$message = '<h3>'.sprintf( __('%1$s，你好！','dmeng'), $user_data->display_name ).'</h3>';
	$message .= '<p>';
	$message .= sprintf( __('有人要求重设您在%1$s的帐号密码（用户名：%2$s）。若这不是您本人要求的，请忽略本邮件，一切如常。 要重置您的密码，请打开下面的链接：%3$s','dmeng'),
												get_bloginfo('name'),
												$user_login,
												network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login')
												);
	$message .= '</p>';

	$content = apply_filters( 'dmeng_email_content', $message );
	return $content;
}
add_filter('retrieve_password_message', 'dmeng_retrieve_password_message', 10,  2);

function dmeng_pre_email_message($message){
	return '<pre>'.apply_filters( 'dmeng_email_content', $message ).'</pre>';
}
add_filter('comment_moderation_text', 'dmeng_pre_email_message', 10,  1);

