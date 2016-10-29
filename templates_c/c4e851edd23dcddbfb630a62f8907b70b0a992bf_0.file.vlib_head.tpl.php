<?php
/* Smarty version 3.1.30, created on 2016-10-29 00:47:47
  from "C:\xampp\htdocs\part-db\templates\nextgen\vlib_head.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5813d593f36239_46190739',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c4e851edd23dcddbfb630a62f8907b70b0a992bf' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\vlib_head.tpl',
      1 => 1477694864,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5813d593f36239_46190739 (Smarty_Internal_Template $_smarty_tpl) {
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
/partdb.css" rel="stylesheet">
        <?php if (isset($_smarty_tpl->tpl_vars['custom_css']->value)) {?><link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;
echo $_smarty_tpl->tpl_vars['custom_css']->value;?>
"> <?php }?>
        
        <?php if (isset($_smarty_tpl->tpl_vars['javascript_files']->value)) {?>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['filename']->value, 'in', false, 'javascript_files');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['javascript_files']->value => $_smarty_tpl->tpl_vars['in']->value) {
?>
            <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
javascript/<?php echo $_smarty_tpl->tpl_vars['filename']->value;?>
.js"><?php echo '</script'; ?>
>

            <?php if ($_smarty_tpl->tpl_vars['filename']->value == "calculator") {?>
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
    </head>
    
    <body class="body" onload="<?php echo $_smarty_tpl->tpl_vars['body_onload']->value;?>
"> 
        
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <?php echo '<script'; ?>
 src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"><?php echo '</script'; ?>
>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <?php echo '<script'; ?>
 src="js/bootstrap.min.js"><?php echo '</script'; ?>
>
        
        
        
    </body><?php }
}
