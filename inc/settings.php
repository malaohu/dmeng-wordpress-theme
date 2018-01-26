<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */

/*
 * 主题设置页面 @author 多梦 at 2014.06.23 
 * 
 */

add_action( 'admin_menu', 'dmeng_admin_menu_page' );
function dmeng_admin_menu_page(){

	$title = __('多梦主题设置','dmeng');
	$slug = 'dmeng_options_general';

	add_menu_page( $title, $title, 'manage_options', $slug, 'dmeng_options_page', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAACRklEQVR42qXUS0hUURzH8fFBQj5CQsjAJAofSGEmgmKRWqQlhJpQmNKDiozIfBBFQrZQ2qSWoQsRo0VRuhFxEe1aWlFhRGEiVii9KFpI1szt+4ffhatMw5QXPsw9Z8753/P4n+PzhfE4jhOHBN9yHgJE4SgGcRENuI4ubPyXQLGoxUHsxnG0oR0tqEQp6lAQztQ6kIJO/HaCP3MoRBV2ICJYsHgF246XCDihn4dIVvuyYAFrkIS7gUDgKV7x/hWPeX+BCaun/B7zyPP0PbRo0yissDXT+0pbGzo/wIzWL0H15xX0F4o9/VPR6g24S0PPVzkPb+HHNLJUX646e8YQrfrDaEaiFWKUEq04qw3JxyVcwQmUuB9nhB/xg7pvyFFAS601SHcb3aFwC40Y10gW0KMR7UMRruEcBuhzn99epdkN3MROd8pD6lxOw0nbNVzm/R0+8b4HXxTAPvoGTUqfbNqM6APpFiyRwmut2XNU4JQ2o0pTs84XtJ7V+KAN+mzJz++s/tvvjrAYW+xLapyjo3cMt20N8cTSSu3PKPG34TSuatqb3IBr7UgR8JkSut+z21MYVn2u6rNxUvV+rXn3ohOjrc+0NbMk1jG0UTbqQ44luHXSf1spf9cMbJnSlp6UTNTbLlpq8Juh+g34qdzze+qbdfysX+3fLoc0HaMC3FNd95JzbVdZpKaZpXJMqBsnF0cU1Kbcx4gfKT9HdbXF6xo7YMc2nDsxQutiibzX8gvr9bEG7ejq/7m1o7EOmy2tFHhVqD5/AMwI1hzsuzg9AAAAAElFTkSuQmCC' ); 

	$dmeng_submenus = array(
		'slide' =>__('幻灯片','dmeng'),
		'tool' =>__('高级','dmeng')
	);
	foreach( $dmeng_submenus as $skey=>$stitle ){
		add_submenu_page( $slug, $stitle, $stitle, 'manage_options', 'dmeng_options_'.$skey , 'dmeng_options_'.$skey.'_page' ); 
	}

}

function dmeng_admin_tabs($tab='general'){
	$dmeng_tabs = array(
		'general' => __('常规设置', 'dmeng'),
		'home' => __('首页', 'dmeng'),
		'writing' =>__('撰写','dmeng'),
		'reading' =>__('阅读','dmeng'),
		'discussion' =>__('讨论','dmeng'),
		'open' =>__('开放平台','dmeng'),
		'credit' =>__('积分','dmeng'),
		'gift' =>__('积分换礼','dmeng'),
		'smtp' =>__('SMTP发信','dmeng'),
	);

	$tab_output = '<h2 class="nav-tab-wrapper">';
	foreach( $dmeng_tabs as $tab_key=>$tab_name ){
		$tab_output .= sprintf('<a href="%s" class="nav-tab%s">%s</a>', add_query_arg('tab', $tab_key), $tab_key==$tab ? ' nav-tab-active' : '', $tab_name);
	}
	$tab_output .= '</h2>';
	echo $tab_output;
}

$dmeng_tabs_array = array(
	'general',
	'home',
	'writing',
	'reading',
	'discussion',
	'open',
	'credit',
	'gift',
	'smtp'
);

//~ 载入设置页面
require_once( get_template_directory() . '/inc/settings-slide.php' );
require_once( get_template_directory() . '/inc/settings-tool.php' );

foreach( $dmeng_tabs_array as $dmeng_tab_slug ){
	require_once( get_template_directory() . '/inc/settings-'.$dmeng_tab_slug.'.php' );
}

function dmeng_options_page(){
	global $dmeng_tabs_array;
	$tab = 'general';
	if( isset($_GET['tab']) ){
		$tab = in_array($_GET['tab'], $dmeng_tabs_array) ? $_GET['tab'] : 'general';
	}
	$tab = 'dmeng_options_'.$tab.'_page';
	$tab();
}

class DmengOptionsOutput {
	
	public function table($items){

		if( empty($items[0]['type']) ) return;
		
		echo '<table class="form-table"><tbody>';
		
		foreach( $items as $item){
			
			$item = wp_parse_args( $item, array(
								'type' => '',
								'th' => '',
								'before' => '',
								'after' => '',
								'key' => '',
								'value' => ''
							));
			
			echo '<tr>';
			$this->tableTH($item['key'], $item['th']);
			$this->tableTD($item['type'], $item['key'], $item['value'], $item['before'], $item['after']);
			echo '</tr>';
		}
		
		echo '</tbody></table>';
	}
	
	public function tableTH($key, $title){
		echo sprintf('<th scope="row"><label for="%s">%s</label></th>', $key, $title);
	}
	
	public function tableTD($type, $key, $value, $before, $after){
		
		echo '<td>'.$before;

		if( $type=='input' ){
			echo sprintf('<input name="%1$s" type="text" id="%1$s" value="%2$s" class="regular-text ltr">', $key, $value);
		}

		if( $type=='input-password' ){
			echo sprintf('<input name="%1$s" type="password" id="%1$s" value="%2$s" class="regular-text ltr">', $key, $value);
		}

		if( $type=='textarea' ){
			echo sprintf('<textarea name="%1$s" rows="5" cols="50" id="%1$s" class="large-text code">%2$s</textarea>', $key, $value);
		}
		
		if( $type=='select' ){
			echo sprintf('<select name="%1$s" id="%1$s">', $key);
			foreach( $value['option'] as $option_key=>$option_value ){
				echo sprintf('<option value="%1$s"%2$s>%3$s</option>', $option_key, (in_array($option_key, $value['default']) ? ' selected="selected"' : ''), $option_value);
			}
			echo '</select>';
		}
		
		if( $type=='checkbox' ){
			foreach( $value['option'] as $option_key=>$option_value ){
				echo sprintf('<label><input name="%1$s[]" type="checkbox" value="%2$s"%3$s> %4$s </label>', $key, $option_key, (in_array($option_key, $value['default']) ? ' checked' : ''), $option_value);
			}
		}
		
		if( $type=='editor' ){
			wp_editor( $value, $key, array( 'media_buttons' => false, 'textarea_rows' => 5 ) );
		}

		echo $after.'</td>';
	}

}
