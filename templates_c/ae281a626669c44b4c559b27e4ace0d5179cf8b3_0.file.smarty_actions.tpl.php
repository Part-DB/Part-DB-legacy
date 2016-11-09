<?php
/* Smarty version 3.1.30, created on 2016-11-07 19:05:31
  from "C:\xampp\htdocs\part-db\templates\nextgen\edit_part_info.php\smarty_actions.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5820c26b941e72_16785775',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ae281a626669c44b4c559b27e4ace0d5179cf8b3' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\edit_part_info.php\\smarty_actions.tpl',
      1 => 1478541927,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5820c26b941e72_16785775 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Aktionen</h4>
    </div>
    <div class="panel-body">    
       <label>Bauteil löschen:</label>
        <form  class="form-inline" action="" method="post">
           <div class="form-group">     
                <input type="hidden" name="pid" value="<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
">
                <button type="submit" class="btn btn-danger" name="delete_part">Lösche Teil!</button>
                <div class="checkbox checkbox-danger">
                    <input type="checkbox" class="styled" name="delete_files_from_hdd">
                    <label for="delete_files_from_hdd" class="text-danger">Dateien dieses Bauteiles, die von keinem anderen Bauteil verwendet werden, auch von der Festplatte löschen</label>
                </div>
            </div>
        </form>
                
        <p></p>
                
        <label>Weiteres Bauteil anlegen:</label>
        <form action="" method="post">
            <div class="form-group">
                <input type="hidden" name="pid" value="<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
">
                <button class="btn btn-default" type="submit" name="add_one_more_part">Neues Bauteil erfassen</button>
            </div>
        </form>
    </div>
</div>
<?php }
}
