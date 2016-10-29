<?php
/* Smarty version 3.1.30, created on 2016-10-29 22:36:55
  from "C:\xampp\htdocs\part-db\templates\nextgen\show_search_parts.php\smarty_search_header.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5815086721c819_70705362',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3f1ede717d0f52120aa1a73b45c6ae731581fafc' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\show_search_parts.php\\smarty_search_header.tpl',
      1 => 1477773281,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5815086721c819_70705362 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="panel panel-success">
    <div class="panel-heading">
        <h3>Suchergebnis</h3>
    </div>
    <div class="panel-body">
        Die Suche nach <b>"<?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
"</b> ergab <b><?php echo $_smarty_tpl->tpl_vars['hits_count']->value;?>
 Treffer</b>.

        <div style="float: right; display: inline;">
            <form action="" method="post" style="display: inline;">
                <input type='hidden' name='keyword'     value='<?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
'>
                <?php if (isset($_smarty_tpl->tpl_vars['search_name']->value)) {?>                <input type='hidden' name='search_name'><?php }?>
                <?php if (isset($_smarty_tpl->tpl_vars['search_category']->value)) {?>            <input type='hidden' name='search_category'><?php }?>
                <?php if (isset($_smarty_tpl->tpl_vars['search_description']->value)) {?>         <input type='hidden' name='search_description'><?php }?>
                <?php if (isset($_smarty_tpl->tpl_vars['search_comment']->value)) {?>             <input type='hidden' name='search_comment'><?php }?>
                <?php if (isset($_smarty_tpl->tpl_vars['search_supplier']->value)) {?>            <input type='hidden' name='search_supplier'><?php }?>
                <?php if (isset($_smarty_tpl->tpl_vars['search_supplierpartnr']->value)) {?>      <input type='hidden' name='search_supplierpartnr'><?php }?>
                <?php if (isset($_smarty_tpl->tpl_vars['search_storelocation']->value)) {?>       <input type='hidden' name='search_storelocation'><?php }?>
                <?php if (isset($_smarty_tpl->tpl_vars['search_footprint']->value)) {?>           <input type='hidden' name='search_footprint'><?php }?>
                <?php if (isset($_smarty_tpl->tpl_vars['search_manufacturer']->value)) {?>        <input type='hidden' name='search_manufacturer'><?php }?>

                <select name="export_format">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['export_formats']->value, 'format');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['format']->value) {
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['format']->value['value'];?>
" <?php if (isset($_smarty_tpl->tpl_vars['format']->value['selected'])) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['format']->value['text'];?>
</option>
                    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                </select>

                <input type="submit" name="export" value="Export">
            </form>
        </div>
        <div class="clear"></div>
    </div>
</div><?php }
}
