<?php
/* Smarty version 3.1.31, created on 2017-09-22 15:13:17
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\smarty_head.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59c50c6da1d066_92215326',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '789faf59016c8f386346375d0a96e2f0fdd577d3' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\smarty_head.tpl',
      1 => 1506085860,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59c50c6da1d066_92215326 (Smarty_Internal_Template $_smarty_tpl) {
echo smarty_function_locale(array('path'=>"nextgen/locale",'domain'=>"partdb"),$_smarty_tpl);?>



<?php if (!isset($_smarty_tpl->tpl_vars['ajax_request']->value) || !'ajax_request') {?>
<!DOCTYPE html>
<!--suppress JSUnresolvedLibraryURL -->
<html lang="<?php if (isset($_smarty_tpl->tpl_vars['lang']->value)) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['lang']->value, ENT_QUOTES, 'UTF-8');
} else { ?>en<?php }?>">
    <head>
        <?php if (isset($_smarty_tpl->tpl_vars['http_charset']->value)) {?><meta charset=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['http_charset']->value, ENT_QUOTES, 'UTF-8');?>
>
        <?php } else { ?><meta charset="utf-8"><?php }?>
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
/icons/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
/icons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
/icons/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
/icons/manifest.json">
        <link rel="mask-icon" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
/icons/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="shortcut icon" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
/icons/favicon.ico">
        <meta name="msapplication-config" content="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
/icons/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">
        
        <title><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['page_title']->value, ENT_QUOTES, 'UTF-8');?>
</title> 
        
        <!-- Include Bootstrap or an Bootswatch theme -->
        <?php if (!isset($_smarty_tpl->tpl_vars['custom_css']->value)) {?>
            <link href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
css/bootstrap.min.css" rel="stylesheet">
        <?php } else { ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');
echo htmlspecialchars($_smarty_tpl->tpl_vars['custom_css']->value, ENT_QUOTES, 'UTF-8');?>
">
        <?php }?>

        <!-- Include Awsome Font -->
        <link rel="stylesheet" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
css/font-awesome.min.css">
        
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

        
        <!-- Checkboxes -->
        <link href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
css/awesome-bootstrap-checkbox.css" rel="stylesheet">
        
        <!-- Fileinput -->
        <link href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
css/fileinput.min.css" media="all" rel="stylesheet"/>
       
        <!-- Include Part-DB Theme -->
        <link href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
templates/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['theme']->value, ENT_QUOTES, 'UTF-8');?>
/nextgen.css" rel="stylesheet">
        
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
js/jquery-3.2.1.min.js"><?php echo '</script'; ?>
>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
js/bootstrap.min.js"><?php echo '</script'; ?>
>

        <!-- Bootstrap select -->
        <link rel="stylesheet" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
css/bootstrap-select.min.css">
        
        <!-- 3d footprint viewer -->
        <?php if (isset($_smarty_tpl->tpl_vars['foot3d_active']->value) && $_smarty_tpl->tpl_vars['foot3d_active']->value) {?>
        <?php echo '<script'; ?>
 src="https://www.x3dom.org/release/x3dom.js" async><?php echo '</script'; ?>
>
        <link rel="stylesheet" href="https://www.x3dom.org/release/x3dom.css">
        <?php }?>

        <!-- JQuery Tristate -->
        <!-- This must be in head because we need its functions in <?php echo '<script'; ?>
> Tags, in smarty_permission.tpl -->
        <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
js/jquery.tristate.js"><?php echo '</script'; ?>
>
        

        
               
        <!-- PHP Debugbar -->
        <?php if (isset($_smarty_tpl->tpl_vars['debugbar_head']->value)) {
echo $_smarty_tpl->tpl_vars['debugbar_head']->value;
}?>



        <!-- SCEditor (WYSIWYG BBCode Editor) -->
        <!-- <link rel="stylesheet" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
js/sceditor/themes/default.min.css" />
        <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
js/sceditor/jquery.sceditor.bbcode.min.js" async><?php echo '</script'; ?>
> -->



    </head>
    
<body>

    <header>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
             <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#sidebar">
                        <span class="sr-only"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Toggle Sidebar<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span>
                        <span class="fa fa-bars"></span>
                    </button>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#searchbar" aria-expanded="false">
                        <span class="sr-only"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Toggle Navigation<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span>
                        <span class="fa fa-search"></span>
                    </button>
                    <a class="navbar-toggle link-anchor" href="zxing://scan/?ret=<?php if (isset($_SERVER['HTTPS'])) {?>https<?php } else { ?>http<?php }?>%3A%2F%2F<?php echo htmlspecialchars(rawurlencode($_SERVER['HTTP_HOST']), ENT_QUOTES, 'UTF-8');
echo htmlspecialchars(rawurlencode($_smarty_tpl->tpl_vars['relative_path']->value), ENT_QUOTES, 'UTF-8');?>
show_part_info.php%3Fbarcode%3D%7BCODE%7D&SCAN_FORMATS=EAN_8">
                        <i class="fa fa-barcode" aria-hidden="true"></i>
                        <span class="sr-only"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Scanne Barcode<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span>
                    </a>
                    <a class="navbar-brand" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
startup.php"><i class="fa fa-microchip" aria-hidden="true"></i> <?php if (!empty($_smarty_tpl->tpl_vars['partdb_title']->value)) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['partdb_title']->value, ENT_QUOTES, 'UTF-8');
} else { ?>Part-DB<?php }?></a>
                </div>

                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle link-anchor" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <?php if ($_smarty_tpl->tpl_vars['loggedin']->value) {?><i class="fa fa-user" aria-hidden="true"></i><?php } else { ?><i class="fa fa-user-o" aria-hidden="true"></i><?php }?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                        <?php if ($_smarty_tpl->tpl_vars['loggedin']->value) {?>
                            <li class="disabled"><a href="#" >Eingeloggt als <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['firstname']->value, ENT_QUOTES, 'UTF-8');?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['lastname']->value, ENT_QUOTES, 'UTF-8');?>
 (<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['username']->value, ENT_QUOTES, 'UTF-8');?>
)</a></li>
                            <li><a href="user_settings.php"><i class="fa fa-cogs" aria-hidden="true"></i> Benutzereinstellungen</a></li>
                            <li><a href="user_info.php"><i class="fa fa-info-circle" aria-hidden="true"></i> Benutzerinformationen</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
login.php?logout"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a></li>
                        <?php } else { ?>
                            <li><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
login.php"><i class="fa fa-sign-in" aria-hidden="true"></i> Login</a></li>
                        <?php }?>
                        </ul>
                    </li>
                </ul>

                <!-- Navbar -->
                <div class="collapse navbar-collapse navbar-right" id="searchbar">

                    <?php if (isset($_smarty_tpl->tpl_vars['can_search']->value) && $_smarty_tpl->tpl_vars['can_search']->value) {?>
                    <!-- Searchbar -->
                    <form class="navbar-form " action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
show_search_parts.php" method="get">
                            <div class="btn-group">
                                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Suchoptionen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="SearchOptions">
                                   <li class="checkbox"><input type="checkbox" name="search_name" value="true" checked>
                                        <label for="search_name"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Name<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label></li>
                                   <li class="checkbox"><input type="checkbox" class="styled" name="search_category" value="true" checked>
                                       <label for="search_category"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Kategorie<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label></li>
                                   <li class="checkbox"><input type="checkbox" name="search_description" value="true" checked>
                                       <label for="search_description"></label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Beschreibung<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</li>
                                   <li class="checkbox"><input type="checkbox" name="search_storelocation" value="true" checked>
                                       <label for="search_storelocation"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Lagerort<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label></li>
                                   <li class="checkbox"><input type="checkbox" name="search_comment" value="true" checked>
                                       <label for="search_comment"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Kommentar<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label></li>
                                   <?php if (!$_smarty_tpl->tpl_vars['suppliers_disabled']->value) {?>
                                   <li class="checkbox"><input type="checkbox" name="search_supplierpartnr" value="true" checked>
                                       <label for="search_supplierpartnr"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Bestellnr.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label></li>
                                   <li class="checkbox"><input type="checkbox" name="search_supplier" value="true">
                                       <label for="search_supplier"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Lieferant<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label></li> <?php }?>
                                   <?php if (!$_smarty_tpl->tpl_vars['manufacturers_disabled']->value) {?>
                                   <li class="checkbox"><input type="checkbox" name="search_manufacturer" value="true">
                                       <label for="search_manufacturer"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Hersteller<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label></li><?php }?>
                                   <?php if (!$_smarty_tpl->tpl_vars['footprints_disabled']->value) {?>
                                   <li class="checkbox"><input type="checkbox" name="search_footprint" value="true">
                                       <label for="search_footprint"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Footprint<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label></li><?php }?>
                                   <li class="checkbox"><input type="checkbox" name="disable_pid_input" value="false">
                                        <label for="disable_pid_input"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Deakt. Barcode<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label></li>
                                   <li class="checkbox"><input type="checkbox" name="regex" value="true">
                                        <label for="regex"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
RegEx Matching<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label></li>
                                </ul>
                            </div>

                            <input type="search" class="form-control" placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Suche<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
" name="keyword">
                            <button type="submit" id="search-submit" class="btn btn-default"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Los!<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                    </form>
                    <?php }?>
                </div><!-- /.navbar-collapse -->



            </div><!-- /.container-fluid -->
        </nav>
    </header>
   
   <main>
      <div class="container-fluid">
   
           <div class="row">
                <aside class="hidden-print col-sm-3 col-md-2 sidebar-collapse collapse sidebar-container" id="sidebar">
                    <nav class="fixed-sidebar">
                        <div class="">
                            <ul class="nav navmenu-nav">
                            <?php if (isset($_smarty_tpl->tpl_vars['can_category']->value) && $_smarty_tpl->tpl_vars['can_category']->value) {?>
                                <li id="categories">
                                <!-- <h4><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Kategorien<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</h4>-->
                                    <div class="dropdown">
                                        <button class="btn-text dropdown-toggle" type="button" id="dropdownCat" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <div class="sidebar-title"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Kategorien<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

                                                <span class="caret"></span></div>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownCat">
                                            <li><a href="#" class="tree-btns" data-mode="expand" data-target="tree-categories"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Alle ausklappen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a></li>
                                            <li><a href="#" class="tree-btns" data-mode="collapse" data-target="tree-categories"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Alle einklappen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a></li>
                                        </ul>
                                    </div>
                                    <div id="tree-categories"></div>
                                </li>
                                <?php }?>
                                <?php if (!$_smarty_tpl->tpl_vars['devices_disabled']->value && isset($_smarty_tpl->tpl_vars['can_device']->value) && $_smarty_tpl->tpl_vars['can_device']->value) {?>
                                <li id="devices">
                                    <div class="dropdown">
                                        <button class="btn-text dropdown-toggle" type="button" id="dropdownDev" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <div class="sidebar-title"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Baugruppen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

                                                <span class="caret"></span></div>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownDev">
                                            <li><a href="#" class="tree-btns" data-mode="expand" data-target="tree-devices"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Alle ausklappen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a></li>
                                            <li><a href="#" class="tree-btns" data-mode="collapse" data-target="tree-devices"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Alle einklappen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a></li>
                                        </ul>
                                    </div>
                                    <div id="tree-devices"></div>
                                </li>
                                <?php }?>

                                <li id="tools">
                                    <div class="dropdown">
                                        <button class="btn-text dropdown-toggle" type="button" id="dropdownTools" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <div class="sidebar-title"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Verwaltung<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

                                                <span class="caret"></span></div>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownTools">
                                            <li><a href="#" class="tree-btns" data-mode="expand" data-target="tree-tools"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Alle ausklappen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a></li>
                                            <li><a href="#" class="tree-btns" data-mode="collapse" data-target="tree-tools"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Alle einklappen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a></li>
                                        </ul>
                                    </div>
                                    <div id="tree-tools"></div>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </aside>
                
                <div class="col-sm-9 col-md-10" id="main">

                    <div class="container-fluid container-progress" id="progressbar" hidden>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                aria-valuemax="100" style="width: 100%;">
                                <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Lade<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span>
                            </div>
                        </div>
                        <h4>Dies kann einen Moment dauern...</h4>
                    </div>

                   <div class="container-fluid" id="content">

<?php } else { ?> 
                <title><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['page_title']->value, ENT_QUOTES, 'UTF-8');?>
</title>

<?php }?>


                   <div id="content-data">

                       <?php if (isset($_smarty_tpl->tpl_vars['messages']->value)) {?>
                       <?php $_smarty_tpl->_assignInScope('alert_style', "alert-info");
?>
                       <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['messages']->value, 'msg');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['msg']->value) {
?>
                            <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['color']) && $_smarty_tpl->tpl_vars['msg']->value['color'] == "red") {?>
                                <?php $_smarty_tpl->_assignInScope('alert_style', "alert-danger");
?>
                            <?php } elseif (isset($_smarty_tpl->tpl_vars['msg']->value['color']) && ($_smarty_tpl->tpl_vars['msg']->value['color'] == "green" || $_smarty_tpl->tpl_vars['msg']->value['color'] == "darkgreen")) {?>
                                 <?php $_smarty_tpl->_assignInScope('alert_style', "alert-success");
?>
                            <?php } elseif (isset($_smarty_tpl->tpl_vars['msg']->value['color']) && ($_smarty_tpl->tpl_vars['msg']->value['color'] == "yellow" || $_smarty_tpl->tpl_vars['msg']->value['color'] == "orange")) {?>
                                 <?php $_smarty_tpl->_assignInScope('alert_style', "alert-warning");
?>
                            <?php }?>
                       <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                        <div class="alert <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['alert_style']->value, ENT_QUOTES, 'UTF-8');?>
" id="messages">
                            <?php if (!empty($_smarty_tpl->tpl_vars['messages_div_title']->value)) {?><h4><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['messages_div_title']->value, ENT_QUOTES, 'UTF-8');?>
</h4><?php }?>
                                <form action="" method="post" class="no-progbar">
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['messages']->value, 'msg');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['msg']->value) {
?>
                                        <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['text'])) {?>
                                            <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['strong']) && $_smarty_tpl->tpl_vars['msg']->value['strong']) {?><strong><?php }?>
                                            <?php echo $_smarty_tpl->tpl_vars['msg']->value['text'];?>

                                            <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['strong']) && $_smarty_tpl->tpl_vars['msg']->value['strong']) {?></strong><?php }?>
                                        <?php }?>

                                        <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['html'])) {?>
                                            <?php echo $_smarty_tpl->tpl_vars['msg']->value['html'];?>

                                        <?php }?>

                                        <?php if (!isset($_smarty_tpl->tpl_vars['msg']->value['no_linebreak']) || !$_smarty_tpl->tpl_vars['msg']->value['no_linebreak']) {?><br><?php }?>
                                    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                                   
                                    <?php if (!empty($_smarty_tpl->tpl_vars['reload_link']->value)) {?>
                                        <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['reload_link']->value, ENT_QUOTES, 'UTF-8');?>
">
                                            <br>
                                            <button class="btn btn-default"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Seite neu laden<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                                        </a>
                                    <?php }?>
                                </form>
                        </div>
                    <?php }
}
}
