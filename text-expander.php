<?php
/*
Plugin Name: Text Expander
Plugin URI: http://www.ali.dj/wordpress-plugin-text-expander/
Description: Allows you to define areas of text that expand/collapse when clicked. It is built on Dagon Design's Expanding Text-Plugin, but comes with some improvements.
Author: Alexander Zigelski
Version: 0.1
Author URI: http://www.ali.dj
*/

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
    wp_enqueue_script('jquery');
	echo '

<script language="JavaScript" type="text/javascript"><!--
function expand(param) {
jQuery(function ($) {
 $("div"+param).slideToggle("slow", function() {
    var linkcontent = $("a"+param).html().substring(1);
    if( $("div"+param).is(":visible") ) {
      $("a"+param).html("&uArr;"+linkcontent);
    }
    else {
        $("a"+param).html("&dArr;"+linkcontent);
    }
  });
});
}
function expander_hide(param) {
    jQuery(function ($) {
        $("div"+param).hide();
        var linkcontent = $("a"+param).html();
        $("a"+param).html("&dArr; " + linkcontent);
        $("a"+param).show();
    });
}
//--></script>
';
}


add_action('wp_head', 'expander_javascript');
add_filter('the_content', 'expander_process');

?>