<?php
/* Smarty version 3.1.30, created on 2016-10-29 11:42:50
  from "C:\xampp\htdocs\part-db\templates\nextgen\smarty_foot.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_58146f1a895353_81073237',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '70ca3354147cd17ffb37168b3004d450afea05ce' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\smarty_foot.tpl',
      1 => 1477734149,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_58146f1a895353_81073237 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['messages']->value)) {?>
    <div class="outer">
        <form action="" method="post">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, 'from', 'messages');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['messages']->value) {
?>
                <?php if (isset($_smarty_tpl->tpl_vars['text']->value)) {?>
                    <?php if (isset($_smarty_tpl->tpl_vars['strong']->value)) {?><strong><?php }?>
                    <?php if (isset($_smarty_tpl->tpl_vars['color']->value)) {?><font color="<?php echo $_smarty_tpl->tpl_vars['color']->value;?>
"><?php }?>
                    <?php echo $_smarty_tpl->tpl_vars['text']->value;?>

                    <?php if (isset($_smarty_tpl->tpl_vars['color']->value)) {?></font><?php }?>
                    <?php if (isset($_smarty_tpl->tpl_vars['strong']->value)) {?></strong><?php }?>
                <?php }?>

                <?php if (isset($_smarty_tpl->tpl_vars['html']->value)) {?>
                    <?php echo $_smarty_tpl->tpl_vars['html']->value;?>

                <?php }?>

                <?php if (!isset($_smarty_tpl->tpl_vars['no_linebreak']->value)) {?><br><?php }?>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

        </form>
    </div>
<?php }?>


</body>

</html>
<?php }
}
