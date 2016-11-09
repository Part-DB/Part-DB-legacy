<?php
/* Smarty version 3.1.30, created on 2016-11-08 21:24:49
  from "C:\xampp\htdocs\part-db\templates\nextgen\edit_part_info.php\smarty_part.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_582234914074b4_95037672',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '13c24a67b5d80e147fa998849284f9b768b1f35d' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\edit_part_info.php\\smarty_part.tpl',
      1 => 1478636675,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_582234914074b4_95037672 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="panel <?php if ($_smarty_tpl->tpl_vars['is_new_part']->value) {?>panel-success<?php } else { ?>panel-warning<?php }?>">
    <div class="panel-heading">
        <h4>
            <?php if (!$_smarty_tpl->tpl_vars['is_new_part']->value) {?>
                Ändere Detailinfos von "<b><a href="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;?>
show_part_info.php?pid=<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['name']->value;?>
</b></a>"
                
                <div style="float: right; display: inline;">
                    ID: <?php echo $_smarty_tpl->tpl_vars['pid']->value;?>

                </div>
            <?php } else { ?>
                Neues Bauteil erstellen
            <?php }?>
        </h4>
    
    </div>    
        
    <div class="panel-body">
        <form action="edit_part_info.php" method="post">
            <!--<table class="table">-->
                <div class="row">
                    <div class="col-md-2">
                        <b>Name:</b>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="name" class="form-control" palceholder="Name" size="35" value="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
">
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-md-2">
                        <b>Beschreibung:</b>
                    </div>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="description" size="35" value="<?php echo $_smarty_tpl->tpl_vars['description']->value;?>
">
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-md-2">
                        <b>Vorhanden:</b>
                    </div>
                    <div class="col-md-10">
                        <input type="number" name="instock" class="form-control" min="0" onkeypress="validateNumber(event)" value="<?php echo $_smarty_tpl->tpl_vars['instock']->value;?>
">
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-md-2">
                        <b>Min. Bestand:</b>
                    </div>
                    <div class="col-md-10">
                        <input type="number" name="mininstock" class="form-control" min="0" onkeypress="validateNumber(event)" value="<?php echo $_smarty_tpl->tpl_vars['mininstock']->value;?>
">
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-md-2">
                        <b>Kategorie:</b>
                    </div>
                    <div class="col-md-7">
                        <select class="form-control" name="category_id" onChange="document.getElementById('search_category_name').value='__ID__='+this.value; document.getElementById('search_category').click();">
                            <?php echo $_smarty_tpl->tpl_vars['category_list']->value;?>

                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search_category_name" id="search_category_name" placeholder="Suchen / Hinzufügen" class="cleardefault" onkeydown="if (event.keyCode == 13) { document.getElementById('search_category').click();} ">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-default" name="search_category" id="search_category">OK!</button>
                            </span>
                        </div>
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-md-2">
                        <b>Lagerort:</b>
                    </div>
                    <div class="col-md-7">
                        <select class="form-control" name="storelocation_id">
                            <option value="0"></option>
                            <?php echo $_smarty_tpl->tpl_vars['storelocation_list']->value;?>

                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search_storelocation_name" class="form-control" placeholder="Suchen / Hinzufügen" class="cleardefault" onkeydown="if (event.keyCode == 13) { document.getElementById('search_storelocation').click();} ">
                            <span class="input-group-btn">
                                <button type="submit" name="search_storelocation" class="btn btn-default" id="search_storelocation">OK!</button>
                            </span>
                        </div>
                    </div>
                </div>
                <p></p>
                <?php if (!$_smarty_tpl->tpl_vars['disable_manufacturers']->value) {?>
                    <div class="row">
                        <div class="col-md-2">
                            <b>Hersteller:</b>
                        </div>
                        <div class="col-md-7">
                            <select class="form-control" name="manufacturer_id">
                                <option value="0"></option>
                                <?php echo $_smarty_tpl->tpl_vars['manufacturer_list']->value;?>

                            </select>
                        </div>
                        <div class="col-md-3">
                           <div class="input-group">
                                <input type="text" class="form-control" name="search_manufacturer_name" placeholder="Suchen / Hinzufügen" onkeydown="if (event.keyCode == 13) { document.getElementById('search_manufacturer').click();} ">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default" name="search_manufacturer" id="search_manufacturer">OK!</button>
                                </span>
                            
                            </div>
                        </div>
                    </div>
                <p></p>
                <?php }?>
                <?php if (!$_smarty_tpl->tpl_vars['disable_footprints']->value) {?>
                    <div class="row">
                        <div class="col-md-2">
                            <b>Footprint:</b>
                        </div>
                        <div class="col-md-7">
                            <select class="form-control" name="footprint_id">
                                <option value="0"></option>
                                <?php echo $_smarty_tpl->tpl_vars['footprint_list']->value;?>

                            </select>
                        </div>
                        <div class="col-md-3">
                           <div class="input-group">
                                <input type="text" name="search_footprint_name" placeholder="Suchen / Hinzufügen" class="form-control" onkeydown="if (event.keyCode == 13) { document.getElementById('search_footprint').click();} ">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default" name="search_footprint" id="search_footprint">OK!</button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <p></p>
                <?php }?>
                <div class="row">
                    <div class="col-md-2">
                        <b>Kommentar:</b>
                    </div>
                    <div class="col-md-10">
                        <textarea  class="form-control" name="comment" rows="4" cols="40"><?php echo $_smarty_tpl->tpl_vars['comment']->value;?>
</textarea>
                    </div>
                </div>
                <p></p>
                
                <div class="row">
                    <div div class="col-md-12">
                        <?php if ($_smarty_tpl->tpl_vars['is_new_part']->value) {?>
                            <button type="submit" class="btn btn-success" name="create_new_part">Bauteil erstellen</button>
                        <?php } else { ?>
                            <input type="hidden" name="pid" value="<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
">
                            <button type="submit" name="apply_attributes" class="btn btn-success">Änderungen übernehmen</button>
                            <button type="submit" class="btn btn-danger">Änderungen verwerfen</button>
                        <?php }?>
                    </div>
                </div>
            <!--</table>-->
        </form>
    </div>
</div>
<?php }
}
