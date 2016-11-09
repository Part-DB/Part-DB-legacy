<?php
/* Smarty version 3.1.30, created on 2016-11-08 22:30:30
  from "C:\xampp\htdocs\part-db\templates\nextgen\edit_part_info.php\smarty_orderdetails.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_582243f69de4b0_59639388',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '647b8e7a6e00bce0f90c10f74e652229ed5c4c70' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\edit_part_info.php\\smarty_orderdetails.tpl',
      1 => 1478640626,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_582243f69de4b0_59639388 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Einkaufsinformationen</h4>
    </div>
    <div class="panel-body table-responsive">
        <table class="table table-striped table-condensed table-hover">
           <thead>
                <tr>
                    <th></th>
                    <th>Lieferant<br>Bestellnummer</th>
                    <th>Eigenschaften</th>
                    <th>Preise</th>
                    <th></th>
                </tr>
            </thead>
            
            <tbody>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderdetails']->value, 'detail');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['detail']->value) {
?>
                <tr class="<?php if ($_smarty_tpl->tpl_vars['detail']->value['row_odd']) {?>trlist_odd<?php } else { ?>trlist_even<?php }?>">
                    <td>
                        <?php if ($_smarty_tpl->tpl_vars['detail']->value['orderdetails_id'] == "new") {?><span class="badge">Neu:</span><?php }?>
                    </td>

                    <form action="edit_part_info.php" method="post">
                        <td>
                            <select class="form-control" name="supplier_id">
                                <?php echo $_smarty_tpl->tpl_vars['detail']->value['supplier_list'];?>

                            </select>
                            <p></p>
                            <input class="form-control" type="text" name="supplierpartnr" placeholder="Bestellnr." size="12" value="<?php echo $_smarty_tpl->tpl_vars['detail']->value['supplierpartnr'];?>
">
                            <input type="hidden" name="pid" value="<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
">
                            <input type="hidden" name="orderdetails_id" value="<?php echo $_smarty_tpl->tpl_vars['detail']->value['orderdetails_id'];?>
">
                        </td>

                        <td>
                           <div class="checkbox">
                                <input type="checkbox" name="obsolete" class="styled" <?php if ($_smarty_tpl->tpl_vars['detail']->value['obsolete']) {?>checked<?php }?>>
                                <label for="obsolete" >Obsolent</label>
                            </div>
                            <?php if ($_smarty_tpl->tpl_vars['detail']->value['orderdetails_id'] == "new") {?>
                                <button class="btn btn-success" type="submit" name="orderdetails_add">Hinzufügen</button>
                            <?php } else { ?>
                                <button class="btn btn-success" type="submit" name="orderdetails_apply">Übernehmen</button>
                                <p></p>
                                <button class="btn btn-danger" type="submit" name="orderdetails_delete">Löschen</button>
                            <?php }?>
                        </td>
                    </form>

                    <td>
                        <?php if ($_smarty_tpl->tpl_vars['detail']->value['orderdetails_id'] != "new") {?>
                            <table class="table table-striped table-bordered table-condensed">
                                <thead>
                                    <tr class="trcat">
                                        <th></th>
                                        <th>Ab Bestellmenge</th>
                                        <th>Preis</th>
                                        <th>Aktionen</th>
                                    </tr>
                                </thead>

                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['detail']->value['pricedetails'], 'price');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['price']->value) {
?>
                                    <form action="" method="post">
                                        <!--the alternating background colors are created here-->
                                        <tr >
                                            <td class="tdrow1">
                                                <?php if ($_smarty_tpl->tpl_vars['price']->value['pricedetails_id'] == "new") {?><b>Neu:</b><?php }?>
                                            </td>

                                            <td class="tdrow1">
                                                <input type="number" min="0" class="form-control" name="min_discount_quantity" size="5" onkeypress="validatePosIntNumber(event)" value="<?php echo $_smarty_tpl->tpl_vars['price']->value['min_discount_quantity'];?>
" <?php if ($_smarty_tpl->tpl_vars['price']->value['min_discount_quantity'] == 1) {?>disabled<?php }?>>
                                            </td>

                                            <td class="row">
                                                <div class="col-md-5">
                                                    <input type="number" min="0" class="form-control" name="price" size="7" onkeypress="validatePosFloatNumber(event)" value="<?php echo $_smarty_tpl->tpl_vars['price']->value['price'];?>
">
                                                </div>
                                                <div class="col-md-1">
                                                    pro
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="number" min="0" class="form-control" name="price_related_quantity" size="5" onkeypress="validatePosIntNumber(event)" value="<?php echo $_smarty_tpl->tpl_vars['price']->value['price_related_quantity'];?>
">
                                                </div>
                                                <div class="col-md-1">
                                                    <label for="price_related_quantity">Stk.</label>
                                                </div>
                                            </td>

                                            <td class="tdrow1">
                                                <input type="hidden" name="pid" value="<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
">
                                                <input type="hidden" name="$price.pricedetails_id" value="<?php echo $_smarty_tpl->tpl_vars['price']->value['pricedetails_id'];?>
">
                                                <input type="hidden" name="$detail.orderdetails_id" value="<?php echo $_smarty_tpl->tpl_vars['detail']->value['orderdetails_id'];?>
">
                                                <?php if ($_smarty_tpl->tpl_vars['price']->value['pricedetails_id'] == "new") {?>
                                                    <button type="submit" class="btn btn-default" name="pricedetails_add">Hinzufügen</button>
                                                <?php } else { ?>
                                                    <div class="btn-group">
                                                        <button class="btn btn-default" type="submit" name="pricedetails_apply">Übernehmen</button>
                                                        <button class="btn btn-default" type="submit" name="pricedetails_delete">Löschen</button>
                                                    </div>
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
                        <br>
                        <?php }?>
                    </td>
                </tr>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

            </tbody>
        </table>
    </div>
</div>
<?php }
}
