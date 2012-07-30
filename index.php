<?php

require_once ('lib.php');

$agents = array(
	'Windows CE', 'Pocket', 'Mobile',
	'Portable', 'Smartphone', 'SDA',
	'PDA', 'Handheld', 'Symbian',
	'WAP', 'Palm', 'Avantgo',
	'cHTML', 'BlackBerry', 'Opera Mini',
	'Nokia', 'PSP', 'J2ME'
);

$mobile = false;
while(list($agent) = each($agents) && isset($_SERVER["HTTP_USER_AGENT"]) && !$mobile) if (strpos($_SERVER["HTTP_USER_AGENT"], $agent)) $mobile = true;

$frameset = array(
	'title'		=>	$title,
	'http_charset'	=>	$http_charset,
	'theme'		=>	$theme,
	'css'		=>	$css,
	'mobile'	=>	$mobile
);

$html = new HTML;
$html -> parse_html_template( 'frameset', $frameset );

?>
