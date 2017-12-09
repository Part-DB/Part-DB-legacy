{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-default" >
    <div class="panel-heading">
        <i class="fa fa-shopping-cart fa-fw" aria-hidden="true"></i>
        {t}Einkaufsinformationen{/t}
    </div>
    <form action="{$relative_path}edit_part_info.php" method="post" class="table-responsive no-progbar" id="orderdetails">
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
                            <select class="form-control" name="supplier_id_{$detail.orderdetails_id}"
                                    {if !($can_orderdetails_edit || ($can_orderdetails_create && $detail.orderdetails_id == "new"))}disabled{/if}>
                                {$detail.supplier_list nofilter}
                            </select>
                            <p></p>
                            <input class="form-control" type="text" name="supplierpartnr_{$detail.orderdetails_id}" placeholder="{t}Bestellnr.{/t}"
                                   size="12" value="{$detail.supplierpartnr}"
                                   {if !($can_orderdetails_edit || ($can_orderdetails_create && $detail.orderdetails_id == "new"))}disabled{/if}>
                            <input type="hidden" name="pid" value="{$pid}">
                            <input type="hidden" name="orderdetails_id_{$detail.orderdetails_id}" value="{$detail.orderdetails_id}">
                        </td>

                        <td>
                           <div class="checkbox">
                                <input type="checkbox" name="obsolete_{$detail.orderdetails_id}" class="styled" {if $detail.obsolete}checked{/if}
                                        {if !($can_orderdetails_edit || ($can_orderdetails_create && $detail.orderdetails_id == "new"))}disabled{/if}>
                                <label for="obsolete">{t}Obsolet{/t}</label>
                            </div>
                            {if $detail.orderdetails_id=="new"}
                                <button class="btn btn-success" type="submit" name="orderdetails_add" value="{$detail.orderdetails_id}"
                                        {if !$can_orderdetails_create}disabled{/if}>{t}Hinzufügen{/t}</button>
                            {else}
                                <button class="btn btn-success" type="submit" name="orderdetails_apply" value="{$detail.orderdetails_id}"
                                        {if !$can_orderdetails_edit}disabled{/if}>{t}Übernehmen{/t}</button>
                                <p></p>
                                <button class="btn btn-danger" type="submit" name="orderdetails_delete" value="{$detail.orderdetails_id}"
                                        {if !$can_orderdetails_delete}disabled{/if}>{t}Löschen{/t}</button>
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
                                        <!--the alternating background colors are created here-->
                                        <tr >
                                            <td>
                                                {if $price.pricedetails_id == "new"}<span class="label label-default">{t}Neu:{/t}</span>{/if}
                                            </td>

                                            <td>
                                                <input type="number" min="0" class="form-control" name="min_discount_quantity_{if $price.pricedetails_id == "new"}{$detail.orderdetails_id}_{/if}{$price.pricedetails_id}" size="5" value="{$price.min_discount_quantity}"
                                                       {if $price.min_discount_quantity == 1}disabled{/if}
                                                        {if !($can_prices_edit || ($can_prices_create && $price.pricedetails_id == "new"))}disabled{/if}>
                                            </td>

                                            <td >
                                                <div class="input-group">
                                                    <input type="number" min="0" step="any" class="form-control"
                                                           name="price_{if $price.pricedetails_id == "new"}{$detail.orderdetails_id}_{/if}{$price.pricedetails_id}"
                                                           value="{$price.price}"
                                                           {if !($can_prices_edit || ($can_prices_create && $price.pricedetails_id == "new"))}disabled{/if}>
                                                    <span class="input-group-addon">{t}pro{/t}</span>
                                                    <input type="number" min="0" class="form-control"
                                                           name="price_related_quantity_{if $price.pricedetails_id == "new"}{$detail.orderdetails_id}_{/if}{$price.pricedetails_id}"
                                                           value="{$price.price_related_quantity}"
                                                           {if !($can_prices_edit || ($can_prices_create && $price.pricedetails_id == "new"))}disabled{/if}>
                                                    <span class="input-group-addon">{t}Stk.{/t}</span>
                                                </div>

                                            </td>

                                            <td>
                                                <input type="hidden" name="pid" value="{$pid}">
                                                <!-- <input type="hidden" name="$price.pricedetails_id" value="{$price.pricedetails_id}"> -->
                                                <!-- <input type="hidden" name="$detail.orderdetails_id" value="{$detail.orderdetails_id}"> -->
                                                {if $price.pricedetails_id == "new"}
                                                    <button type="submit" class="btn btn-default" name="pricedetails_add"
                                                            {if !$can_prices_create}disabled{/if}
                                                            value="{$detail.orderdetails_id}">{t}Hinzufügen{/t}</button>
                                                {else}
                                                    <button class="btn btn-default" type="submit" name="pricedetails_apply"
                                                            {if !$can_prices_edit}disabled{/if}
                                                            value="{$price.pricedetails_id}">{t}Übernehmen{/t}</button>
                                                    <p></p>
                                                    <button class="btn btn-default" type="submit" name="pricedetails_delete"
                                                            {if !$can_prices_delete}disabled{/if}
                                                            value="{$price.pricedetails_id}">{t}Löschen{/t}</button>
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
