<?php
//////////// WPADMIN BOOTSTRAP LOADER
    if(file_exists('../../../wp-load.php')) {
    	require_once("../../../wp-load.php");
    } else if(file_exists('../../wp-load.php')) {
    	require_once("../../wp-load.php");
    } else if(file_exists('../wp-load.php')) {
    	require_once("../wp-load.php");
    } else if(file_exists('wp-load.php')) {
    	require_once("wp-load.php");
    } else if(file_exists('../../../../wp-load.php')) {
    	require_once("../../../../wp-load.php");
    } else if(file_exists('../../../../wp-load.php')) {
    	require_once("../../../../wp-load.php");
    } else {
    	if(file_exists('../../../wp-config.php')) {    		require_once("../../../wp-config.php");    	} else if(file_exists('../../wp-config.php')) {
    		require_once("../../wp-config.php");
    	} else if(file_exists('../wp-config.php')) {
    		require_once("../wp-config.php");
    	} else if(file_exists('wp-config.php')) {
    		require_once("wp-config.php");
    	} else if(file_exists('../../../../wp-config.php')) {
    		require_once("../../../../wp-config.php");
    	} else if(file_exists('../../../../wp-config.php')) {
    		require_once("../../../../wp-config.php");
    	} else {
    		echo '<p>Failed to load bootstrap.</p>';			exit;
    	}
    }
    require_once(ABSPATH.'wp-admin/admin.php');
//////////// END BOOTSTRAP LOADER
if (function_exists('admin_url')) {
	wp_admin_css_color('classic', __('Blue'), admin_url("css/colors-classic.css"), array('#073447', '#21759B', '#EAF3FA', '#BBD8E7'));
	wp_admin_css_color('fresh', __('Gray'), admin_url("css/colors-fresh.css"), array('#464646', '#6D6D6D', '#F1F1F1', '#DFDFDF'));
} else {
	wp_admin_css_color('classic', __('Blue'), get_bloginfo('wpurl').'/wp-admin/css/colors-classic.css', array('#073447', '#21759B', '#EAF3FA', '#BBD8E7'));
	wp_admin_css_color('fresh', __('Gray'), get_bloginfo('wpurl').'/wp-admin/css/colors-fresh.css', array('#464646', '#6D6D6D', '#F1F1F1', '#DFDFDF'));
}
wp_enqueue_script( 'common' );
wp_enqueue_script( 'jquery-color' );
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Text Expander'); ?> &#8212; <?php _e('WordPress'); ?></title>
	<?php
		wp_enqueue_style( 'global' );
		wp_enqueue_style( 'wp-admin' );
		wp_enqueue_style( 'colors' );
		wp_enqueue_style( 'media' );
	?>
	<script type="text/javascript">
	//<![CDATA[
		function addLoadEvent(func) {if ( typeof wpOnload!='function'){wpOnload=func;}else{ var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}}
	//]]>
	</script>
	<?php
	do_action('admin_print_styles');
	do_action('admin_print_scripts');
	do_action('admin_head');
	if ( isset($content_func) && is_string($content_func) )
		do_action( "admin_head_{$content_func}" );
	?>
</head>
<body id="media-upload">
<div style="padding:10px;" id="hs4wOptions">
<h3>Text Expander</h3>
<ul>
With a Text Expander you can insert special links to your content. If a user click this expander link the hidden text slides in. That expander can of course contain html-elements like images or tables.<br /><br />
<b>Example::</b><br /><br />
<b>[EXPAND <i>Click to expand!</i>]</b>Thats the expanded text<b>[/EXPAND]</b><br /><br />
</ul>
<h3>Insert Options</h3>
<ul>
<table>
  <form name="formular">
    <tr>
        <td><label for="e_Title">Title: </label></td>
        <td><input name="e_Title" type="text" size="32" value="" tabindex="1" /></td>
        <td><small><i>Title of your HTML Expander</i></small></td>
    </tr>
    <tr>
        <td><label for="e_Text">Text: </label></td>
        <td><textarea name="e_Text" cols="50" rows="10" tabindex="2" /></textarea></td>
        <td><small><i>Text you want to be expandable/collapsable</i></small></td>
    </tr>
  </form>
</table><br/>
<input type="submit" id="insertcode" class="button button-primary" name="insertintopost" value="Insert into post" />
</ul>
<script type="text/javascript">
/* <![CDATA[ */
    jQuery('#insertcode').click(function(){
        var win = window.dialogArguments || opener || parent || top;
        if( window.document.formular.e_Title.value || window.document.formular.e_Text.value ) {
            var out = '[EXPAND ';
            if( window.document.formular.e_Title.value )    out = out + window.document.formular.e_Title.value + ']';
            else                                            out = out + 'Click to expand!]';
            if( window.document.formular.e_Text.value )    out = out + window.document.formular.e_Text.value;
            else                                            out = out + 'Text';
            out = out + '[/EXPAND]'
        } else {
            var out = '[EXPAND Click to expand!]Thats the expanded text[/EXPAND]';
        }
        if (jQuery('#format').val()>0) win.send_to_editor(out + ' format="' + jQuery('#format').val() + '"]');
        else win.send_to_editor(out);
    });
/* ]]> */
</script>
</div>
</body>
</html>