<?php
/* Smarty version 3.1.30, created on 2016-11-06 19:11:53
  from "C:\xampp\htdocs\part-db\templates\nextgen\show_category_parts.php\smarty_show_category_parts.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_581f7269da9453_75694013',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '26fd5d34c9b7ee46207967e60c0506b6b74f42e1' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\show_category_parts.php\\smarty_show_category_parts.tpl',
      1 => 1478455912,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../smarty_table.tpl' => 1,
  ),
),false)) {
function content_581f7269da9453_75694013 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="panel panel-success">
    <div class="panel-heading">
        <h4>Sonstiges</h4>
    </div>
    <div class="panel-body">
        <form action="" method="post">
            Unterkategorien:
            <input type="hidden" name="cid" value="<?php echo $_smarty_tpl->tpl_vars['cid']->value;?>
">
            <input type="hidden" name="subcat" value="<?php if ($_smarty_tpl->tpl_vars['with_subcategories']->value) {?>0<?php } else { ?>1<?php }?>">
            <button type="submit" class="btn btn-default" name="subcat_button" ><?php if ($_smarty_tpl->tpl_vars['with_subcategories']->value) {?>ausblenden<?php } else { ?>einblenden<?php }?></button>
        </form>
        <p></p>
        <a class="btn btn-primary" href="edit_part_info.php?category_id=<?php echo $_smarty_tpl->tpl_vars['cid']->value;?>
"
            onclick="openPart('edit_part_info.php?category_id=<?php echo $_smarty_tpl->tpl_vars['cid']->value;?>
';">
            Neues Teil in dieser Kategorie
        </a>
    </div>
</div>

<div class="panel panel-info">
    <div class="panel-heading">
        <h5>Teile in der Kategorie <b>"<?php echo $_smarty_tpl->tpl_vars['category_name']->value;?>
" </b></h5>
    </div>
    <div class="ipanel-body">
        <form method="post" action="">
            <input type="hidden" name="cid" value="<?php echo $_smarty_tpl->tpl_vars['cid']->value;?>
">
            <input type="hidden" name="subcat" value="<?php if ($_smarty_tpl->tpl_vars['with_subcategories']->value) {?>1<?php } else { ?>0<?php }?>">
            <input type="hidden" name="table_rowcount" value="<?php echo $_smarty_tpl->tpl_vars['table_rowcount']->value;?>
">
               <?php $_smarty_tpl->_subTemplateRender("file:../smarty_table.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        </form>
    </div>
</div>
<?php }
}
