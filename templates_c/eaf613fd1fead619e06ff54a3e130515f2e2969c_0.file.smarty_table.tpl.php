<?php
/* Smarty version 3.1.30, created on 2016-11-08 19:04:11
  from "C:\xampp\htdocs\part-db\templates\nextgen\show_search_parts.php\..\smarty_table.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5822139b122191_03380885',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'eaf613fd1fead619e06ff54a3e130515f2e2969c' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\show_search_parts.php\\..\\smarty_table.tpl',
      1 => 1478628227,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5822139b122191_03380885 (Smarty_Internal_Template $_smarty_tpl) {
?>
  
   <div class="table-responsive">
    <table id="parts-table" class="table table-striped table-condensed table-compact table-hover tablesorter"> 
        <thead class="thead-default">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['table']->value, 't');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['t']->value) {
?>
            <?php if (isset($_smarty_tpl->tpl_vars['t']->value['print_header']) && $_smarty_tpl->tpl_vars['t']->value['print_header']) {?>   
               <tr class="trcat">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['t']->value['columns'], 'col');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['col']->value) {
?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "row") {?><th>Nr.</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "hover_picture") {?><th class="no-sort"></th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "id") {?><th class="idclass">ID</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "row_index") {?><th class="idclass">Nr.</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "name") {?><th>Name</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "name_edit") {?><th>Name</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "description") {?><th>Beschreibung</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "description_edit") {?><th>Beschreibung</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "comment") {?><th>Kommentar</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "comment_edit") {?><th>Kommentar</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "name_description") {?><th>Name / Beschreibung</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "instock") {?><th>Bestand</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "instock_edit") {?><th>Bestand</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "instock_edit_buttons") {?><th>Bestand ändern</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "order_quantity_edit") {?><th>Bestell-<br>menge</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "mininstock") {?><th>Mindest-<br>bestand</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "mininstock_edit") {?><th>Mindest-<br>bestand</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "instock_mininstock") {?><th>Vorh./<br>Min.Best</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "category") {?><th>Kategorie</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "category_edit") {?><th>Kategorie</th><?php }?> 
                        <?php if (!$_smarty_tpl->tpl_vars['disable_footprints']->value) {?>
                            <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "footprint") {?><th>Footprint</th><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "footprint_edit") {?><th>Footprint</th><?php }?> 
                        <?php }?>
                        <?php if (!$_smarty_tpl->tpl_vars['disable_manufacturers']->value) {?>
                            <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "manufacturer") {?><th>Hersteller</th><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "manufacturer_edit") {?><th>Hersteller</th><?php }?> 
                        <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "storelocation") {?><th>Lagerort</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "storelocation_edit") {?><th>Lagerort</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "suppliers") {?><th>Lieferanten</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "supplier_edit") {?><th>Lieferant</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "suppliers_radiobuttons") {?><th>Lieferanten</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "datasheets") {
if (!$_smarty_tpl->tpl_vars['disable_auto_datasheets']->value) {?><th>Datenblätter</th><?php }
}?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "button_decrement") {?><th class="text-center no-sort">-</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "button_increment") {?><th class="text-center no-sort">+</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "order_options") {?><th>Optionen</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "quantity_edit") {?><th>Anzahl</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "mountnames_edit") {?><th>Bestückungs-<br>daten</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "price_edit") {?><th>Preis</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "average_single_price") {?><th>Einzel-<br>preis Ø</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "single_prices") {?><th>Einzel-<br>preise</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "total_prices") {?><th>Gesamt-<br>preise</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "supplier_partnrs") {?><th>Bestell-<br>nummern</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "supplier_partnr_edit") {?><th>Bestell-<br>nummer</th><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "attachements") {?><th>Dateianhänge</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "systemupdate_from_version") {?><th>Von Version</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "systemupdate_to_version") {?><th>Auf Version</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "systemupdate_release_date") {?><th>Veröffentlichung</th><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['col']->value['caption'] == "systemupdate_changelog") {?><th>Changelog</th><?php }?>
                    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                </tr>
            <?php }?>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

        </thead>
        
        <tbody>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['table']->value, 't');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['t']->value) {
?>
            <?php if (!isset($_smarty_tpl->tpl_vars['t']->value['print_header']) || !$_smarty_tpl->tpl_vars['t']->value['print_header']) {?>
                <input type="hidden" name="id_<?php echo $_smarty_tpl->tpl_vars['t']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['t']->value['id'];?>
">
                
                <tr>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['t']->value['row_fields'], 'row');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "row") {?>
                        
                        <td class="tdrow1"><?php echo $_smarty_tpl->tpl_vars['row']->value['row'];?>
</td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "hover_picture") {?>
                        
                        <td class="tdrow0">
                            <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "hover_picture") {?>
                                <p>
                                    <img class="hoverpic img-responsive" src="<?php echo $_smarty_tpl->tpl_vars['row']->value['small_picture'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['row']->value['picture_name'];?>
">
                                </p>
                            <?php } else { ?>
                                <?php if ($_smarty_tpl->tpl_vars['row']->value['small_picture']) {?>
                                    <img class="hoverpic img-responsive" src="<?php echo $_smarty_tpl->tpl_vars['row']->value['small_picture'];?>
" alt="">
                                <?php }?>
                            <?php }?>
                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "id") {?>
                        
                        <td class="tdrow4 idclass<?php if ($_smarty_tpl->tpl_vars['row']->value['part_not_found']) {?> backred<?php }?>"><?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
</td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "row_index") {?>
                        
                        <td class="tdrow4 idclass"><?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
</td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "name") {?>
                        
                        <td class="tdrow1<?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "obsolete") {?> backred<?php }?>">
                            <a  data-toggle="tooltip" title="<?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "obsolete") {?>(nicht mehr erhätlich) <?php }
if (isset($_smarty_tpl->tpl_vars['row']->value['comment'])) {?>Kommentar: <?php echo $_smarty_tpl->tpl_vars['row']->value['comment'];
}?>"
                                href="show_part_info.php?pid=<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
"
                                onclick="loadLink(this);"><?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>

                            </a>
                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "name_edit") {?>
                        
                        <td class="tdrow1"><input type="text" style="width:150px" name="name_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>
"></td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "description") {?>
                        
                        <td class="tdrow1<?php if ($_smarty_tpl->tpl_vars['row']->value['obsolete']) {?> backred<?php }?>"><?php echo $_smarty_tpl->tpl_vars['row']->value['description'];?>
</td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "description_edit") {?>
                        
                        <td class="tdrow1"><input type="text" style="width:150px" name="description_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['description'];?>
"></td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "comment") {?>
                        
                        <td class="tdrow1"><?php echo $_smarty_tpl->tpl_vars['row']->value['comment'];?>
</td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "comment_edit") {?>
                        
                        <td class="tdrow1"><input type="text" style="width:150px" name="comment_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['comment'];?>
"></td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "name_description") {?>
                        
                        <td class="tdrow1<?php if ($_smarty_tpl->tpl_vars['row']->value['obsolete']) {?> backred<?php }?>">
                            <a data-toggle="tooltip" title="<?php if ($_smarty_tpl->tpl_vars['row']->value['obsolete']) {?>(nicht mehr erhätlich) <?php }
if ($_smarty_tpl->tpl_vars['row']->value['comment']) {?>Kommentar: <?php echo $_smarty_tpl->tpl_vars['row']->value['comment'];
}?>"
                                href="show_part_info.php?pid=<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" onclick="loadLink(this);">
                                <?php echo $_smarty_tpl->tpl_vars['name']->value;
if (isset($_smarty_tpl->tpl_vars['row']->value['description'])) {?>&nbsp;<?php echo $_smarty_tpl->tpl_vars['row']->value['description'];
}?>
                            </a>
                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "instock") {?>
                        
                        <td class="tdrow2 <?php if ($_smarty_tpl->tpl_vars['row']->value['not_enought_instock']) {?> backred<?php }?>">
                            <div data-toggle="tooltip" title="min. Bestand: <?php echo $_smarty_tpl->tpl_vars['row']->value['mininstock'];?>
"><?php echo $_smarty_tpl->tpl_vars['row']->value['instock'];?>
</div>
                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "instock_edit") {?>
                        
                        <td class="tdrow1"><input type="text" style="width:45px" name="instock_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['instock'];?>
" onkeypress="validatePosIntNumber(event)"></td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "order_quantity_edit") {?>
                        
                        <td class="tdrow1" nowrap>
                            <input type="text" style="width:45px" name="order_quantity_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['order_quantity'];?>
" onkeypress="validatePosIntNumber(event)"><br>
                            (mind. <?php echo $_smarty_tpl->tpl_vars['row']->value['min_order_quantity'];?>
)
                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "mininstock") {?>
                        
                        <td class="tdrow2">
                            <?php echo $_smarty_tpl->tpl_vars['row']->value['mininstock'];?>

                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "mininstock_edit") {?>
                        
                        <td class="tdrow1"><input type="text" style="width:45px" name="mininstock_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['mininstock'];?>
" onkeypress="validatePosIntNumber(event)"></td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "instock_mininstock") {?>
                        
                        <td class="tdrow2 <?php if ($_smarty_tpl->tpl_vars['row']->value['not_enought_instock']) {?> backred<?php }?>">
                            <?php echo $_smarty_tpl->tpl_vars['row']->value['instock'];?>
/<?php echo $_smarty_tpl->tpl_vars['row']->value['mininstock'];?>

                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "category") {?>
                        
                        <td class="tdrow1">
                            <div data-toggle="tooltip" title="<?php echo $_smarty_tpl->tpl_vars['row']->value['category_path'];?>
"><?php echo $_smarty_tpl->tpl_vars['row']->value['category_name'];?>
</div>
                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "category_edit") {?>
                        
                        <td class="tdrow1"><input type="text" style="width:100px" name="category_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['category_name'];?>
"></td>
                    <?php }?>
                    <?php if (!$_smarty_tpl->tpl_vars['disable_footprints']->value) {?>
                        <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "footprint") {?>
                            
                            <td class="tdrow1">
                            <?php if (isset($_smarty_tpl->tpl_vars['row']->value['footprint_path'])) {?>
                                <div title="<?php echo $_smarty_tpl->tpl_vars['row']->value['footprint_path'];?>
"><?php echo $_smarty_tpl->tpl_vars['row']->value['footprint_name'];?>
</div>
                            <?php }?>
                            </td>
                        <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "footprint_edit") {?>
                            
                            <td class="tdrow1"><input type="text" style="width:100px" name="footprint_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['footprint_name'];?>
"></td>
                        <?php }?>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "disable_manufacturers") {?>
                        <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "manufacturer") {?>
                            
                            <td class="tdrow1">
                                <div title="<?php echo $_smarty_tpl->tpl_vars['row']->value['manufacturer_path'];?>
"><?php echo $_smarty_tpl->tpl_vars['row']->value['manufacturer_name'];?>
</div>
                            </td>
                        <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "manufacturer_edit") {?>
                            
                            <td class="tdrow1"><input type="text" style="width:100px" name="manufacturer_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['manufacturer_name'];?>
"></td>
                        <?php }?>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "storelocation") {?>
                        
                        <td class="tdrow1" id="location">
                            <?php if (isset($_smarty_tpl->tpl_vars['row']->value['storelocation_path'])) {?>
                            <div title="<?php echo $_smarty_tpl->tpl_vars['row']->value['storelocation_path'];?>
"><?php echo $_smarty_tpl->tpl_vars['row']->value['storelocation_name'];?>
</div>
                            <?php }?>
                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "storelocation_edit") {?>
                        
                        <td class="tdrow1"><input type="text" style="width:100px" name="storelocation_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['storelocation_name'];?>
"></td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "datasheets") {?>
                        <?php if (!$_smarty_tpl->tpl_vars['disable_auto_datasheets']->value) {?>
                            
                            <td class="tdrow5">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['row']->value['datasheets'], 'sheet');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['sheet']->value) {
?>
                                    <a title="<?php echo $_smarty_tpl->tpl_vars['sheet']->value['name'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['sheet']->value['url'];?>
" target="_blank"><img class="companypic" src="<?php echo $_smarty_tpl->tpl_vars['relative_path']->value;
echo $_smarty_tpl->tpl_vars['sheet']->value['image'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['sheet']->value['name'];?>
"></a>
                                <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                            </td>
                        <?php }?>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "button_decrement") {?>
                        
                        <td class="tdrow6">
                           <input type="submit" class="btn btn-sm btn-outline-secondary" name="decrement_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="-"<?php if ($_smarty_tpl->tpl_vars['row']->value['decrement_disabled']) {?>disabled="disabled"<?php }?>>
                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "button_increment") {?>
                        
                        <td class="tdrow7">
                            <input type="submit" class="btn btn-sm btn-outline-secondary" name="increment_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="+">
                        </td>
                    <?php }?> 
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "order_options") {?>
                        
                        <td class="tdrow1" nowrap>
                            <input type="checkbox" name="tostock_<?php echo $_smarty_tpl->tpl_vars['row_index']->value;?>
">Einbuchen<br>
                            <?php if ($_smarty_tpl->tpl_vars['row']->value['enable_remove']) {?><input type="checkbox" name="remove_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
">Aus Liste löschen<?php }?>
                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "quantity_edit") {?>
                        
                        <td class="tdrow1" nowrap>
                            <input type="text" style="width:45px" name="quantity_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" onkeypress="validatePosIntNumber(event)" value="<?php if (isset($_smarty_tpl->tpl_vars['row']->value['quantity'])) {
echo $_smarty_tpl->tpl_vars['row']->value['quantity'];
} else { ?>0<?php }?>">
                            <input type="button" value="X" onClick="elements['quantity_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
'].value=0">
                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "mountnames_edit") {?>
                        
                        <td class="tdrow1">
                            <input type="text" size="8" name="mountnames_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['mountnames'];?>
">
                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "suppliers") {?>
                        
                        <td class="tdrow4" nowrap valign="top">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['row']->value['suppliers'], 'sup');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['sup']->value) {
?>
                                <div style="display:inline-block; height:1.7em; line-height:1.7em;">
                                    <?php echo $_smarty_tpl->tpl_vars['sup']->value['supplier_name'];?>

                                </div><br>
                            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "supplier_edit") {?>
                        
                        <td class="tdrow1"><input type="text" style="width:100px" name="supplier_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['supplier_name'];?>
"></td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "suppliers_radiobuttons") {?>
                        
                        <td class="tdrow1" nowrap valign="top">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['row']->value['suppliers_radiobuttons'], 'radio');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['radio']->value) {
?>
                                <div style="display:inline-block; height:1.7em; line-height:1.7em;">
                                    <input type="radio" name="orderdetails_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['orderdetails_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['radio']->value['selected']) {?>checked<?php }?>><?php echo $_smarty_tpl->tpl_vars['radio']->value['supplier_name'];?>
<br>
                                </div><br>
                            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "price_edit") {?>
                        
                        <td class="tdrow1"><input type="text" style="width:45px" name="price_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['price'];?>
" onkeypress="validatePosFloatNumber(event)"></td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "average_single_price") {?>
                        
                        <td class="tdrow4" nowrap>
                            <?php echo $_smarty_tpl->tpl_vars['row']->value['average_single_price'];?>

                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "single_prices") {?>
                        
                        <td class="tdrow4" nowrap valign="top">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['row']->value['single_prices'], 'price');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['price']->value) {
?>
                                <div style="display:inline-block; height:1.7em; line-height:1.7em;">
                                    <?php echo $_smarty_tpl->tpl_vars['price']->value['single_price'];?>

                                </div><br>
                            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "total_prices") {?>
                        
                        <td class="tdrow4" nowrap valign="top">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['row']->value['total_prices'], 'price');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['price']->value) {
?>
                                <div style="display:inline-block; height:1.7em; line-height:1.7em;">
                                    <?php echo $_smarty_tpl->tpl_vars['price']->value['total_price'];?>

                                </div><br>
                            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "supplier_partnrs") {?>
                        
                        <td class="tdrow1" nowrap valign="top">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['row']->value['supplier_partnrs'], 'sup');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['sup']->value) {
?>
                                <div style="display:inline-block; height:1.7em; line-height:1.7em;">
                                    <?php if (isset($_smarty_tpl->tpl_vars['sup']->value['supplier_product_url'])) {?>
                                        <a title="<?php echo $_smarty_tpl->tpl_vars['sup']->value['supplier_product_url'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['sup']->value['supplier_product_url'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['sup']->value['supplier_partnr'];?>
</a>
                                    <?php } else { ?>
                                        <?php echo $_smarty_tpl->tpl_vars['sup']->value['supplier_partnr'];?>

                                    <?php }?>
                                </div><br>
                            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "supplier_partnr_edit") {?>
                        
                        <td class="tdrow1"><input type="text" style="width:120px" name="supplier_partnr_<?php echo $_smarty_tpl->tpl_vars['row']->value['row_index'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['supplier_partnr'];?>
"></td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "attachements") {?>
                        
                        <td class="tdrow5" id="attach">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['row']->value['attachements'], 'attach');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['attach']->value) {
?>
                                <a title="<?php echo $_smarty_tpl->tpl_vars['attach']->value['type'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['attach']->value['filename'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['attach']->value['name'];?>
</a><br>
                            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                            <div></div>
                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "systemupdate_from_version") {?>
                        
                        <td class="tdrow1<?php if ($_smarty_tpl->tpl_vars['row']->value['stable']) {?> backgreen<?php }?>" style="min-width:100px">
                            <?php echo $_smarty_tpl->tpl_vars['from_version']->value;?>

                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "systemupdate_to_version") {?>
                        
                        <td class="tdrow1<?php if ($_smarty_tpl->tpl_vars['row']->value['stable']) {?> backgreen<?php }?>" style="min-width:100px">
                            <b><?php echo $_smarty_tpl->tpl_vars['to_version']->value;?>
</b>
                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "systemupdate_release_date") {?>
                        
                        <td class="tdrow1<?php if ($_smarty_tpl->tpl_vars['row']->value['stable']) {?> backgreen<?php }?>" style="min-width:100px">
                            <?php echo $_smarty_tpl->tpl_vars['release_date']->value;?>

                        </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['row']->value['caption'] == "systemupdate_changelog") {?>
                        
                        <td class="tdrow1<?php if ($_smarty_tpl->tpl_vars['row']->value['stable']) {?> backgreen<?php }?>" style="min-width:100px">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['changelog']->value, 'change');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['change']->value) {
?>
                                <?php if (isset($_smarty_tpl->tpl_vars['change']->value['log_item'])) {?>&nbsp;&bull;&nbsp;<?php echo $_smarty_tpl->tpl_vars['change']->value['log_item'];?>
<br><?php }?>
                            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                        </td>
                    <?php }?>
                <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                </tr>       
            <?php }?>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

        </tbody>
        
       </table>
    </div>
<?php }
}
