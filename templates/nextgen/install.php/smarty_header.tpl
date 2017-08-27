{locale path="nextgen/locale" domain="partdb"}

<!DOCTYPE html>
<html lang="{if isset($lang)}{$lang}{else}en{/if}">
<head>
    {if isset($http_charset)}<meta charset={$http_charset}>
    {else}<meta charset="utf-8">{/if}

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" sizes="180x180" href="{$relative_path}/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="{$relative_path}/icons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="{$relative_path}/icons/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="{$relative_path}/icons/manifest.json">
    <link rel="mask-icon" href="{$relative_path}/icons/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="{$relative_path}/icons/favicon.ico">
    <meta name="msapplication-config" content="{$relative_path}/icons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <title>{t}Part-DB Installation/Update{/t}</title>

    <link href="{$relative_path}css/bootstrap.min.css" rel="stylesheet">

    <!-- Include Awsome Font -->
    <link rel="stylesheet" href="{$relative_path}css/font-awesome.min.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Checkboxes -->
    <link href="{$relative_path}css/awesome-bootstrap-checkbox.css" rel="stylesheet">

    <!-- Fileinput -->
    <link href="{$relative_path}css/fileinput.min.css" media="all" rel="stylesheet"/>

    <!-- Include Part-DB Theme -->
    <link href="{$relative_path}templates/nextgen/nextgen.css" rel="stylesheet">

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="{$relative_path}js/jquery-3.2.1.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{$relative_path}js/bootstrap.min.js"></script>

    <!-- jQuery Form lib -->
    <script src="{$relative_path}js/jquery.form.min.js"></script>

    <!-- Functions -->
    <!-- <script src="{$relative_path}templates/nextgen/js/part-db.js"></script> -->

</head>
<body>

    <header>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <span class="navbar-brand"><i class="fa fa-microchip" aria-hidden="true"></i> Part-DB {$system_version} </span>

                </div>
                <div class="navbar-right">
                    <span class="navbar-brand">{t}Version:{/t} {$system_version_full}</span>
                </div>
            </div>
        </nav>
    </header>

<div class="container">

    {if isset($messages)}
        {assign "alert_style" "alert-info"}
        {foreach $messages as $msg}
            {if isset($msg.color) && $msg.color == "red"}
                {assign "alert_style" "alert-danger"}
            {elseif isset($msg.color) && ( $msg.color == "green" || $msg.color == "darkgreen")}
                {assign "alert_style" "alert-success"}
            {elseif isset($msg.color) && ($msg.color == "yellow" || $msg.color == "orange")}
                {assign "alert_style" "alert-warning"}
            {/if}
        {/foreach}
        <div class="alert {$alert_style}" id="messages">
            {if !empty($messages_div_title)}<h4>{$messages_div_title}</h4>{/if}
            <form action="" method="post" class="no-progbar">
                {foreach $messages as $msg}
                    {if isset($msg.text)}
                        {if isset($msg.strong) && $msg.strong}<strong>{/if}
                        {$msg.text nofilter}
                        {if isset($msg.strong) && $msg.strong}</strong>{/if}
                    {/if}

                    {if isset($msg.html)}
                        {$msg.html nofilter}
                    {/if}

                    {if !isset($msg.no_linebreak) || !$msg.no_linebreak}<br>{/if}
                {/foreach}

                {if !empty($reload_link)}
                    <a href="{$reload_link}">
                        <br>
                        <button class="btn btn-default">{t}Seite neu laden{/t}</button>
                    </a>
                {/if}
            </form>
        </div>
    {/if}


