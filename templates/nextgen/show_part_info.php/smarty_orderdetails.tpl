{locale path="nextgen/locale" domain="partdb"}

<div class="card mt-3">
    <div class="card-header">
        <a data-toggle="collapse" class="link-collapse text-default" href="#panel-orderdetails"><i class="fa fa-shopping-cart fa-fw" aria-hidden="true"></i>
            {t}Einkaufsinformationen{/t}
        </a>
    </div>
    {if isset($orderdetails) && $orderdetails}
        <div class="card-collapse collapse in" id="panel-orderdetails">
            <div class="table-responsive " >
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
                                <a href="{$relative_path}show_supplier_parts.php?sid={$order.supplier_id}&subsup=0">{$order.supplier_full_path}</a>
                            </td>

                            <td class="tdrow1{if $order.obsolete} backred{/if}">
                                {if isset($order.supplier_product_url) && !empty($order.supplier_product_url)}
                                    <a title="{$order.supplier_product_url}" rel="noopener" target="_blank" class="hidden-print-href link-external" href="{$order.supplier_product_url}">{$order.supplierpartnr}</a>
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
                <div class="panel-body" style="padding-top: 0;">
                    <b>{t}Durchschnittspreis für 1 Stk.:{/t} {$average_price}</b>
                </div>
            {/if}
        </div>

    {else}
        <div class="card-body card-collapse collapse in" id="panel-orderdetails">
            <!-- This a have not to have link-anchor class -->
            <span class="form-text text-muted" style="display: inline;">{t}Dieses Bauteil hat keine Einkaufsinformationen.{/t}</span>
            <a class="btn btn-secondary float-right hidden-print"
               href="edit_part_info.php?pid={$pid}#orderdetails"
               {if !$can_orderdetails_create}disabled{/if}>
                {t}Einkaufsinformationen hinzufügen{/t}</a>
        </div>
    {/if}
</div>