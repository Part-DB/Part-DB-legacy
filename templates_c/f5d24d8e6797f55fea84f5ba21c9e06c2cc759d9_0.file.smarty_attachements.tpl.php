<?php
/* Smarty version 3.1.30, created on 2016-11-08 22:17:04
  from "C:\xampp\htdocs\part-db\templates\nextgen\edit_part_info.php\smarty_attachements.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_582240d06e0e01_37269695',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f5d24d8e6797f55fea84f5ba21c9e06c2cc759d9' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\edit_part_info.php\\smarty_attachements.tpl',
      1 => 1478639819,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_582240d06e0e01_37269695 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Dateianhänge</h4>
    </div>
    <div class="panel-body table-responsive">
        <table class="table  table-condensed tabel-hover table-striped">
           <thead>
                <tr class="trcat">
                    <th>Bild / Link</th>
                    <th>Eigenschaften</th>
                    <th></th>
                </tr>
            </thead>

            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['attachements_loop']->value, 'attach');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['attach']->value) {
?>
                <form action="edit_part_info.php" method="post" enctype="multipart/form-data">
                    <tr>

                        <!--Picture-->
                        <td class="tdrow0">
                            <?php if ($_smarty_tpl->tpl_vars['attach']->value['id'] == "new") {?>
                                <b>Neue Datei hinzufügen:</b>
                            <?php } else { ?>
                                <?php if (isset($_smarty_tpl->tpl_vars['attach']->value['picture_filename'])) {?>
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['attach']->value['picture_filename'];?>
">
                                        <img style="max-height:180px; max-width:180px" src="<?php echo $_smarty_tpl->tpl_vars['attach']->value['picture_filename'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['attach']->value['name'];?>
">
                                    </a>
                                <?php } else { ?>
                                    <?php if (isset($_smarty_tpl->tpl_vars['attach']->value['filename'])) {?>
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['attach']->value['filename'];?>
"><?php echo $_smarty_tpl->tpl_vars['attach']->value['name'];?>
</a>
                                    <?php } else { ?>
                                        <?php echo $_smarty_tpl->tpl_vars['attach']->value['name'];?>

                                    <?php }?>
                                <?php }?>
                            <?php }?>
                        </td>

                        <td>
                            <table class="table table-striped  table-hover table-bordered">
                                <tr>
                                    <td>
                                        <b>Name:</b><br>
                                        <input type="text" class="form-control" name="name" size="12" value="<?php echo $_smarty_tpl->tpl_vars['attach']->value['name'];?>
">
                                    </td>
                                    <td>
                                        <b>Dateityp:</b><br>
                                        <select class="form-control" name="attachement_type_id">
                                            <?php echo $_smarty_tpl->tpl_vars['attach']->value['attachement_types_list'];?>

                                        </select>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <input type="checkbox" class="styled" name="show_in_table" <?php if ($_smarty_tpl->tpl_vars['attach']->value['show_in_table']) {?> checked<?php }?>>
                                            <label for="show_in_table">In Tabelle anzeigen</label>
                                        </div>
                            
                                        <?php if ($_smarty_tpl->tpl_vars['attach']->value['is_picture']) {?>
                                        <div class="checkbox">
                                            <input type="checkbox" class="styled" name="is_master_picture" <?php if ($_smarty_tpl->tpl_vars['attach']->value['is_master_picture']) {?> checked<?php }?>><label for="is_master_picture">Als Hauptbild verwenden</label>
                                        </div>
                                        <?php }?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Dateiname / URL:</b>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" class="form-control" name="attachement_filename" value="<?php echo $_smarty_tpl->tpl_vars['attach']->value['filename_base_relative'];?>
" style="width:98%">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Neue Datei hochladen:</b>
                                    </td>
                                    <td colspan="2">
                                        <input data-show-caption="false" type="file" name="attachement_file">
                                        <!--(max. <?php echo $_smarty_tpl->tpl_vars['max_upload_filesize']->value;?>
) -->
                                    </td>
                                </tr>
                            </table>
                        </td>

                        <td class="tdrow1">
                            <input type="hidden" name="pid" value="<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
">
                            <input type="hidden" name="attachement_id" value="<?php echo $_smarty_tpl->tpl_vars['attach']->value['id'];?>
">
                            <?php if ($_smarty_tpl->tpl_vars['attach']->value['id'] == "new") {?>
                                <button class="btn btn-success" type="submit" name="attachement_add">Hinzufügen</button>
                            <?php } else { ?>
                                <button class="btn btn-success" type="submit" name="attachement_apply">Übernehmen</button>
                                <button class="btn btn-danger" type="submit" name="attachement_delete">Löschen</button>
                            <?php }?>
                        </td>
                    </tr>
                </form>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

        </table>
    </div>
</div>
<?php }
}
