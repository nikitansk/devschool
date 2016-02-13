<?php 

$plugin_url = $_GET['url'];
if (!filter_var($plugin_url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
	$plugin_url = '';
}
	
$mp4 = $_GET['mp4'];
if (!filter_var($mp4, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
	$mp4 = '';
}

$autoplay = $_GET['autoplay'];
if ($autoplay != '1' || $autoplay != '0' || $autoplay != 1 || $autoplay != 0) {
	$autoplay = '0';
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title></title>
<meta name="google" value="notranslate" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css" media="screen">
html, body {
	height: 100%;
	width: 100%;
}
body {
	margin: 0;
	padding: 0;
	overflow: auto;
	text-align: center;
	background-color: #ffffff;
}
object:focus {
	outline: none;
}
#flashContent {
	display: none;
}
</style>
<script type="text/javascript" src="<?php echo $plugin_url ?>/fallback/swfobject.js"></script>
<script type="text/javascript">
	var swfVersionStr = "11.1.0";
	var xiSwfUrlStr = "<?php echo $plugin_url ?>/fallback/playerProductInstall.swf";
	var flashvars = {'src':'<?php echo $mp4; ?>', 'autoplay':'<?php echo $autoplay; ?>'};
	var params = {};
	params.quality = "high";
	params.bgcolor = "#ffffff";
	params.allowscriptaccess = "always";
	params.allowfullscreen = "true";
	var attributes = {};
	attributes.id = "sh5fp";
	attributes.name = "sh5fp";
	attributes.align = "middle";
	swfobject.embedSWF(
		"<?php echo $plugin_url ?>/fallback/sh5fp.swf", "flashContent", 
		"100%", "100%", 
		swfVersionStr, xiSwfUrlStr, 
		flashvars, params, attributes);
	swfobject.createCSS("#flashContent", "display:block;text-align:left;");
</script>
</head>
<body>
<div id="flashContent">
	<p>To view this page ensure that Adobe Flash Player version 11.1.0 or greater is installed.</p>
	<script type="text/javascript"> 
	var pageHost = ((document.location.protocol == "https:") ? "https://" : "http://"); 
	document.write(
		"<a href='http://www.adobe.com/go/getflashplayer'><img src='" 
		+ pageHost + "www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a>"
	); 
	</script> 
</div>

<noscript>
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%" id="sh5fp">
	<param name="movie" value="<?php echo $plugin_url ?>/fallback/sh5fp.swf" />
	<param name="quality" value="high" />
	<param name="bgcolor" value="#ffffff" />
	<param name="allowScriptAccess" value="always" />
	<param name="allowFullScreen" value="true" />
	<param name="wmode" value="transparent" />
	<param name="flashvars" value="src=<?php echo urlencode($mp4); ?>&autoplay=<?php echo urlencode($autoplay); ?>" />
	<!--[if !IE]>-->
	<object type="application/x-shockwave-flash" data="<?php echo $plugin_url ?>/fallback/sh5fp.swf" width="100%" height="100%">
	<param name="quality" value="high" />
	<param name="bgcolor" value="#ffffff" />
	<param name="allowScriptAccess" value="always" />
	<param name="allowFullScreen" value="true" />
	<param name="wmode" value="transparent" />
	<param name="flashvars" value="src=<?php echo urlencode($mp4); ?>&autoplay=<?php echo urlencode($autoplay); ?>" />
	<!--<![endif]-->
	<!--[if gte IE 6]>-->
	<p>Either scripts and active content are not permitted to run or Adobe Flash Player version 11.1.0 or greater is not installed. </p>
	<!--<![endif]--> 
	<a href="http://www.adobe.com/go/getflashplayer"> <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash Player" /> </a> 
	<!--[if !IE]>-->
	</object>
	<!--<![endif]-->
</object>
</noscript>     
</body>
</html>
