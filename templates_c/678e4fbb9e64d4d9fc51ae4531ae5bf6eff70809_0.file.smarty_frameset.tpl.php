<?php
/* Smarty version 3.1.30, created on 2016-10-29 11:15:36
  from "C:\xampp\htdocs\part-db\templates\nextgen\index.php\smarty_frameset.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_581468b8b397a6_47244057',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '678e4fbb9e64d4d9fc51ae4531ae5bf6eff70809' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\index.php\\smarty_frameset.tpl',
      1 => 1477732532,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_581468b8b397a6_47244057 (Smarty_Internal_Template $_smarty_tpl) {
?>
<body>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <?php echo '<script'; ?>
 src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"><?php echo '</script'; ?>
>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
templates/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/js/bootstrap.min.js"><?php echo '</script'; ?>
>
    
    <frameset cols="300,*" frameborder="0" framespacing="0" border="0">
        <frame name="navigation_frame" src="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
navigation.php">
        <frame name="content_frame" src="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
startup.php">
    </frameset>

</body><?php }
}
