<?php
    @( include('config.php')) or die('<h2>Fehler: config.php ist nicht vorhanden!</h2>Bitte mit <em>cp config.php_template config.php</em> anlegen');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"                                  "http://www.w3.org/TR/html4/frameset.dtd">

<html>
  <head>
      <title><?php print $title ?></title>
      <link rel="icon" href="img/partdb/favicon.ico" type="image/x-icon">
  </head>

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

if ( check_mobile()) 
{
    @( include('mobil/mobil.php')) or die('<h2>Part-DB Mobile ist nicht installiert!</h2>');
}
else { 
?>
    <frameset cols="300,*" frameborder="0" framespacing="0" border="0">
        <frame name="_nav_frame" src="nav.php">
        <frame name="content_frame" src="startup.php">
    </frameset>
<?php
}
?>
</html> 

