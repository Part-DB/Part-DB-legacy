<?php
/* Smarty version 3.1.31, created on 2017-08-28 11:54:57
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\install.php\smarty_header.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59a3e8711d0b45_23706599',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1b2356e87153986f767fa7462b12d74e779826d5' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\install.php\\smarty_header.tpl',
      1 => 1503914091,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59a3e8711d0b45_23706599 (Smarty_Internal_Template $_smarty_tpl) {
echo smarty_function_locale(array('path'=>"nextgen/locale",'domain'=>"partdb"),$_smarty_tpl);?>


<!DOCTYPE html>
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

    <title><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Part-DB Installation/Update<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</title>

    <link href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
css/bootstrap.min.css" rel="stylesheet">

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
templates/nextgen/nextgen.css" rel="stylesheet">

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

    <!-- jQuery Form lib -->
    <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
js/jquery.form.min.js"><?php echo '</script'; ?>
>

    <!-- Functions -->
    <!-- <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
templates/nextgen/js/part-db.js"><?php echo '</script'; ?>
> -->

</head>
<body>

    <header>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <span class="navbar-brand"><i class="fa fa-microchip" aria-hidden="true"></i> Part-DB <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['system_version']->value, ENT_QUOTES, 'UTF-8');?>
 </span>

                </div>
                <div class="navbar-right">
                    <span class="navbar-brand"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Version:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['system_version_full']->value, ENT_QUOTES, 'UTF-8');?>
</span>
                </div>
            </div>
        </nav>
    </header>

<div class="container">

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
    <?php }?>


<?php }
}
