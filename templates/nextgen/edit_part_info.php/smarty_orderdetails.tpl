{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i> 
        {t}Einkaufsinformationen{/t}
    </div>
    <form action="{$relative_path}edit_part_info.php" method="post" class="table-responsive">
        <table class="table table-striped table-condensed table-hover">
                <thead>
                    <tr>
                        <th></th>
                        <th>{t}Lieferant{/t}<br>{t}Bestellnummer{/t}</th>
                        <th>{t}Eigenschaften{/t}</th>
                        <th>{t}Preise{/t}</th>
                        <th></th>
                    </tr>
                </thead>
            
                <tbody>
                {foreach $orderdetails as $detail}
                    <tr>
                        <td>
                            {if $detail.orderdetails_id =="new"}<span class="label label-primary">{t}Neu:{/t}</span>{/if}
                        </td>

                        <td>
                            <select class="form-control" name="supplier_id_{$detail.orderdetails_id}">
                                {$detail.supplier_list nofilter}
                            </select>
                            <p></p>
                            <input class="form-control" type="text" name="supplierpartnr_{$detail.orderdetails_id}" placeholder="{t}Bestellnr.{/t}" size="12" value="{$detail.supplierpartnr}">
                            <input type="hidden" name="pid" value="{$pid}">
                            <input type="hidden" name="orderdetails_id_{$detail.orderdetails_id}" value="{$detail.orderdetails_id}">
                        </td>

                        <td>
                           <div class="checkbox">
                                <input type="checkbox" name="obsolete_{$detail.orderdetails_id}" class="styled" {if $detail.obsolete}checked{/if}>
                                <label for="obsolete">{t}Obsolent{/t}</label>
                            </div>
                            {if $detail.orderdetails_id=="new"}
                                <button class="btn btn-success" type="submit" name="orderdetails_add" value="{$detail.orderdetails_id}">{t}Hinzufügen{/t}</button>
                            {else}
                                <button class="btn btn-success" type="submit" name="orderdetails_apply" value="{$detail.orderdetails_id}">{t}Übernehmen{/t}</button>
                                <p></p>
                                <button class="btn btn-danger" type="submit" name="orderdetails_delete" value="{$detail.orderdetails_id}">{t}Löschen{/t}</button>
                            {/if}
                        </td>

                        <td>
                            {if $detail.orderdetails_id != "new"}
                                <table class="table table-striped table-bordered table-condensed">
                                    <thead>
                                        <tr class="trcat">
                                            <th></th>
                                            <th>{t}Ab Bestellmenge{/t}</th>
                                            <th>{t}Preis{/t}</th>
                                            <th>{t}Aktionen{/t}</th>
                                        </tr>
                                    </thead>

                                    {foreach $detail.pricedetails as $price}
                                        <tr >
                                            <td>
                                                {* Check if id contains the word "new" *}
                                                {if strpos($price.pricedetails_id , "new") !== false}<span class="label label-default">{t}Neu:{/t}</span>{/if}
                                            </td>

                                            <td>
                                                <input type="number" min="0" class="form-control" name="min_discount_quantity_{$price.pricedetails_id}" size="5" onkeypress="validatePosIntNumber(event)" value="{$price.min_discount_quantity}" {if $price.min_discount_quantity == 1}disabled{/if}>
                                            </td>

                                            <td >
                                                <div class="input-group">
                                                    <input type="text" min="0" step="0.01" class="form-control" name="price_{$price.pricedetails_id}" onkeypress="validatePosFloatNumber(event)" value="{$price.price}">
                                                    <span class="input-group-addon">{t}pro{/t}</span>
                                                    <input type="number" min="0" class="form-control" name="price_related_quantity_{$price.pricedetails_id}" onkeypress="validatePosIntNumber(event)" value="{$price.price_related_quantity}">
                                                    <span class="input-group-addon">{t}Stk.{/t}</span>
                                                </div>

                                            </td>

                                            <td>
                                                <input type="hidden" name="pid" value="{$pid}">
                                                {if strpos($price.pricedetails_id , "new") !== false}
                                                    <button type="submit" class="btn btn-default" name="pricedetails_add" value="{$detail.orderdetails_id}">{t}Hinzufügen{/t}</button>
                                                {else}
                                                    <button class="btn btn-default" type="submit" name="pricedetails_apply" value="{$price.pricedetails_id}">{t}Übernehmen{/t}</button>
                                                    <button class="btn btn-default" type="submit" name="pricedetails_delete" value="{$price.pricedetails_id}">{t}Löschen{/t}</button>
                                                {/if}
                                            </td>
                                        </tr>
                                    {/foreach}
                            </table>
                        {/if}
                    </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
    </form>
</div>
