<!DOCTYPE html>
<html lang="en">
    <head>
        {if isset($http_charset)}<meta charset={$http_charset}>
        {else}<meta charset="utf-8">{/if}
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>{$title}</title> 
        
        <!-- Include Bootstrap -->
        <link href="{$relative_path}templates/{$theme}/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        
        <!-- Include Part-DB Theme -->
        <link href="{$relative_path}templates/{$theme}/partdb.css" rel="stylesheet">
        <!-- <link href="{$relative_path}templates/{$theme}/partdb.css" rel="stylesheet"> -->
        {if isset($custom_css)}<link rel="stylesheet" href="{$relative_path}{$custom_css}"> {/if}
        
        {if isset($javascript_files)}
        {foreach $filename in $javascript_files}
            <script type="text/javascript" src="{$relative_path}javascript/{$filename}.js"></script>

            {if $filename=="calculator"}
                <link rel="stylesheet" href="{$relative_path}templates/{$theme}/tools_calculator.php/calculator.css" type="text/css">
            {/if}
        {/foreach}
        {/if}
        
        
        <base target="_self">
    </head>
    
