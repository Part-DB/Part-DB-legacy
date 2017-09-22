<?php
/* Smarty version 3.1.31, created on 2017-09-06 12:37:39
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\install.php\smarty_finish.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59afcff3951012_94540561',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '991bd3e5f87568826893cdc5fe4126ea706e9ff3' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\install.php\\smarty_finish.tpl',
      1 => 1503859154,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59afcff3951012_94540561 (Smarty_Internal_Template $_smarty_tpl) {
echo smarty_function_locale(array('path'=>"nextgen/locale",'domain'=>"partdb"),$_smarty_tpl);?>

<div class="panel panel-success">
    <div class="panel-heading"><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Installation: Fertigstellung<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</div>
    <div class="panel panel-body">
        <b><span style="color: green; "><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Herzlichen Glückwunsch, die Installation bzw. das Update von Part-DB ist fast abgeschlossen!<br>
                Weitere Einstellungen finden Sie unter dem Menüpunkt "System".<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span></b>
        <br><br>
        <form action="index.php" method="post">
            <button class="btn btn-primary" type="submit" name="finish"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Fertigstellen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
        </form>
    </div>
</div>
<?php }
}
