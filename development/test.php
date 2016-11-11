<?php

$language = "en_US";
$d1 = putenv("LANG=$language");
$d2 = setlocale(LC_ALL, $language);

// Set the text domain as 'messages'
$domain = 'test';
$d3 = bindtextdomain($domain, "../locale");
$d4 = textdomain($domain);

echo gettext("Statistik");

?>