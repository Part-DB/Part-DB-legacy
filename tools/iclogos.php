<?php

require_once ('../lib.php');



$tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
$tmpl -> setVar('head_title', 'Hersteller IC Logos');
$tmpl -> setVar('head_charset', $http_charset);
$tmpl -> setVar('head_css', "../".$css);
$tmpl -> setVar('head_menu', true);
$tmpl -> pparse();

$tmpl = new vlibTemplate(BASE."/templates/$theme/iclogos.php/vlib_iclogos.tmpl");
$tmpl -> pparse();

$tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_foot.tmpl");
$tmpl -> pparse();

?>

