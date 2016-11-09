<?php
/* Smarty version 3.1.30, created on 2016-11-06 14:57:54
  from "C:\xampp\htdocs\part-db\templates\nextgen\show_part_info.php\smarty_show_part_info.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_581f36e2d23c08_88120654',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '60fc701c1a78e0106d7e8124fed272faacfc09b8' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\show_part_info.php\\smarty_show_part_info.tpl',
      1 => 1478440672,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_581f36e2d23c08_88120654 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="panel panel-primary">
   <div class="panel-heading">
       <h4>Detailinfo zu <b>"<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
"</b>
            <div style="float: right; display: inline;">
                ID: <?php echo $_smarty_tpl->tpl_vars['pid']->value;?>

            </div>
        </h4>
    </div>

    <div class="panel-body">
       <div class="row">
                <div class="col-md-9">
                    <table class="table">
                       <tr>
                           <td><b>Name:</b></td>
                           <td>
                                <?php if (isset($_smarty_tpl->tpl_vars['manufacturer_product_url']->value)) {?>
                                    <a title="<?php echo $_smarty_tpl->tpl_vars['manufacturer_product_url']->value;?>
" href="<?php echo $_smarty_tpl->tpl_vars['manufacturer_product_url']->value;?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['name']->value;?>
</a>
                                <?php } else { ?>
                                    <?php echo $_smarty_tpl->tpl_vars['name']->value;?>

                                <?php }?>
                            </td>
                       </tr>
                       <tr>
                           <td><b>Beschreibung:</b></td>
                           <td><?php if (isset($_smarty_tpl->tpl_vars['description']->value)) {
echo $_smarty_tpl->tpl_vars['description']->value;
} else { ?>-<?php }?></td>
                       </tr>
                       <tr>
                           <td><b>Vorhanden:</b></td>
                           <td><?php echo $_smarty_tpl->tpl_vars['instock']->value;?>
</td>
                       </tr>
                       <tr>
                           <td><b>Min. Bestand:</b></td>
                           <td><?php echo $_smarty_tpl->tpl_vars['mininstock']->value;?>
</td>
                       </tr>
                       <tr>
                           <td><b>Kategorie:</b></td>
                           <td><?php echo $_smarty_tpl->tpl_vars['category_full_path']->value;?>
</td>
                       </tr>
                       <tr>
                           <td><b>Lagerort:</b></td>
                           <td><?php echo $_smarty_tpl->tpl_vars['storelocation_full_path']->value;
if ($_smarty_tpl->tpl_vars['storelocation_is_full']->value) {?> [voll]<?php }?></td>
                       </tr>
                       <?php if (!$_smarty_tpl->tpl_vars['disable_manufacturers']->value) {?>
                           <tr>
                               <td><b>Hersteller:</b></td>
                               <td><?php echo $_smarty_tpl->tpl_vars['manufacturer_full_path']->value;?>
</td>
                           </tr>
                       <?php }?>
                       <?php if (!$_smarty_tpl->tpl_vars['disable_footprints']->value) {?>
                           <tr>
                               <td><b>Footprint:</b></td>
                               <td>
                                   <?php echo $_smarty_tpl->tpl_vars['footprint_full_path']->value;?>
<br>
                                   <?php if (isset($_smarty_tpl->tpl_vars['footprint_filename']->value)) {?><img align="middle" src="<?php echo $_smarty_tpl->tpl_vars['footprint_filename']->value;?>
" alt="" height="70"><?php }?>
                               </td>
                           </tr>
                       <?php }?>
                       <tr>
                           <td valign="top"><b>Kommentar:</b></td>
                           <td><?php if (isset($_smarty_tpl->tpl_vars['comment']->value)) {
echo $_smarty_tpl->tpl_vars['comment']->value;
} else { ?>-<?php }?></td>
                       </tr>
                    </table>
                    
                    <a class="btn btn-primary" href="edit_part_info.php?pid=<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
">Angaben verändern</a>
                </div>

                <div class="col-md-3">
                        <form action="" method="post">
                            <input type="hidden" name="pid" value="<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="n_less">Teile entnehmen:</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="n_less" min="0" max="999" value="1" placeholder="Anzahl" onkeypress="validatePosIntNumber(event)">
                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default" name="dec">Entnehmen</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
        
                        <p></p>
                       
                        <form action="" method="post">
                            <input type="hidden" name="pid" value="<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="n_more">Teile hinzufügen</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="n_more" min="0" max="999" value="1" onkeypress="validatePosIntNumber(event)">
                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default" name="inc">Hinzufügen</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <p></p>
                       
                        <form action="" method="post">
                            <input type="hidden" name="pid" value="<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php if ($_smarty_tpl->tpl_vars['manual_order_exists']->value) {?>
                                        <label for="remove_mark_to_order">Bauteil wurde manuell zum Bestellen vorgemerkt.</label>  
                                        <button type="submit" class="btn btn-default" name="remove_mark_to_order">Aufheben</button>
                                    <?php } else { ?>
                                        <?php if ($_smarty_tpl->tpl_vars['auto_order_exists']->value) {?>
                                            <i>Das Bauteil wird unter "Zu bestellende Teile"aufgelistet, da der Bestand kleiner als der Mindestbestand ist.</i>
                                        <?php } else { ?>
                                            <label for="order_quantity">Zum Bestellen vormerken:</label>
                                            <div class="input-group">
                                                <input type="number" min="0" max="999" class="form-control" value="1" name="order_quantity" placeholder="Bestellmenge" onkeypress="validatePosIntNumber(event)"><br>
                                                <div class="input-group-btn">
                                                    <button type="submit" class="btn btn-default" name="mark_to_order">Übernehmen</button>
                                                </div>
                                            </div>
                                        <?php }?>
                                    <?php }?>
                                </div>
                            </div>
                        </form>
                </div>
            </tr>
        </div>
        </div>
    </div>

<div class="panel panel-info">
    <div class="panel-heading">
        <h4>Dateianhänge</h4>
    </div>
    <div class="panel-body">
        <?php if (isset($_smarty_tpl->tpl_vars['attachement_types_loop']->value)) {?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['attachement_types_loop']->value, 'attach_type');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['attach_type']->value) {
?>
                <b><?php echo $_smarty_tpl->tpl_vars['attach_type']->value['attachement_type'];?>
:</b><br>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['attach_type']->value['attachements_loop'], 'attach');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['attach']->value) {
?>
                    <?php if ($_smarty_tpl->tpl_vars['attach']->value['is_picture']) {?>
                        <a href="javascript:popUp('<?php echo $_smarty_tpl->tpl_vars['attach']->value['filename'];?>
', <?php if ($_smarty_tpl->tpl_vars['use_modal_popup']->value) {?>true<?php } else { ?>false<?php }?>,
                                                    <?php echo $_smarty_tpl->tpl_vars['popup_width']->value;?>
, <?php echo $_smarty_tpl->tpl_vars['popup_height']->value;?>
);">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['attach']->value['filename'];?>
" alt="Zum Vergrößern klicken!" style="max-height:180px; max-width:180px"></a>
                    <?php } else { ?>
                        <a target="_blank" href="<?php echo $_smarty_tpl->tpl_vars['attach']->value['filename'];?>
"><?php echo $_smarty_tpl->tpl_vars['attach']->value['attachement_name'];?>
</a><br>
                    <?php }?>
                <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

        <?php } else { ?>
            <p>Dieses Bauteil besitzt keine Dateianhänge.</p>
            <p><a class="btn btn-default" href="edit_part_info.php?pid=<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
">Dateianhänge hinzufügen</a></p>
        <?php }?>
    </div>
</div>
<?php }
}
