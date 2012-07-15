<?php

function check_mobile()
{
	$agents = array(
		'Windows CE', 'Pocket', 'Mobile',
		'Portable', 'Smartphone', 'SDA',
		'PDA', 'Handheld', 'Symbian',
		'WAP', 'Palm', 'Avantgo',
		'cHTML', 'BlackBerry', 'Opera Mini',
		'Nokia', 'PSP', 'J2ME'
	);
	$result = false;

	if ( isset( $_SERVER["HTTP_USER_AGENT"]))
	{
		foreach( $agents as $agent)
		{
			if ( strpos( $_SERVER["HTTP_USER_AGENT"], $agent))
			{
				$result = true;
				break;
			}
		}
	}
	return $result;
}

require_once ('lib.php');

if ( check_mobile())
{
	require ('mobil/mobil.php');
}
else {
	$tmpl = new vlibTemplate(BASE."/templates/$theme/index.php/vlib_frameset.tmpl");
}

if ($tmpl)
{
	$tmpl -> setVar('head_title', $title);
	$tmpl -> pparse();
}
?>
