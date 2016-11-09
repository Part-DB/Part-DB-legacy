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
            {foreach $orderdetails as $detail}
                <tr class="{if $detail.row_odd}trlist_odd{else}trlist_even{/if}">
                    <td>
                        {if $detail.orderdetails_id =="new"}<span class="badge">Neu:</span>{/if}
                    </td>

                    <form action="edit_part_info.php" method="post">
                        <td>
                            <select class="form-control" name="supplier_id">
                                {$detail.supplier_list}
                            </select>
                            <p></p>
                            <input class="form-control" type="text" name="supplierpartnr" placeholder="Bestellnr." size="12" value="{$detail.supplierpartnr}">
                            <input type="hidden" name="pid" value="{$pid}">
                            <input type="hidden" name="orderdetails_id" value="{$detail.orderdetails_id}">
                        </td>

                        <td>
                           <div class="checkbox">
                                <input type="checkbox" name="obsolete" class="styled" {if $detail.obsolete}checked{/if}>
                                <label for="obsolete" >Obsolent</label>
                            </div>
                            {if $detail.orderdetails_id=="new"}
                                <button class="btn btn-success" type="submit" name="orderdetails_add">Hinzufügen</button>
                            {else}
                                <button class="btn btn-success" type="submit" name="orderdetails_apply">Übernehmen</button>
                                <p></p>
                                <button class="btn btn-danger" type="submit" name="orderdetails_delete">Löschen</button>
                            {/if}
                        </td>
                    </form>

                    <td>
                        {if $detail.orderdetails_id != "new"}
                            <table class="table table-striped table-bordered table-condensed">
                                <thead>
                                    <tr class="trcat">
                                        <th></th>
                                        <th>Ab Bestellmenge</th>
                                        <th>Preis</th>
                                        <th>Aktionen</th>
                                    </tr>
                                </thead>

                                {foreach $detail.pricedetails as $price}
                                    <form action="" method="post">
                                        <!--the alternating background colors are created here-->
                                        <tr >
                                            <td class="tdrow1">
                                                {if $price.pricedetails_id == "new"}<b>Neu:</b>{/if}
                                            </td>

                                            <td class="tdrow1">
                                                <input type="number" min="0" class="form-control" name="min_discount_quantity" size="5" onkeypress="validatePosIntNumber(event)" value="{$price.min_discount_quantity}" {if $price.min_discount_quantity == 1}disabled{/if}>
                                            </td>

                                            <td class="row">
                                                <div class="col-md-5">
                                                    <input type="number" min="0" class="form-control" name="price" size="7" onkeypress="validatePosFloatNumber(event)" value="{$price.price}">
                                                </div>
                                                <div class="col-md-1">
                                                    <label for="price">pro</label>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="number" min="0" class="form-control" name="price_related_quantity" size="5" onkeypress="validatePosIntNumber(event)" value="{$price.price_related_quantity}">
                                                </div>
                                                <div class="col-md-1">
                                                    <label for="price_related_quantity">Stk.</label>
                                                </div>
                                            </td>

                                            <td class="tdrow1">
                                                <input type="hidden" name="pid" value="{$pid}">
                                                <input type="hidden" name="$price.pricedetails_id" value="{$price.pricedetails_id}">
                                                <input type="hidden" name="$detail.orderdetails_id" value="{$detail.orderdetails_id}">
                                                {if $price.pricedetails_id == "new"}
                                                    <button type="submit" class="btn btn-default" name="pricedetails_add">Hinzufügen</button>
                                                {else}
                                                    <div class="btn-group">
                                                        <button class="btn btn-default" type="submit" name="pricedetails_apply">Übernehmen</button>
                                                        <button class="btn btn-default" type="submit" name="pricedetails_delete">Löschen</button>
                                                    </div>
                                                {/if}
                                            </td>
                                        </tr>
                                    </form>
                                {/foreach}
                            </table>
                        <br>
                        {/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
