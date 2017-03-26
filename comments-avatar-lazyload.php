<?php
/**
 * @package comments-avatar-lazyload
 */
/*
Plugin Name: Comments Avatar Lazyload
Plugin URI: https://www.litefeel.com/comments-avatar-lazyload/
Description: Comments Avatar Lazyload at server side load replace the src property of img tag. It is relly lazyload. It was successfully checked by W3C. 
Version: 2.6.3
Author: litefeel
Author URI: https://www.litefeel.com/
License: GPLv2 or later
Text Domain: comments-avatar-lazyload
*/

/* options */
/* ------------------------------------------------------------ */
class CommentsAvatarLazyloadOptions {

	public static function getDefalutOptions(){
		$options = array();
		$options['load_js_at_front_page']	= false;
		return $options;
	}
	
	public static function getOptions() {
		$options = get_option('commentsAvatarLazyload_options');
		$ver_change = false;
		if(!is_array($options)) {
			$options = array();
		}
		$default = CommentsAvatarLazyloadOptions::getDefalutOptions();
		foreach ( $default as $key => $value) {
			if(!isset($options[$key])) {
				$options[$key] = $value;
				$ver_change = true;
			}
		}
		if ($ver_change) {
			update_option('commentsAvatarLazyload_options', $options);
		}
		return $options;
	}
	
	public static function add() {
		if(isset($_POST['commentsAvatarLazylaod_save'])) {
			$options = CommentsAvatarLazyloadOptions::getOptions();
			
			if(!$_POST['load_js_at_front_page']) {
				$options['load_js_at_front_page'] = (bool)false;
			} else {
				$options['load_js_at_front_page'] = (bool)true;
			}
			update_option('commentsAvatarLazyload_options', $options);
		}else{
			CommentsAvatarLazyloadOptions::getOptions();
		}

		add_options_page(__('Comments Avatar Lazyload', 'comments-avatar-lazyload'), __('Comments Avatar Lazyload', 'comments-avatar-lazyload'), 'manage_options', __FILE__, array('CommentsAvatarLazyloadOptions', 'display'));
		add_filter( 'plugin_action_links', array('CommentsAvatarLazyloadOptions', 'plugin_action_links'), 10, 2 );
	}
	
	public static function plugin_action_links( $links, $file ) {
		if ( $file != plugin_basename( __FILE__ )) return $links;

		$settings_link = '<a href="options-general.php?page=comments-avatar-lazyload/comments-avatar-lazyload.php">' . __( 'Settings', 'comments-avatar-lazyload' ) . '</a>';

		array_push( $links, $settings_link );
		return $links;
	}

	public static function display() {
		$options = CommentsAvatarLazyloadOptions::getOptions();
?>

<div class="wrap">
	<div class="icon32" id="icon-options-general"><br /></div>
	<h2><?php _e('CommentsAvatarLazyload Options', 'comments-avatar-lazyload'); ?></h2>

	<div id="poststuff" class="has-right-sidebar">
		<div class="inner-sidebar">
			<div id="donate" class="postbox" style="border:2px solid #080;">
				<h3 class="hndle" style="color:#080;cursor:default;"><?php _e('Donation', 'comments-avatar-lazyload'); ?></h3>
				<div class="inside">
					<p><?php _e('If you like this plugin, please donate to support development and maintenance!', 'comments-avatar-lazyload'); ?>
					<br /><br /><strong><a href="https://me.alipay.com/lite3" target="_blank"><?php _e('Donate by alipay', 'comments-avatar-lazyload'); ?></a></strong><style>#donate form{display:none;}</style>
					</p>
				</div>
			</div>

			<div class="postbox">
				<h3 class="hndle" style="cursor:default;"><?php _e('About Author', 'comments-avatar-lazyload'); ?></h3>
				<div class="inside">
					<ul>
						<li><a href="http://www.litefeel.com/" target="_blank"><?php _e('Author Blog', 'comments-avatar-lazyload'); ?></a></li>
						<li><a href="http://www.litefeel.com/plugins/" target="_blank"><?php _e('More Plugins', 'comments-avatar-lazyload'); ?></a></li>
					</ul>
				</div>					
			</div>
		</div>

		<div id="post-body">
			<div id="post-body-content">

<form action="#" method="POST" enctype="multipart/form-data" name="commentsavatarlazyload_form">
		<div>
							<input name="load_js_at_front_page" type="checkbox" <?php if($options['load_js_at_front_page']) echo 'checked="checked"'; ?> />
							 <?php _e('Comments will be displayed on the home page. If your comments will not be displayed on the home page, do not choose this option.', 'comments-avatar-lazyload'); ?>
						</div>

		<p class="submit">
			<input class="button-primary" type="submit" name="commentsAvatarLazylaod_save" value="<?php _e('Save Changes', 'comments-avatar-lazyload'); ?>" />
		</p>
</form>
			</div>
		</div>

	</div>
</div>

<?php
	}
}

add_action('admin_menu', array('CommentsAvatarLazyloadOptions', 'add'));
/* l10n */
/* ------------------------------------------------------------ */
load_plugin_textdomain( 'comments-avatar-lazyload' );


add_action('template_redirect', 'call_load_static');
function call_load_static() {
	$options = get_option('commentsAvatarLazyload_options');
	if($options['load_js_at_front_page'] || !is_front_page()) {
		wp_enqueue_script('comments-avatar-lazyload-js',  plugins_url('js/lazyload.js', __FILE__), array('jquery'), '2.4.3', true);
	}
}

add_filter('get_avatar','call_get_avatar_lazyload',10,5);
function call_get_avatar_lazyload($avatar, $id_or_email, $size, $default, $alt) {
	if(is_admin()) return $avatar;
	if (isset($_GET['action'])) {
		if($_GET['action'] == 'rc-ajax') return $avatar;
		if($_GET['action'] == 'rc-content') return $avatar;
	}
	if ($alt !== false) return $avatar;
	
	$out = '';
	preg_match_all('/src=\'[^\']*\'|src="[^"]*"/',$avatar, $out);
	$tmp = $out[0][0];
	$avatar = str_replace($tmp, 'src="' . plugins_url('img/loading.gif', __FILE__). '"', $avatar);
	$tmp = str_replace('src' ,'alt', $tmp);
	$avatar = str_replace('alt=\'\'', $tmp, $avatar);
	$avatar = str_replace('alt=""', $tmp, $avatar);
	// for comments-avatar-lazyload class
	preg_match_all('/class=\'[^\']*\'|class="[^"]*"/',$avatar, $out);
	$tmp = $out[0][0];
	$now_class = substr($tmp,0,-1) . ' comments-avatar-lazyload' . substr($tmp,-1,1);
	$avatar = str_replace($tmp, $now_class, $avatar);
	return $avatar;
}
add_action('wp_footer', 'call_remove_get_avatar_lazyload', 1);
function call_remove_get_avatar_lazyload() {
	remove_filter('get_avatar','call_get_avatar_lazyload',10,5);
}
?>
