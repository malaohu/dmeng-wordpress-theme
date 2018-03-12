<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 自定义小工具 @author 多梦 at 2014.06.21 
 * 
 */

class DmengAnalyticsWidget extends WP_Widget {

	function __construct() {
		parent::__construct( 'analytics', __( ' 站点统计' , 'dmeng' ) , array('classname' => 'widget_analytics', 'description' => __( '站点统计信息' , 'dmeng' ) ) );
	}

	function widget( $args, $instance ) {

		extract($args);

		$output = apply_filters( 'dmeng_pre_analytics', null, $args, 'a' );
		
		if ( null !== $output ) {
			echo $output;
			return;
		}

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$output .= $before_widget;
		if ( $title )
			$output .= $before_title . $title . $after_title;

		if(isset($instance['user']) && trim($instance['user'])=='on'){
			$users = count_users();
			$users = $users['total_users'];
		}else{
			$users = '';
		}

		$list = array(
			'post' => array( __('文章','dmeng'), wp_count_posts('post')->publish ),
			'cat' => array( __('分类','dmeng'), wp_count_terms('category') ),
			'tag' => array( __('标签','dmeng'), wp_count_terms('post_tag') ),
			'user' => array( __('用户','dmeng'), $users ),
			'comment' => array( __('评论','dmeng'), wp_count_comments()->total_comments ),
			'view' => array( __('浏览总数','dmeng'), get_dmeng_traffic_all() ),
			'search' => array( __('搜索次数','dmeng'), get_dmeng_traffic_all('search') )
		);
	
		$output .= '<ul>';
		
		$output .= '<li class="update">'.__('统计时间', 'dmeng') . ' : <time title="'.sprintf(__('统计数据最后更新时间：%s', 'dmeng') , current_time('mysql') ).' ">'.current_time('m-d H:i').'</time></li>';
		
		foreach( $list as $key=>$value ){
			if($instance[$key]) $output .= '<li>'.$value[0].' : '.$value[1] .'</li>';
		}
		
		$output .= '</ul>';

		$output .= $after_widget;
		
		 $output = apply_filters( 'dmeng_analytics', $output, $args, 'a' );
		 
		echo $output;
		
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'post' => 'on', 'cat' => 'on', 'tag' => 'on', 'user' => 'on', 'comment' => 'on', 'view' => 'on', 'search' => 'on') );
		$title = $instance['title'];
?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题：','dmeng');?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>
	<p><?php _e('选择要显示的项目。','dmeng');?></p>
<p>
	
	<?php
	
	$list = array(
		'post' => __('文章总数','dmeng'),
		'cat' => __('文章分类','dmeng'),
		'tag' => __('文章标签','dmeng'),
		'user' => __('用户总数','dmeng'),
		'comment' => __('评论总数','dmeng'),
		'view' => __('浏览总数','dmeng'),
		'search' => __('搜索次数','dmeng')
	);
	
	foreach( $list as $key=>$value ){
	?>
	<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id($key); ?>" name="<?php echo $this->get_field_name($key); ?>" <?php if(isset($instance[$key])&&trim($instance[$key])=='on') echo 'checked="checked"';?>> <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $value?></label><br>
	<?php
	}
	?>

</p>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array());
		$list = array('title', 'post', 'cat', 'tag', 'user', 'comment', 'view', 'search');
		foreach( $list as $key ){
			$instance[$key] = strip_tags($new_instance[$key]);
		}
		
		do_action( 'dmeng_update_analytics', $instance, 'a' );

		return $instance;
	}

}

class DmengOpenWidget extends WP_Widget {

	function __construct() {
		parent::__construct( 'open_login', __( '登录和资料' , 'dmeng' ) , array('classname' => 'widget_open_login', 'description' => __( '用户登录&个人资料展示' , 'dmeng' ) ) );
	}

	function widget( $args, $instance ) {

		extract($args);

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$output = $before_widget;
		if ( $title )
			$output .= $before_title . $title . $after_title;
			
			$output .= '<ul class="user-profile">';

	if( is_user_logged_in() ){ 
			
			$output .= dmeng_user_profile_widget();
			
	}else{

		if( $instance['qq'] && dmeng_is_open_qq() ) {
			$output .= '<li class="icon qq"><a href="'.home_url('/qqlogin?&action=login&redirect='.urlencode(dmeng_get_current_page_url())).'" rel="nofollow">'.__( '使用QQ账号登录' , 'dmeng' ).'</a></li>';
		}
		
		if( $instance['weibo'] && dmeng_is_open_weibo() ) {
			$output .= '<li class="icon weibo"><a href="'.home_url('/?connect=weibo&action=login&redirect='.urlencode(dmeng_get_current_page_url())).'" rel="nofollow">'.__( '使用微博账号登录' , 'dmeng' ).'</a></li>';
		} 
		
		if( $instance['wordpress'] ) {
			$output .= '<li class="icon wordpress"><a href="'.wp_login_url().'" rel="nofollow">'.__( '使用本地账号登录' , 'dmeng' ).'</a></li>';
		}
		
	}

		$output .= '<ul>';
		
		$output .= $after_widget;
		
		echo $output;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'qq' => 'on', 'weibo' => 'on', 'wordpress' => 'on') );
		$title = $instance['title'];
?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题：','dmeng');?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>
	<p><?php _e('选择要显示的项目。','dmeng');?></p>
<p>
	<?php
	
	$list = array(
		'qq' => __('显示QQ登录（如果有启用QQ登录）','dmeng'),
		'weibo' => __('显示微博登录（如果有启用微博登录）','dmeng'),
		'wordpress' => __('显示本地登录','dmeng'),
	);
	
	foreach( $list as $key=>$value ){
	?>
	<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id($key); ?>" name="<?php echo $this->get_field_name($key); ?>" <?php if(isset($instance[$key])&&trim($instance[$key])=='on') echo 'checked="checked"';?>> <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $value?></label><br>
	<?php
	}
	?>

</p>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array());
		$list = array('title', 'qq', 'weibo', 'wordpress');
		foreach( $list as $key ){
			$instance[$key] = strip_tags($new_instance[$key]);
		}
		return $instance;
	}
	
}

class DmengCreditRankWidget extends WP_Widget {

	function __construct() {
		parent::__construct( 'creditRank', __( ' 积分排行榜' , 'dmeng' ) , array('classname' => 'widget_credit_rank', 'description' => __( '用户可用积分排行榜' , 'dmeng' ) ) );
	}

	function widget( $args, $instance ) {
		extract($args);
		
		$output = apply_filters( 'dmeng_pre_credit_rank', null, $args, 'cr' );
		
		if ( null !== $output ) {
			echo $output;
			return;
		}
		
		ob_start();

		global $wpdb;
		$rank = $wpdb->get_results( "SELECT user_id,meta_value FROM $wpdb->usermeta WHERE meta_key = 'dmeng_credit' ORDER BY -meta_value ASC LIMIT 6 ");
		if($rank){

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

?>
<ul>
	<?php foreach ( $rank as $term ){ 
		$user = get_user_by( 'id',  $term->user_id );
		$user_name = filter_var($user->user_url, FILTER_VALIDATE_URL) ? '<a href="'.$user->user_url.'" target="_blank" rel="external">'.$user->display_name.'</a>' : $user->display_name;
		?><li><span class="pull-right"><?php echo $term->meta_value;?></span><a href="<?php echo get_author_posts_url( $term->user_id ); ?>"><?php echo dmeng_get_avatar( $term->user_id , 20 , dmeng_get_avatar_type($term->user_id) ); ?></a> <?php echo $user_name;?></li>
	<?php } ?>
</ul>
<?php

		echo $after_widget;
		
		$cache = apply_filters( 'dmeng_credit_rank', ob_get_flush(), $args, 'cr' );
			}
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = $instance['title'];
?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题：','dmeng');?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => ''));
		$instance['title'] = strip_tags($new_instance['title']);
		
		do_action( 'dmeng_update_credit_rank', $instance, 'cr' );
		
		return $instance;
	}
	
}

class DmengRankWidget extends WP_Widget {

	function __construct() {
		parent::__construct( 'dmengRank', __( ' 排行榜' , 'dmeng' ) , array('classname' => 'widget_dmeng_rank', 'description' => __( '热门文章/热议文章/搜索排行榜' , 'dmeng' ) ) );
	}

	function widget( $args, $instance ) {
		
		extract($args);
		
		$output = apply_filters( 'dmeng_pre_rank', null, $args, 'r' );
		
		if ( null !== $output ) {
			echo $output;
			return;
		}
		
		ob_start();

		$number = (int)$instance['number'];
		if(!$number) return;
		
		$items = array();

//~ 数据转HTML函数
function panel_group_item_output_html($style,$parent,$id,$collapsed,$glyphicon,$data,$title){
	$output = '<div class="panel panel-'.$style.'">';
	$output .= '<div class="panel-heading"><h3 class="panel-title"><a data-toggle="collapse" data-parent="#'.$parent.'" href="#'.$id.'"><span class="glyphicon '.$glyphicon.'"></span> '.$title.'</a></h3></div>';
	$output .= ' <div id="'.$id.'" class="panel-collapse collapse '.$collapsed.'">';
	foreach( $data as $item ){
		$output .= '<li class="list-group-item"><span class="badge" title="'.$item['badge_title'].'">'.$item['badge'].'</span> <a href="'.$item['url'].'" title="'.$item['title'].'">'.$item['title'].'</a></li>';
	}
	$output .= '</div></div>';
	return $output;
}

function get_panel_item_data($type,$number){
	
	$data = array();
	
	if( $type=='search' ){
		$search_rank = dmeng_tracker_rank('search',$number);
		if($search_rank){
			foreach( $search_rank as $search ){
				$data[] = array(
					'url' => add_query_arg('s',$search->pid,home_url()),
					'title' => strip_tags($search->pid),
					'badge' => $search->traffic,
					'badge_title' => sprintf(__('%s次搜索','dmeng'),$search->traffic)
				);
			}
			return array(
				'style' => 'success',
				'id' => 'search',
				'icon' => 'glyphicon-search',
				'data' => $data,
				'title' => __( '搜索次数最多的%s个关键词','dmeng')
			);
		}
	}
	
	if( $type=='comment' ){
		$query = new WP_Query( array( 'posts_per_page' => $number, 'orderby' => 'comment_count', 'ignore_sticky_posts' => true, 'post_type' => array( 'post', 'page' ) ) );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$data[] = array(
					'url' => get_permalink(),
					'title' => get_the_title(),
					'badge' => get_comments_number(),
					'badge_title' => sprintf(__('%s条评论','dmeng'),get_comments_number())
				);
			}
			wp_reset_postdata();
			return array(
				'style' => 'info',
				'id' => 'comment',
				'icon' => 'glyphicon-volume-up',
				'data' => $data,
				'title' => __( '最多人评论的%s篇内容','dmeng')
			);
		}
	}
	
	if( $type=='vote' ){
		global $wpdb;
		$vote_rank = $wpdb->get_results("SELECT post_id,sum(meta_value+0) AS count FROM $wpdb->postmeta WHERE meta_key='dmeng_votes_up' OR meta_key='dmeng_votes_down' GROUP BY post_id ORDER BY count DESC LIMIT $number");
		if($vote_rank){
			foreach( $vote_rank as $vote ){
				$data[] = array(
					'url' => get_permalink($vote->post_id),
					'title' => get_the_title($vote->post_id),
					'badge' =>  $vote->count,
					'badge_title' => sprintf(__('%s人投票','dmeng'),$vote->count)
				);
			}
			return array(
				'style' => 'warning',
				'id' => 'vote',
				'icon' => 'glyphicon-stats',
				'data' => $data,
				'title' => __( '按投票率排行的%s篇内容','dmeng')
			);
		}
	}
	
	if( $type=='view' ){
		$view_rank = dmeng_tracker_rank('single',$number);
		if($view_rank){
			foreach( $view_rank as $view ){
				$data[] = array(
					'url' => get_permalink($view->pid),
					'title' => get_the_title($view->pid),
					'badge' => $view->traffic,
					'badge_title' => sprintf(__('%s次浏览','dmeng'),$view->traffic)
				);
			}
			return array(
				'style' => 'danger',
				'id' => 'view',
				'icon' => 'glyphicon-fire',
				'data' => $data,
				'title' => __( '按浏览次数排行的%s篇内容','dmeng')
			);
		}
	}
	
}

	foreach( $instance as $key=>$value ){
		if( isset($instance[$key]) && trim($instance[$key])=='on' ){
			$output = get_panel_item_data($key, $number);
			if($output) $items[] = $output;
		}
	}

	$count = count($items);
	
	if($count){
		
		echo '<aside id="accordion-'.$args['widget_id'].'" class="panel-group">';
		
		$i = 1;
		foreach( $items as $item ){
			
			$in = $i==$count ? 'in' : '';

			echo panel_group_item_output_html($item['style'], 'accordion-'.$args['widget_id'], $item['id'].'-'.$args['widget_id'], $in, $item['icon'], $item['data'], sprintf( $item['title'] , $number ));
			
			$i++;
		}
		
		echo '</aside>';
	}

		$cache = apply_filters( 'dmeng_rank', ob_get_flush(), $args, 'r' );
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array() );

		$list = array('search', 'comment', 'vote', 'view','number');
		foreach( $list as $key ){
			$instance[$key] = strip_tags($new_instance[$key]);
		}

		do_action( 'dmeng_update_rank', $instance, 'r' );
			
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'search' => 'on', 'comment' => 'on', 'vote' => 'on', 'view' => 'on', 'number' => 10 ) );
		$number = $instance['number'];
?>
<p><?php _e('选择要显示的项目。','dmeng');?></p>
<p>
	<?php
	
	$list = array(
		'search' => __('搜索次数最多的关键词','dmeng'),
		'comment' => __('评论最多的内容','dmeng'),
		'vote' => __('最多人投票的内容','dmeng'),
		'view' => __('浏览次数最多的内容','dmeng'),
	);
	
	foreach( $list as $key=>$value ){
	?>
	<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id($key); ?>" name="<?php echo $this->get_field_name($key); ?>" <?php if(isset($instance[$key])&&trim($instance[$key])=='on') echo 'checked="checked"';?>> <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $value?></label><br>
	<?php
	}
	?>
</p>
	<p>
		<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('显示数量：','dmeng');?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" size="3">
	</p>
<?php
	}
	
}

class DmengRecentUserWidget extends WP_Widget {

	function __construct() {
		parent::__construct( 'recent_user', __( ' 最近登录用户' , 'dmeng' ) , array('classname' => 'widget_recent_user', 'description' => __( '显示最近登录用户头像' , 'dmeng' ) ) );
	}

	function widget( $args, $instance ) {

		extract($args);
		
		$output = apply_filters( 'dmeng_pre_recent_user', null, $args, 'ru' );
		
		if ( null !== $output ) {
			echo $output;
			return;
		}
		
		ob_start();

		$number = (int)$instance['number'];
		if(!$number) return;
		
		$users = dmeng_get_recent_user($number);

		if($users){

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

?>
<ul id="recent_user">
	<?php foreach( $users as $user ){
			$user_url = get_author_posts_url( $user->ID );
			echo '<li><a href="'.$user_url .'" target="_blank" title="'.$user->display_name.'">'.dmeng_get_avatar( $user->ID , '55' , dmeng_get_avatar_type($user->ID) ).'</a></li>';
		}?>
</ul>
<?php

		echo $after_widget;
		
		 $cache = apply_filters( 'dmeng_recent_user', ob_get_flush(), $args, 'ru' );

		}
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '最近登录用户', 'number' => 10) );
		$title = $instance['title'];
		$number = (int)$instance['number'];
?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题：','dmeng');?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('显示数量：','dmeng');?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" size="3">
	</p>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => '最近登录用户', 'number' => 10));
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int)$new_instance['number'];
		
		do_action( 'dmeng_update_recent_user', $instance, 'ru' );
			
		return $instance;
	}
	
}

function dmeng_register_widgets() {
	register_widget( 'DmengAnalyticsWidget' );
	register_widget( 'DmengOpenWidget' );
	register_widget( 'DmengCreditRankWidget' );
	register_widget( 'DmengRankWidget' );
	register_widget( 'DmengRecentUserWidget' );
}

add_action( 'widgets_init', 'dmeng_register_widgets' );
