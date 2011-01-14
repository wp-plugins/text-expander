<?php
/*
Plugin Name: Text Expander
Plugin URI: http://www.ali.dj/wordpress-plugin-text-expander/
Description: Allows you to define areas of text that expand/collapse when clicked. It is an improved version of Dagon Design's Expanding Text-Plugin.
Author: Alexander Zigelski
Version: 0.2
Author URI: http://www.ali.dj
*/

// add the options page
add_action('admin_menu', 'text_expander_admin_add_page');
add_action('wp_head', 'expander_javascript');
add_filter('the_content', 'expander_process');
$wp_text_expander_url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

function text_expander_admin_add_page() {
add_options_page('Text Expander Settings', 'Text Expander', 'manage_options', 'text_expander', 'text_expander_options_page');
}

// display the options page
function text_expander_options_page() {
?>
<div>
<h2>Text Expander Settings</h2>
Options relating to the Custom Plugin.
<form action="options.php" method="post">
<?php settings_fields('text_expander_options'); ?>
<?php do_settings_sections('text_expander'); ?>
<br />
<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form></div>

<?php
}
add_action('admin_init', 'plugin_admin_init');

function plugin_admin_init(){
register_setting( 'text_expander_options', 'text_expander_options', 'expand_text_validate' );
add_settings_section('text_expander_main', 'Expand/Collapse-link', 'text_expander_main_section_text', 'text_expander');
add_settings_field('expand_text', 'Before collapse link:', 'text_expander_expand_text_string', 'text_expander', 'text_expander_main');
add_settings_field('collapse_text', 'Before expand link:', 'text_expander_collapse_text_string', 'text_expander', 'text_expander_main');
}

register_activation_hook(__FILE__, 'add_defaults_fn');

// Define default option settings
function add_defaults_fn() {
	$tmp = get_option('text_expander_options');
    if(($tmp['chkbox1']=='on')||(!is_array($tmp))) {
		$arr = array("expand_text"=>"&uArr;", "collapse_text" => "&dArr;");
		update_option('text_expander_options', $arr);
	}
}

function text_expander_main_section_text() {
echo '<p>Here you can adjust which character or string is shown before the expand or collapse link.<br />
		Default values are a down-and up-arrow</p>';
} 

function text_expander_expand_text_string() {
$options = get_option('text_expander_options');
echo "<input id='expand_text_string' name='text_expander_options[expand_text]' size='40' type='text' value='{$options['expand_text']}' />";
} 

function text_expander_collapse_text_string() {
$options = get_option('text_expander_options');
echo "<input id='collapse_text_string' name='text_expander_options[collapse_text]' size='40' type='text' value='{$options['collapse_text']}' />";
} 

// validate our options
function expand_text_validate($input) {
$options = get_option('text_expander_options');
$options['expand_text'] = trim($input['expand_text']);
$options['collapse_text'] = trim($input['collapse_text']);
return $options;
}


function expander_str_replace_once($needle , $replace , $haystack){
    // Looks for the first occurence of $needle in $haystack
    // and replaces it with $replace.
    $pos = strpos($haystack, $needle);
    if ($pos === false) {
        // Nothing found
    return $haystack;
    }
    return substr_replace($haystack, $replace, $pos, strlen($needle));
}

function wp_text_expander_add_media_button() {
	GLOBAL $wp_text_expander_url;
	$url = $wp_text_expander_url.'/media-button-expander.php?tab=add&TB_iframe=true&amp;height=300&amp;width=640';
	if (is_ssl()) $url = preg_replace( '/^http:\/\//', 'https://',  $url );
	echo '<a href="'.$url.'" class="thickbox" title="'.'Add Text Expander'.'"><img src="'.$wp_text_expander_url.'/img/media-button-text-expander.gif" alt="'.'Add Text Expander'.'"></a>';
}

add_action('media_buttons', 'wp_text_expander_add_media_button', 20);

function expander_process($content) {

	$offset = 0;
	$stag = '[EXPAND ';
	$etag = '[/EXPAND]';
	while (stripos($content, $stag, $offset)) {

		// string to replace
		$s = stripos($content, $stag, $offset);
		$e = stripos($content, $etag, $s) + strlen($etag);

		// inside data
		$ds = stripos($content, ']', $s) + 1;
		$de = $e - strlen($etag);

		// style tag
		$ss = $s + strlen($stag);
		$se = $ds - 1;

		$sstring = substr($content, $s, $e - $s);
		$sdesc = substr($content, $ss, $se - $ss);
		$sdata = substr($content, $ds, $de - $ds);

		mt_srand((double)microtime()*1000000);
		$rnum = mt_rand();

		$new_string = '<a style="display:none;" id="te' . $rnum;
		$new_string .= '" href="javascript:expand(\'#te' . $rnum . '\')">';
		$new_string .= $sdesc . '</a>' . "\n";
		$new_string .= '<div class="te_div" id="te' . $rnum . '">';
		$new_string .= '<script language="JavaScript" type="text/javascript">expander_hide(\'#te' . $rnum . '\');</script>';
		
		$sdata = preg_replace('`^<br />`sim', '', $sdata);
		$content = expander_str_replace_once($sstring, $new_string . $sdata . '</div>', $content);
		$offset = $s + 1;
	}
	return $content;
}

function expander_javascript() {
	$options = get_option('text_expander_options');	
	
    wp_enqueue_script('jquery');
	echo '

<script language="JavaScript" type="text/javascript"><!--
function expand(param) {
jQuery(function ($) {
 $("div"+param).stop().slideToggle("slow", function() {
    if( $("div"+param).is(":visible") ) {
      $("a"+param).html("'.$options['expand_text'].' "+linkname);
    }
    else {
        $("a"+param).html("'.$options['collapse_text'].' "+linkname);	
    }
  });
});
}
function expander_hide(param) {
    jQuery(function ($) {
        $("div"+param).hide();
		linkname = $("a"+param).html();
        $("a"+param).html("'.$options['collapse_text'].' " + linkname);
        $("a"+param).show();
    });
}
//--></script>
';
}

?>