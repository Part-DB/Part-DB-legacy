<?php
/* Smarty version 3.1.30, created on 2016-10-29 22:41:42
  from "C:\xampp\htdocs\part-db\templates\nextgen\show_search_parts.php\smarty_searched_parts_table.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_58150986421be0_15388632',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3e28eb24bdafdfb3d77e08c0159d0572d874d2d8' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\show_search_parts.php\\smarty_searched_parts_table.tpl',
      1 => 1477773286,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../smarty_table.tpl' => 1,
  ),
),false)) {
function content_58150986421be0_15388632 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="panel panel-info">
   <div class="panel-heading">
      <h4>Treffer in der Kategorie <b>"<?php echo $_smarty_tpl->tpl_vars['category_full_path']->value;?>
"</b></h4>
   </div>
    <div class="panel-body">
        <form method="post" action="">
            <input type="hidden" name="table_rowcount" value="<?php echo $_smarty_tpl->tpl_vars['table_rowcount']->value;?>
">

            <input type='hidden' name='keyword' value='<?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
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

           </form>
           
           <!-- For responsive design -->
           <div class="table-responsive">
            <table class="table table-hover table-striped">
               <?php $_smarty_tpl->_subTemplateRender("file:../smarty_table.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

            </table>
           </div> 
        
    </div>
</div>

<?php }
}
