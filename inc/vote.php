<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 投票 @author 多梦 at 2014.07.04
 * 
 */
 
/*
 * 
 * 获取投票数
 * 
 */

function get_dmeng_user_vote( $uid, $count=true, $type='', $vote='', $limit=0, $offset=0 ){

	$uid = intval($uid);
	
	if( !$uid ) return;
	
	$type = in_array($type, array('post', 'comment')) ? $type : '';
	$vote = in_array($vote, array('up', 'down')) ? $vote : '';
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'dmeng_meta';

	$where = "WHERE user_id='$uid' ";
	
	if($type) {
		$vote_type = 'vote_'.$type.'_%';
		$where .= "AND meta_key LIKE '$vote_type' ";
	}
	
	if($vote) $where .= "AND meta_value LIKE '$vote' ";

	if($count){
		$check = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name $where" );
	}else{
		$field = $vote ? 'meta_key' : 'meta_key,meta_value';
		$check = $wpdb->get_results( "SELECT $field FROM $table_name $where ORDER BY meta_id DESC LIMIT $offset,$limit" );
	}
	
	if($check)	return $check;
	
	return 0;
}
 
/*
 * 
 * 投票HTML
 * 
 */
 
function dmeng_vote_html($type,$id){
	
	$type = $type=='post' ? 'post' : 'comment';

	$vote_class = 'btn-group vote-group';
	$up_class = 'btn btn-default up';
	$down_class = 'btn btn-default down';
	$up_html = '<span class="glyphicon glyphicon-thumbs-up"></span>';
	$down_html = '<span class="glyphicon glyphicon-thumbs-down"></span>';
	$type_html = 'data-vote-type="post" itemscope itemtype="http://data-vocabulary.org/Review-aggregate"';
		
	if($type=='comment'){
		$vote_class = 'comment-votes vote-group';
		$up_class = 'up';
		$down_class = 'down';
		$up_html = '<span class="glyphicon glyphicon-chevron-up"></span>';
		$down_html = '<span class="glyphicon glyphicon-chevron-down"></span>';
		$type_html = '';
	}

	$key = 'vote_'.$type.'_'.$id;
	
	$uid = get_current_user_id();
	
	if($uid>0){
	
		$vote = get_dmeng_meta($key,$uid);
		
		if($vote){
			$vote_class .= ' disabled';
			if($vote=='up') $up_class .= ' highlight';
			elseif($vote=='down') $down_class .= ' highlight';
		}
	
	}
	
	if($type==='post'||$type==='comment'){
		$votes_up = (int)get_metadata($type, $id, 'dmeng_votes_up', true);
		$votes_down = (int)get_metadata($type, $id, 'dmeng_votes_down', true);
	}

	$votes = $votes_up-$votes_down;

?><div class="<?php echo $vote_class;?>" data-votes-up="<?php echo $votes_up;?>" data-votes-down="<?php echo $votes_down;?>" data-vote-id="<?php echo $id; ?>" <?php echo $type_html; ?>>
<a href="javascript:;" class="<?php echo $up_class;?>"><?php echo $up_html;?> <span class="votes"><?php echo $votes;?></span><?php

if($type=='post'){
	
	$count = $votes_up+$votes_down;
	
	//~ 投票计算得分，最低1分，最高10分，四舍五入留一个小数点（用于微数据 for microdata）
	$rating = ($votes_up+$votes_down)>0 ? round($votes_up/($votes_up+$votes_down)*5, 1) : 0;
	if($rating<1) $rating = 1;
	
	echo '<div class="hide" itemprop="rating" itemscope itemtype="http://data-vocabulary.org/Rating"><span itemprop="average">'.$rating.'</span><span itemprop="votes">'.$count.'</span><span itemprop="count">'.get_comments_number().'</span></div>';
	
}

?></a>
<a href="javascript:;" class="<?php echo $down_class;?>"><?php echo $down_html;?></a>
</div><?php
}
 
/*
 * 
 * 投票AJAX
 * 
 */
 
function dmeng_vote_ajax_callback(){

	if ( ! wp_verify_nonce( trim($_POST['wp_nonce']), 'check-nonce' ) ){
		echo 'NonceIsInvalid';
		die();
	}
	
	if ( !isset($_POST['type']) || !isset($_POST['id']) || !isset($_POST['vote']) ) return;
	
	$type = sanitize_text_field($_POST['type']);
	$id = intval($_POST['id']);
	$vote = sanitize_text_field($_POST['vote']);

	$key = 'vote_'.$type.'_'.$id;
	$uid = get_current_user_id();
	
	if($uid===0){
	
		add_dmeng_meta($key,$vote,$uid);

	}else{
		
		update_dmeng_meta($key,$vote,$uid);
		
	}
	
	//~ 为了便于列表应用和排序，up和down各保存一个总数到wordpress的原有meta表(postmeta/commentmeta)
	if($type==='post'||$type==='comment'){
		if($vote=='up') update_metadata($type, $id, 'dmeng_votes_up', (int)get_dmeng_meta_count($key,'up'));
		if($vote=='down') update_metadata($type, $id, 'dmeng_votes_down', (int)get_dmeng_meta_count($key,'down'));
	}
	
	echo 'ok';

	die();
}
add_action( 'wp_ajax_dmeng_vote_ajax', 'dmeng_vote_ajax_callback' );
add_action( 'wp_ajax_nopriv_dmeng_vote_ajax', 'dmeng_vote_ajax_callback' );
