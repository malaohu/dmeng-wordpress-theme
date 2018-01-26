<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */
 
 /*
  * 主要导航菜单本来是用在 Github 的一个菜单类 wp_bootstrap_navwalker
  * 但是这个walker有诸多不合理，比如说会把标题属性直接当做glyphicon图标输出，我改了，但是还有不满意，比如说二级以下的菜单处理等等。
  * 奈何强迫症～越看越不顺眼～
  * 重新写一个吧～
  * 
  * @author 多梦 at 2014.08.29
  */

//~ 头部导航菜单
class Dmeng_Bootstrap_Menu extends Walker {

	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );

	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		if( $depth == 0 ){
			$output .= '<ul class="dropdown-menu" role="menu">';
		}else{
			$output .= '<li class="divider"></li>';
		}
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		if( $depth == 0 ) $output .= "</ul>";
	}

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$caret = $glyphicon_icon = $item_output = '';
		
		if( $depth > 0 && $item->description ) $item_output .= '<li role="presentation" class="dropdown-header">'.$item->description.'</li><li class="divider"></li>';
		
		$atts = $atts_class = array();
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		 //~ 判断标题属性是否以 glyphicon 开头，是的话就当做 glyphicon 图标输出，添加在链接文本前
		 //~ @author 多梦 at 2014.06.20 
		if ( ! empty( $item->attr_title ) ) {
			if ( strpos( esc_attr( $item->attr_title ) , 'glyphicon' ) !== false && strpos( esc_attr( $item->attr_title ) , 'glyphicon' ) == 0 ){
				$glyphicon_icon = '<span class="glyphicon ' . esc_attr( $item->attr_title ) . '"></span> ';
			}else{
				$atts['title']  = $item->attr_title;
			}
		}
		
		$class_names = (array) get_post_meta( $item->ID, '_menu_item_classes', true );
		
		if ( $depth == 0 && ($args->depth)>=0 && in_array( 'menu-item-has-children', $item->classes ) ){
			$class_names[] = 'dropdown';
			$atts_class[] = 'dropdown-toggle';
			$atts['data-toggle'] = 'dropdown';
			$caret = ' <span class="caret"></span></a>';
		}
		
		if( empty( $item->url ) && empty($atts['data-toggle']) ) $atts_class[] = 'navbar-text';

		if( $item->current || $item->current_item_ancestor || $item->current_item_parent ){
			$class_names[] = 'active';
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $class_names ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$item_output .= '<li' . $class_names .'>';

		$atts['class'] = join('', $atts_class);

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output .= $args->before;
		$item_output .= ( $item->url || !empty($atts['data-toggle']) ) ? '<a'. $attributes .' itemprop="url">' : '<div'. $attributes .'>';
		$item_output .= $args->link_before . $glyphicon_icon . apply_filters( 'the_title', $item->title, $item->ID ) . $caret . $args->link_after;
		$item_output .= ( $item->url || !empty($atts['data-toggle']) ) ? '</a>' : '</div>';
		$item_output .= $args->after;

		$output .= $item_output;

	}

	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>";
	}

}
