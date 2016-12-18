{locale path="nextgen/locale" domain="partdb"}

<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;
        {t}Detailinfo zu{/t} <b>"{$name}"</b>
            <div class="pull-right">
                {t}ID:{/t} {$pid}
            </div>
        
    </div>

    <div class="panel-body">
       <div class="row">
                <div class="col-md-9">
                    <div class="form-horizontal">
                       <div class="form-group">
                           <label class="col-sm-3 control-label">{t}Name:{/t}</label>
                           <div class="col-sm-9">
                                {if isset($manufacturer_product_url)}
                                    <a class="form-control-static" title="{$manufacturer_product_url}" href="{$manufacturer_product_url}">{$name}</a>
                                {else}
                                    <p class="form-control-static">{$name}</p>
                                {/if}
                            </div>
                       </div>
                       
                       <div class="form-group">
                           <label class="col-sm-3 control-label">{t}Beschreibung:{/t}</label>
                           <div class="col-sm-9">
                               <p class="form-control-static">
                               {if isset($description)}{$description nofilter}{else}-{/if}
                               </p>
                           </div>
                       </div>
                       
                       <div class="form-group">
                           <label class="col-sm-3 control-label">{t}Vorhanden:{/t}</label>
                           <div class="col-sm-9">
                               <p class="form-control-static">{$instock}</p>
                           </div>
                       </div>
                       
                       <div class="form-group">
                           <label class="col-sm-3 control-label">{t}Min. Bestand:{/t}</label>
                           <div class="col-sm-9"><p class="form-control-static">{$mininstock}</p></div>
                       </div>
                       
                       <div class="form-group">
                           <label class="col-sm-3 control-label">{t}Kategorie:{/t}</label>
                           <div class="col-sm-9"><p class="form-control-static">{$category_full_path}</p></div>
                       </div>
                       
                       <div class="form-group">
                           <label class="col-sm-3 control-label">{t}Lagerort:{/t}</label>
                           <div class="col-sm-9">
                               <p class="form-control-static">{$storelocation_full_path}{if $storelocation_is_full} [voll]{/if}</p>
                           </div>
                       </div>
                       
                       {if !$disable_manufacturers}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{t}Hersteller:{/t}</label>
                            <div class="col-sm-9">
                                <p class="form-control-static">{$manufacturer_full_path}</p>
                            </div>
                        </div>
                       {/if}
                       
                       {if !$disable_footprints}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{t}Footprint:{/t}</label>
                            <div class="col-sm-9">
                                <p class="form-control-static">{$footprint_full_path}</p>
                            </div>
                        </div>
                        
                            {if !empty($footprint_filename) && $footprint_valid}
                            <div class="form-group">
                                <div class="col-sm-9 col-md-offset-3">
                                    <img align="middle" rel="popover" src="{$footprint_filename}" alt="" height="70">
                                </div>
                            </div>
                            {/if}
                        
                            {if $foot3d_active && !empty($foot3d_filename) && $foot3d_valid}
                            <div class="form-group">
                                <div class="col-sm-9 col-md-offset-3">
                                    <x3d id="foot3d" class="img-thumbnail" height="150" width="500" >
                                        <scene >
                                            <!-- <Viewpoint id="front" position="0 0 10" orientation="-0.01451 0.99989 0.00319 3.15833" description="camera"></Viewpoint> -->
                                            <transform>
                                                <inline url="{$foot3d_filename}"> </inline>
                                            </transform>
                                        </scene>
                                        <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#fullscreen"><i class="fa fa-arrows-alt" aria-hidden="true"></i></button>
                                    </x3d>
                                </div>
                            </div>
                            {/if}
                        
                        {/if}
                       
                       <div class="form-group">
                           <label class="col-sm-3 control-label">{t}Kommentar:{/t}</label>
                           <div class="col-sm-9">
                               <p class="form-control-static">{if !empty($comment)}{$comment nofilter}{else}-{/if}</p>
                            </div>
                       </div>
                       
                        <div class="form-group hidden-print">
                           <div class="col-sm-9 col-sm-offset-3">
                            <a class="btn btn-primary" href="edit_part_info.php?pid={$pid}">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i> {t}Angaben verändern{/t}</a>
                            </div>
                        </div>
                </div>
                
           </div>

                <div class="col-md-3">
                        <form action="" method="post" class="hidden-print">
                            <input type="hidden" name="pid" value="{$pid}">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="n_less">{t}Teile entnehmen:{/t}</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="n_less" min="0" max="999" value="1" placeholder="Anzahl" onkeypress="validatePosIntNumber(event)">
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-default" name="dec">{t}Entnehmen{/t}</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
        
                        <p></p>
                       
                        <form action="" method="post" class="hidden-print">
                            <input type="hidden" name="pid" value="{$pid}">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="n_more">{t}Teile hinzufügen{/t}</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="n_more" min="0" max="999" value="1" onkeypress="validatePosIntNumber(event)">
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-default" name="inc">{t}Hinzufügen{/t}</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <p></p>
                       
                        <form action="" method="post" class="hidden-print">
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
                                                <span class="input-group-btn">
                                                    <button type="submit" class="btn btn-default" name="mark_to_order">{t}Übernehmen{/t}</button>
                                                </span>
                                            </div>
                                        {/if}
                                    {/if}
                                </div>
                            </div>
                        </form>
                        
                        <p></p>
                        
                        <div class="form-group">
                            <label>{t}Hinzugefügt:{/t}</label>
                            <p>{$datetime_added}</p>
                        </div>
                        
                        <p></p>
                        
                        <div class="form-group">
                            <label>{t}Letzte Änderung:{/t}</label>
                            <p>{$last_modified}</p>
                        </div>
                        
                        
                </div>
        </div>
        </div>
    </div>

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
                <a class="btn btn-default pull-right hidden-print" href="edit_part_info.php?pid={$pid}">{t}Einkaufsinformationen hinzufügen{/t}</a>
            </div>
        {/if}
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-file" aria-hidden="true"></i>&nbsp;
        {t}Dateianhänge{/t}
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
            <a class="btn btn-default pull-right hidden-print" href="edit_part_info.php?pid={$pid}">{t}Dateianhänge hinzufügen{/t}</a>
        {/if}
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="fullscreen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{t}3D-Footprint{/t}</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <x3d id="foot3d" class="img-thumbnail x3d-fullscreen">
                        <scene>
                            <transform>
                                <inline url="{$foot3d_filename}"> </inline>
                            </transform>
                        </scene>
                    </x3d>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{t}Schließen{/t}</button>
            </div>
        </div>
    </div>
</div>
