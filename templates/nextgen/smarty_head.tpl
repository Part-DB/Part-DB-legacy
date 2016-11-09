<!DOCTYPE html>
<html lang="en">
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
        
        
        
        <link rel="stylesheet" type="text/css" href="{$relative_path}DataTables-1.10.12/css/jquery.dataTables.min.css"/>
 
        <script type="text/javascript" src="{$relative_path}DataTables-1.10.12/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="{$relative_path}DataTables-1.10.12/js/dataTables.bootstrap.min.js"></script>
        
        {if isset($javascript_files)}
        {foreach $javascript_files as $file}
            <script type="text/javascript" src="{$relative_path}javascript/{$file.filename}.js"></script>

            {if $file.filename=="calculator"}
                <link rel="stylesheet" href="{$relative_path}templates/{$theme}/tools_calculator.php/calculator.css" type="text/css">
            {/if}
        {/foreach}
        {/if}
                
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
      <nav class="navbar navbar-default">
         <div class="container-fluid">
         <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{$relative_path}startup.php">Part-DB</a>
          </div>

          <!-- Navbar -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <!-- Searchbar -->

               <form class="navbar-form navbar-right" action="{$relative_path}show_search_parts.php" method="get">
                  <div class=class="navbar-form navbar-right">
                     <div class="btn-group">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Suchoptionen
                        <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="SearchOptions">
                           <li class="checkbox"><input type="checkbox" name="search_name" value="true" checked>
                                <label for="search_name">Name</label></li>
                           <li class="checkbox"><input type="checkbox" class="styled" name="search_category" value="true" checked>
                               <label for="search_category">Kategorie</label></li>
                           <li class="checkbox"><input type="checkbox" name="search_description" value="true" checked>
                               <label for="search_description"></label>Beschreibung</li>
                           <li class="checkbox"><input type="checkbox" name="search_storelocation" value="true" checked>
                               <label for="search_storelocation">Lagerort</label></li>
                           <li class="checkbox"><input type="checkbox" name="search_comment" value="true" checked>
                               <label for="search_comment">Kommentar</label></li>
                           <li class="checkbox"><input type="checkbox" name="search_supplierpartnr" value="true" checked>
                               <label for="search_supplierpartnr">Bestellnr.</label></li>
                           <li class="checkbox"><input type="checkbox" name="search_supplier" value="true">
                               <label for="search_supplier">Lieferant</label></li>
                           <li class="checkbox"><input type="checkbox" name="search_manufacturer" value="true">
                               <label for="search_manufacturer">Hersteller</label></li>
                           <li class="checkbox"><input type="checkbox" name="search_footprint" value="true">
                               <label for="search_footprint">Footprint</label></li>
                        </ul>
                     </div>
                     
                    <input type="search" class="form-control" placeholder="Suche" name="keyword">
                    <button type="submit" class="btn btn-default">Los!</button>
                 
                 
                  </div>
               </form>
      
          
    
         </div><!-- /.navbar-collapse -->
     
      </div><!-- /.container-fluid -->
    </nav>
   </header>
   
   <main>
      <div class="container-fluid">
   
           <div class="row">
                <aside>
                    <div class="col-sm-3 col-md-2 sidebar">
                        <ul class="nav nav-sidebar">
                            <div id="categories">
                               <h4>Kategorien</h4>
                                <div id="tree-categories"/>
                            </div>
                            <div id="devices">
                                <h4>Baugruppen</h4>
                                <div id="tree-devices"/>
                            </div>
                            <div id="tools">
                                <h4>Verwaltung</h4>
                                <div id="tree-tools"/>
                            </div>
                        </ul>
                        
                        <ul class="nav nav-sidebar" id="tree-managment">

                        </ul>
                    </div>
                </aside>
                <div class="col-xs-12 col-sm-9 col-md-10" id="main" main >
                   <div class="container-fluid" id="content">
                       {if isset($messages)}
                        <div class="panel panel-error">
                            {if isset($messages_div_title)}<div class="panel-heading"><h2>{$messages_div_title}</h2></div>{/if}
                            <div class="panel-body">
                                <form action="" method="post">
                                    {foreach $messages as $msg}
                                        {if isset($msg.text)}
                                            {if $msg.strong}<strong>{/if}
                                            {if isset($msg.color)}<font color="{$msg.color}">{/if}
                                            {$msg.text}
                                            {if isset($msg.color)}</font>{/if}
                                            {if $msg.strong}</strong>{/if}
                                        {/if}

                                        {if isset($html)}
                                            {$html}
                                        {/if}

                                        {if !$no_linebreak}<br>{/if}
                                    {/foreach}

                                    {if isset($reload_link)}
                                        <br>
                                        <a href="{$reload_link}">
                                            <button>Seite neu laden</button>
                                        </a>
                                    {/if}
                                </form>
                            </div>
                        </div>
                    {/if}