<?php
/* Smarty version 3.1.31, created on 2017-08-28 11:54:15
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\install.php\smarty_set_admin_password.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59a3e84790f5d5_90706189',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b972d18f8159c71afefbf3e58fdd1042417531a8' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\install.php\\smarty_set_admin_password.tpl',
      1 => 1503859154,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59a3e84790f5d5_90706189 (Smarty_Internal_Template $_smarty_tpl) {
echo smarty_function_locale(array('path'=>"nextgen/locale",'domain'=>"partdb"),$_smarty_tpl);?>


<div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-key" aria-hidden="true"></i>&nbsp;
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Installation/Update: Administratorpasswort festlegen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</div>
    <div class="panel-body">

        <p><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Für spätere Systemänderungen oder zum Debuggen muss ein Administratorpasswort gewählt werden.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</p>


        <form action="" method="post" class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Administratorpasswort:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                <div class="col-md-9">
                    <input class="form-control" type="password" name="adminpass_1" required>
                </div>
            </div>
            <div class="form-group">
                    <label class="col-md-3 control-label"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Wiederholung:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                    <div class="col-md-9">
                        <input type="password" class="form-control" name="adminpass_2" required>
                    </div>
            </div>
            <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                    <button class="btn btn-primary" type="submit" name="save_admin_password"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Weiter<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php }
}
