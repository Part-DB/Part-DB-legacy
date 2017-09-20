{locale path="nextgen/locale" domain="partdb"}

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i>&nbsp;
        {t}Einkaufsinformationen{/t}
    </div>
    {if isset($orderdetails) && $orderdetails}
        <div class="table-responsive">
            <table class="table table-striped table-header">
                <thead>
                <tr class="trcat">
                    <th>{t}Lieferant{/t}</th>
                    <th>{t}Bestellnummer{/t}</th>
                    <th>{t}Ab Bestellmenge{/t}</th>
                    <th>{t}Preis{/t}</th>
                    <th>{t}Einzelpreis{/t}</th>
                </tr>
                </thead>

                <tbody>
                {foreach $orderdetails as $order}
                    <!--the alternating background colors are created here-->
                    <tr class="">
                        <td class="tdrow1{if $order.obsolete} backred{/if}">
                            {$order.supplier_full_path}
                        </td>

                        <td class="tdrow1{if $order.obsolete} backred{/if}">
                            {if isset($order.supplier_product_url) && !empty($order.supplier_product_url)}
                                <a title="{$order.supplier_product_url}" target="_blank" class="hidden-print-href link-external" href="{$order.supplier_product_url}">{$order.supplierpartnr}</a>
                            {else}
                                {$order.supplierpartnr}
                            {/if}
                        </td>

                        <td class="tdrow2{if $order.obsolete} backred{/if}">
                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                {foreach $order.pricedetails as $price}
                                    <tr>
                                        <td class="tdrow2">
                                            {$price.min_discount_quantity}
                                        </td>
                                    </tr>
                                {/foreach}
                            </table>
                        </td>

                        <td class="tdrow1{if $order.obsolete} backred{/if}">
                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                {foreach $order.pricedetails as $price}
                                    <tr>
                                        <td class="tdrow2">
                                            {$price.price} / {$price.price_related_quantity}Stk.
                                        </td>
                                    </tr>
                                {/foreach}
                            </table>
                        </td>

                        <td class="tdrow2{if $order.obsolete} backred{/if}">
                            <table>
                                {foreach $order.pricedetails as $price}
                                    <tr>
                                        <td class="tdrow2">
                                            {$price.single_price}
                                        </td>
                                    </tr>
                                {/foreach}
                            </table>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        {if isset($average_price)}
            <div class="panel-body">
                <b>{t}Durchschnittspreis für 1 Stk.:{/t} {$average_price}</b>
            </div>
        {/if}

    {else}
        <div class="panel-body">
            {t}Dieses Bauteil hat keine Einkaufsinformationen.{/t}
            <a class="btn btn-default pull-right hidden-print link-anchor"
               href="edit_part_info.php?pid={$pid}#orderdetails"
               {if !$can_orderdetails_create}disabled{/if}>
                {t}Einkaufsinformationen hinzufügen{/t}</a>
        </div>
    {/if}
</div>