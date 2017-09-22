<?php
/* Smarty version 3.1.31, created on 2017-09-22 15:22:49
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\edit_groups.php\..\smarty_permissions.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59c50ea93e4734_17040690',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '49364cd1b8f4a6f868dd652b703b6b363486d0de' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\edit_groups.php\\..\\smarty_permissions.tpl',
      1 => 1506085860,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59c50ea93e4734_17040690 (Smarty_Internal_Template $_smarty_tpl) {
echo smarty_function_locale(array('path'=>"nextgen/locale",'domain'=>"partdb"),$_smarty_tpl);?>


<?php if (isset($_smarty_tpl->tpl_vars['perm_loop']->value)) {?>

    <div class="">
        <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Erläuterung der Zustände:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
        <div>
            <div class="checkbox checkbox-inline">
                <input type="checkbox" hidden>
                <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Verboten<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
            </div>
            <div class="checkbox checkbox-inline">
                <input type="checkbox" hidden checked>
                <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Erlaubt<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
            </div>
            <div class="checkbox checkbox-inline">
                <input type="checkbox" class="tristate" hidden indeterminate="indeterminate">
                <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Erbe von (übergeordneter) Gruppe<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
            </div>
        </div>
    </div>

    <br>

    <ul class="nav nav-pills">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['perm_loop']->value, 'perm_group', false, 'n');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['n']->value => $_smarty_tpl->tpl_vars['perm_group']->value) {
?>
            <li <?php if ($_smarty_tpl->tpl_vars['n']->value == 0) {?>class="active"<?php }?>><a data-toggle="pill" class="link-anchor" href="#perm_tab_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['n']->value, ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['perm_group']->value['title'], ENT_QUOTES, 'UTF-8');?>
</a></li>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    </ul>

    <div class="tab-content">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['perm_loop']->value, 'perm_group', false, 'n');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['n']->value => $_smarty_tpl->tpl_vars['perm_group']->value) {
?>
            <div id="perm_tab_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['n']->value, ENT_QUOTES, 'UTF-8');?>
" class="tab-pane fade <?php if ($_smarty_tpl->tpl_vars['n']->value == 0) {?>in active<?php }?>">
                <br>
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Berechtigung<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</th>
                        <th><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Wert<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['perm_group']->value['permissions'], 'perm');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['perm']->value) {
?>
                        <tr>
                            <td style="vertical-align: middle;">
                                <?php if ($_smarty_tpl->tpl_vars['perm']->value['readonly']) {?>
                                    <b><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['perm']->value['description'], ENT_QUOTES, 'UTF-8');?>
</b>
                                <?php } else { ?>
                                    <div class="checkbox checkbox-inline">
                                        <input type="checkbox" class="tristate-toggle-all" data-target="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['perm']->value['name'], ENT_QUOTES, 'UTF-8');?>
">
                                        <label><b><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['perm']->value['description'], ENT_QUOTES, 'UTF-8');?>
</b></label>
                                    </div>
                                <?php }?>

                            </td>
                            <td>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['perm']->value['ops'], 'op', false, 'm');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['m']->value => $_smarty_tpl->tpl_vars['op']->value) {
?>
                                    <div class="checkbox checkbox-inline"
                                         <?php if ($_smarty_tpl->tpl_vars['m']->value == 0) {?>style="margin-left: 10px"<?php }?>>
                                        <input type="checkbox" class="styled tristate" name="perm/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['perm']->value['name'], ENT_QUOTES, 'UTF-8');?>
/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['op']->value['name'], ENT_QUOTES, 'UTF-8');?>
"
                                                <?php if ($_smarty_tpl->tpl_vars['op']->value['value'] == 0) {?> indeterminate="indeterminate"<?php } elseif ($_smarty_tpl->tpl_vars['op']->value['value'] == 1) {?> checked="checked"<?php }?>
                                                <?php if ($_smarty_tpl->tpl_vars['perm']->value['readonly']) {?>disabled<?php }?>>
                                        <label><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['op']->value['description'], ENT_QUOTES, 'UTF-8');?>
</label>
                                    </div>
                                <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                            </td>
                        </tr>
                    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                    </tbody>
                </table>
            </div>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    </div>

    <?php echo '<script'; ?>
>
        $("input.tristate-toggle-all").tristate({
            change: function (state, value) {
                var $this = $(this);
                var target = $this.data('target');
                var state = $this.tristate('state');
                $("input.tristate[name^='perm/" + target + "/']").tristate('state', state);
            }
        });

    
        function toggleAllPermCheckboxes(element) {
            var $this = $(element);
            var state = $this.tristate('state');
            var target = $this.data('target');
            $("input.tristate[name^='perm/" + target + "/']").tristate('state', state);
        }
    <?php echo '</script'; ?>
>


<?php }
}
}
