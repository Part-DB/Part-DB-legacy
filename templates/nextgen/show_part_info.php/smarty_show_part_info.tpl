{locale path="nextgen/locale" domain="partdb"}

  <div class="panel panel-primary">
   <div class="panel-heading">
       <h4>{t}Detailinfo zu{/t} <b>"{$name}"</b>
            <div style="float: right; display: inline;">
                {t}ID:{/t} {$pid}
            </div>
        </h4>
    </div>

    <div class="panel-body">
       <div class="row">
                <div class="col-md-9">
                    <!--<div class="form-horizontal">-->
                       <div class="row">
                           <label class="col-sm-2">{t}Name:{/t}</label>
                           <span class="col-sm-10">
                                {if isset($manufacturer_product_url)}
                                    <a title="{$manufacturer_product_url}" href="{$manufacturer_product_url}" target="_blank">{$name}</a>
                                {else}
                                    {$name}
                                {/if}
                            </span>
                       </div>
                       <div class="row">
                           <label class="col-sm-2">{t}Beschreibung:{/t}</label>
                           <span class="col-sm-10">{if isset($description)}{$description}{else}-{/if}</span>
                       </div>
                       <div class="row">
                           <label class="col-sm-2">{t}Vorhanden:{/t}</label>
                           <span class="col-sm-10">{$instock}</span>
                       </div>
                       <div class="row">
                           <label class="col-sm-2">{t}Min. Bestand:{/t}</label>
                           <span class="col-sm-10">{$mininstock}</span>
                       </div>
                       <div class="row">
                           <label class="col-sm-2">{t}Kategorie:{/t}</label>
                           <span class="col-sm-10">{$category_full_path}</span>
                       </div>
                       <div class="row">
                           <label class="col-sm-2">{t}Lagerort:{/t}</label>
                           <span class="col-sm-10">{$storelocation_full_path}{if $storelocation_is_full} [voll]{/if}</span>
                       </div>
                       {if !$disable_manufacturers}
                        <div class="row">
                            <label class="col-sm-2">{t}Hersteller:{/t}</label>
                            <span class="col-sm-10">{$manufacturer_full_path}</span>
                        </div>
                       {/if}
                       {if !$disable_footprints}
                        <div class="row">
                            <label class="col-sm-2">{t}Footprint:{/t}</label>
                            <div class="col-sm-10">
                               {$footprint_full_path}<br>
                                {if isset($footprint_filename)}<img align="middle" src="{$footprint_filename}" alt="" height="70">{/if}
                            </div>
                        </div>
                       {/if}
                       <div class="row">
                           <label class="col-sm-2">{t}Kommentar:{/t}</label>
                           <div class="col-sm-10">{if isset($comment)}{$comment}{else}-{/if}</div>
                       </div>
                    
                      <hr>
                       
                        <div class="row">
                           <div class="col-sm-12">
                            <a class="btn btn-primary" href="edit_part_info.php?pid={$pid}">Angaben verändern</a>
                            </div>
                        </div>
                </div>

                <div class="col-md-3">
                        <form action="" method="post">
                            <input type="hidden" name="pid" value="{$pid}">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="n_less">{t}Teile entnehmen:{/t}</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="n_less" min="0" max="999" value="1" placeholder="Anzahl" onkeypress="validatePosIntNumber(event)">
                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default" name="dec">{t}Entnehmen{/t}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
        
                        <p></p>
                       
                        <form action="" method="post">
                            <input type="hidden" name="pid" value="{$pid}">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="n_more">{t}Teile hinzufügen{/t}</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="n_more" min="0" max="999" value="1" onkeypress="validatePosIntNumber(event)">
                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default" name="inc">{t}Hinzufügen{/t}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <p></p>
                       
                        <form action="" method="post">
                            <input type="hidden" name="pid" value="{$pid}">
                            <div class="row">
                                <div class="col-md-12">
                                    {if $manual_order_exists}
                                        <label for="remove_mark_to_order">{t}Bauteil wurde manuell zum Bestellen vorgemerkt.{/t}</label>  
                                        <button type="submit" class="btn btn-default" name="remove_mark_to_order">{t}Aufheben{/t}</button>
                                    {else}
                                        {if $auto_order_exists}
                                            <i>{t}Das Bauteil wird unter "Zu bestellende Teile"aufgelistet, da der Bestand kleiner als der Mindestbestand ist.{/t}</i>
                                        {else}
                                            <label for="order_quantity">{t}Zum Bestellen vormerken:{/t}</label>
                                            <div class="input-group">
                                                <input type="number" min="0" max="999" class="form-control" value="1" name="order_quantity" placeholder="Bestellmenge" onkeypress="validatePosIntNumber(event)"><br>
                                                <div class="input-group-btn">
                                                    <button type="submit" class="btn btn-default" name="mark_to_order">{t}Übernehmen{/t}</button>
                                                </div>
                                            </div>
                                        {/if}
                                    {/if}
                                </div>
                            </div>
                        </form>
                </div>
            </tr>
        </div>
        </div>
    </div>

<div class="panel panel-default">
    <div class="panel-heading"><h4>{t}Einkaufsinformationen{/t}</h4></div>
            {if isset($orderdetails) && $orderdetails}
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
                                {if isset($order.supplier_product_url)}
                                    <a title="{$order.supplier_product_url}" href="{$order.supplier_product_url}">{$order.supplierpartnr}</a>
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
                                <table border="0" cellspacing="0" cellpadding="0" width="100%">
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
            {if isset($average_price)}
                <b>{t}Durchschnittspreis für 1 Stk.:{/t} {$average_price}</b>
            {/if}
        {else}
            <div class="panel-body">
                {t}Dieses Bauteil hat keine Einkaufsinformationen.{/t}
                <a class="btn btn-default pull-right" href="edit_part_info.php?pid={$pid}">{t}Einkaufsinformationen hinzufügen{/t}</a>
            </div>
        {/if}
</div>

<div class="panel panel-info">
    <div class="panel-heading">
        <h4>{t}Dateianhänge{/t}</h4>
    </div>
    <div class="panel-body">
        {if isset($attachement_types_loop)}
            {foreach $attachement_types_loop as $attach_type}
                <b>{$attach_type.attachement_type}:</b><br>
                {foreach $attach_type.attachements_loop as $attach}
                    {if $attach.is_picture}
                        <a href="javascript:popUp('{$attach.filename}', {if $use_modal_popup}true{else}false{/if},
                                                    {$popup_width}, {$popup_height});">
                        <img src="{$attach.filename}" alt="Zum Vergrößern klicken!" style="max-height:180px; max-width:180px"></a>
                    {else}
                        <a target="_blank" href="{$attach.filename}">{$attach.attachement_name}</a><br>
                    {/if}
                {/foreach}
            {/foreach}
        {else}
            {t}Dieses Bauteil besitzt keine Dateianhänge.{/t}
            <a class="btn btn-default pull-right" href="edit_part_info.php?pid={$pid}">{t}Dateianhänge hinzufügen{/t}</a>
        {/if}
    </div>
</div>
