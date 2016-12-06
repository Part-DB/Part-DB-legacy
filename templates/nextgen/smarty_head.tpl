{locale path="nextgen/locale" domain="partdb"}

<!DOCTYPE html>
<html lang="{if isset($lang)}{$lang}{else}en{/if}">
    <head>
        {if isset($http_charset)}<meta charset={$http_charset}>
        {else}<meta charset="utf-8">{/if}
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>{$page_title}</title> 
        
        <!-- Include Bootstrap -->
        <link href="{$relative_path}css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Include Awsome Font -->
        <link rel="stylesheet" href="{$relative_path}css/font-awesome.min.css">
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        
        <!-- Includes Sidebar -->
        <!-- <link href="{$relative_path}templates/{$theme}/css/simple-sidebar.css" rel="stylesheet"> -->
        
        <!-- Checkboxes -->
        <link href="{$relative_path}css/awesome-bootstrap-checkbox.css" rel="stylesheet">
        
        <!-- Fileinput -->
        <link href="{$relative_path}css/fileinput.min.css" media="all" rel="stylesheet"/>
       
        <!-- Include Part-DB Theme -->
        <link href="{$relative_path}templates/{$theme}/nextgen.css" rel="stylesheet">
        <!-- <link href="{$relative_path}templates/{$theme}/partdb.css" rel="stylesheet"> -->
        {if isset($custom_css)}<link rel="stylesheet" href="{$relative_path}{$custom_css}"> {/if}
        
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="{$relative_path}js/jquery-3.1.1.min.js"></script>
        
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="{$relative_path}js/bootstrap.min.js"></script>   
        
        <!-- jQuery Form lib -->
        <script src="{$relative_path}js/jquery.form.min.js"></script>   
        
        <!-- 3d footprint viewer -->
        <script src="http://www.x3dom.org/release/x3dom.js"></script>
        <link rel="stylesheet" href="http://www.x3dom.org/release/x3dom.css">
        
        <!--
        <link rel="stylesheet" type="text/css" href="{$relative_path}DataTables-1.10.12/css/dataTables.bootstrap.min.css"/>
        <script type="text/javascript" src="{$relative_path}DataTables-1.10.12/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="{$relative_path}DataTables-1.10.12/js/dataTables.bootstrap.min.js"></script> -->
        
        
        <link rel="stylesheet" type="text/css" href="{$relative_path}datatables/datatables.min.css"/>
        <script type="text/javascript" src="{$relative_path}datatables/datatables.min.js"></script>
        

        
        {if isset($javascript_files)}
        {foreach $javascript_files as $file}
            <script type="text/javascript" src="{$relative_path}javascript/{$file.filename}.js"></script>
        {/foreach}
        {/if}
               
        <!-- Always include CSS for Calculator. Maybe minimize this later for better performance -->  
        <link rel="stylesheet" href="{$relative_path}templates/{$theme}/tools_calculator.php/calculator.css" type="text/css">
        
        <!-- Redirect -->
       {* {if $redirect } <meta http-equiv="refresh" content="0; url={$relative_path}startup.php" /> {/if} *}
        
    </head>
    
<body>

    
    <!-- Treeview -->
    <script src="{$relative_path}js/bootstrap-treeview.js"></script>
    
    <!-- FileInput -->
    <script src="{$relative_path}js/fileinput.min.js"></script>
    
    <script src="{$relative_path}templates/nextgen/js/part-db.js"></script>
    

   
    <header>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
             <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="offcanvas">
                        <span class="sr-only">{t}Toggle Sidebar{/t}</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#searchbar" aria-expanded="false">
                        <span class="sr-only">{t}Toggle Navigation{/t}</span>
                        <span class="glyphicon glyphicon-search"></span>
                    </button>
                    <a class="navbar-brand" href="{$relative_path}startup.php"><i class="fa fa-microchip" aria-hidden="true"></i> Part-DB</a>
                </div>

                <!-- Navbar -->
                <div class="collapse navbar-collapse" id="searchbar">
                    <!-- Searchbar -->
                    <form class="navbar-form navbar-right" action="{$relative_path}show_search_parts.php" method="get">
                            <div class="btn-group">
                                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    {t}Suchoptionen{/t}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="SearchOptions">
                                   <li class="checkbox"><input type="checkbox" name="search_name" value="true" checked>
                                        <label for="search_name">{t}Name{/t}</label></li>
                                   <li class="checkbox"><input type="checkbox" class="styled" name="search_category" value="true" checked>
                                       <label for="search_category">{t}Kategorie{/t}</label></li>
                                   <li class="checkbox"><input type="checkbox" name="search_description" value="true" checked>
                                       <label for="search_description"></label>{t}Beschreibung{/t}</li>
                                   <li class="checkbox"><input type="checkbox" name="search_storelocation" value="true" checked>
                                       <label for="search_storelocation">{t}Lagerort{/t}</label></li>
                                   <li class="checkbox"><input type="checkbox" name="search_comment" value="true" checked>
                                       <label for="search_comment">{t}Kommentar{/t}</label></li>
                                   <li class="checkbox"><input type="checkbox" name="search_supplierpartnr" value="true" checked>
                                       <label for="search_supplierpartnr">{t}Bestellnr.{/t}</label></li>
                                   <li class="checkbox"><input type="checkbox" name="search_supplier" value="true">
                                       <label for="search_supplier">{t}Lieferant{/t}</label></li>
                                   <li class="checkbox"><input type="checkbox" name="search_manufacturer" value="true">
                                       <label for="search_manufacturer">{t}Hersteller{/t}</label></li>
                                   <li class="checkbox"><input type="checkbox" name="search_footprint" value="true">
                                       <label for="search_footprint">{t}Footprint{/t}</label></li>
                                </ul>
                            </div>

                            <input type="search" class="form-control" placeholder="{t}Suche{/t}" name="keyword">
                            <button type="submit" class="btn btn-default">{t}Los!{/t}</button>
                    </form>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
    </header>
   
   <main>
      <div class="container-fluid">
   
           <div class="row row-offcanvas row-offcanvas-right active">
                <aside class="hidden-print">
                   <nav>
                    <div class="col-sm-3 col-md-2" id="sidebar">
                        <ul class="nav navmenu-nav nav-sidebar">
                            <div id="categories">
                                <h4>{t}Kategorien{/t}</h4>
                                <div id="tree-categories"></div>
                            </div>
                            <div id="devices">
                                <h4>{t}Baugruppen{/t}</h4>
                                <div id="tree-devices"></div>
                            </div>
                            <div id="tools">
                                <h4>{t}Verwaltung{/t}</h4>
                                <div id="tree-tools"></div>
                            </div>
                        </ul>

                    </div>
                    </nav>
                </aside>
                
                <div class="col-sm-9 col-md-10" id="main" main >
                   <div class="container-fluid" id="content">
                       
                       {if isset($messages)}
                        <div class="alert alert-danger">
                            {if isset($messages_div_title)}<h4>{$messages_div_title}</h4>{/if}
                                <form action="" method="post">
                                    {foreach $messages as $msg}
                                        {if isset($msg.text)}
                                            {if isset($msg.strong) && $msg.strong}<strong>{/if}
                                            {if isset($msg.color)}<font color="{$msg.color}">{/if}
                                            {$msg.text nofilter}
                                            {if isset($msg.color)}</font>{/if}
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