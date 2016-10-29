<?php
/* Smarty version 3.1.30, created on 2016-10-29 22:26:34
  from "C:\xampp\htdocs\part-db\templates\nextgen\smarty_head.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_581505fae50ff1_55327727',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c62886e2cd9939019d2f9a5decfc9017dfc45286' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\smarty_head.tpl',
      1 => 1477772790,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_581505fae50ff1_55327727 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php if (isset($_smarty_tpl->tpl_vars['http_charset']->value)) {?><meta charset=<?php echo $_smarty_tpl->tpl_vars['http_charset']->value;?>
>
        <?php } else { ?><meta charset="utf-8"><?php }?>
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title> 
        
        <!-- Include Bootstrap -->
        <link href="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
templates/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <?php echo '<script'; ?>
 src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"><?php echo '</script'; ?>
>
            <?php echo '<script'; ?>
 src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"><?php echo '</script'; ?>
>
        <![endif]-->
        
        <!-- Include Part-DB Theme -->
        <link href="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
templates/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/nextgen.css" rel="stylesheet">
        <!-- <link href="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
templates/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/partdb.css" rel="stylesheet"> -->
        <?php if (isset($_smarty_tpl->tpl_vars['custom_css']->value)) {?><link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;
echo $_smarty_tpl->tpl_vars['custom_css']->value;?>
"> <?php }?>
        
        <?php if (isset($_smarty_tpl->tpl_vars['javascript_files']->value)) {?>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, 'javascript_files', 'file');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['file']->value) {
?>
            <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
javascript/<?php echo $_smarty_tpl->tpl_vars['file']->value['filename'];?>
.js"><?php echo '</script'; ?>
>

            <?php if ($_smarty_tpl->tpl_vars['file']->value['filename'] == "calculator") {?>
                <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
templates/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/tools_calculator.php/calculator.css" type="text/css">
            <?php }?>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

        <?php }?>
        
        
        <base target="_self">
        
        <!-- Redirect -->
        <?php if ($_smarty_tpl->tpl_vars['redirect']->value) {?> <meta http-equiv="refresh" content="0; url=<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
startup.php" /> <?php }?>
        
    </head>
    
<body>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <?php echo '<script'; ?>
 src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"><?php echo '</script'; ?>
>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
templates/nextgen/js/bootstrap.min.js"><?php echo '</script'; ?>
>
   
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
            <a class="navbar-brand" href="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
startup.php">Part-DB</a>
          </div>

          <!-- Navbar -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Kategorien<span class="caret"></span></a>
                <ul class="dropdown-menu">

                </ul>
              </li>

              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Verwaltung<span class="caret"></span></a>
                <ul class="dropdown-menu">

                </ul> 
                </li>
            </ul>

            <!-- Searchbar -->

               <form class="navbar-form navbar-right" action="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
show_search_parts.php" method="get">
                  <div class=class="navbar-form navbar-right">
                     <div class="btn-group">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Suchoptionen
                        <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-radio" aria-labelledby="dropdownMenu1">
                           <li><input class="drop-radio" type="checkbox" name="search_name" value="true" checked>Name</li>
                           <li><input type="checkbox" name="search_category" value="true" checked>Kategorie</li>
                           <li><input type="checkbox" name="search_description" value="true" checked>Beschreibung</li>
                           <li><input type="checkbox" name="search_storelocation" value="true" checked>Lagerort</li>
                           <li><input type="checkbox" name="search_comment" value="true" checked>Kommentar</li>
                           <li><input type="checkbox" name="search_supplierpartnr" value="true" checked>Bestellnr.</li>
                           <li><input type="checkbox" name="search_supplier" value="true">Lieferant</li>
                           <li><input type="checkbox" name="search_manufacturer" value="true">Hersteller</li>
                           <li><input type="checkbox" name="search_footprint" value="true">Footprint</li>
                        </ul>
                     </div>
                     <input type="text" class="form-control" placeholder="Suche" name="keyword">
                     <button type="submit" class="btn btn-default">Los!</button>

                  </div>
               </form>
      
          
    
         </div><!-- /.navbar-collapse -->
     
      </div><!-- /.container-fluid -->
    </nav>
   </header>
   
   <main>
      <div class="container-fluid">
   
         <?php if (isset($_smarty_tpl->tpl_vars['messages']->value)) {?>
              <div class="panel panel-error">
                  <?php if (isset($_smarty_tpl->tpl_vars['messages_div_title']->value)) {?><div class="panel-heading"><h2><?php echo $_smarty_tpl->tpl_vars['messages_div_title']->value;?>
</h2></div><?php }?>
                  <div class="panel-body">
                      <form action="" method="post">
                          <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['messages']->value, 'msg');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['msg']->value) {
?>
                              <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['text'])) {?>
                                  <?php if ($_smarty_tpl->tpl_vars['msg']->value['strong']) {?><strong><?php }?>
                                  <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['color'])) {?><font color="<?php echo $_smarty_tpl->tpl_vars['msg']->value['color'];?>
"><?php }?>
                                  <?php echo $_smarty_tpl->tpl_vars['msg']->value['text'];?>

                                  <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['color'])) {?></font><?php }?>
                                  <?php if ($_smarty_tpl->tpl_vars['msg']->value['strong']) {?></strong><?php }?>
                              <?php }?>

                              <?php if (isset($_smarty_tpl->tpl_vars['html']->value)) {?>
                                  <?php echo $_smarty_tpl->tpl_vars['html']->value;?>

                              <?php }?>

                              <?php if (!$_smarty_tpl->tpl_vars['no_linebreak']->value) {?><br><?php }?>
                          <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>


                          <?php if (isset($_smarty_tpl->tpl_vars['reload_link']->value)) {?>
                              <br>
                              <a href="<?php echo $_smarty_tpl->tpl_vars['reload_link']->value;?>
">
                                  <button>Seite neu laden</button>
                              </a>
                          <?php }?>
                      </form>
                  </div>
              </div>
          <?php }
}
}
