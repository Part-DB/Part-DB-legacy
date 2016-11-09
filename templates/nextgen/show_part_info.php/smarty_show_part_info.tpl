<div class="panel panel-primary">
   <div class="panel-heading">
       <h4>Detailinfo zu <b>"{$name}"</b>
            <div style="float: right; display: inline;">
                ID: {$pid}
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
                                {if isset($manufacturer_product_url)}
                                    <a title="{$manufacturer_product_url}" href="{$manufacturer_product_url}" target="_blank">{$name}</a>
                                {else}
                                    {$name}
                                {/if}
                            </td>
                       </tr>
                       <tr>
                           <td><b>Beschreibung:</b></td>
                           <td>{if isset($description)}{$description}{else}-{/if}</td>
                       </tr>
                       <tr>
                           <td><b>Vorhanden:</b></td>
                           <td>{$instock}</td>
                       </tr>
                       <tr>
                           <td><b>Min. Bestand:</b></td>
                           <td>{$mininstock}</td>
                       </tr>
                       <tr>
                           <td><b>Kategorie:</b></td>
                           <td>{$category_full_path}</td>
                       </tr>
                       <tr>
                           <td><b>Lagerort:</b></td>
                           <td>{$storelocation_full_path}{if $storelocation_is_full} [voll]{/if}</td>
                       </tr>
                       {if !$disable_manufacturers}
                           <tr>
                               <td><b>Hersteller:</b></td>
                               <td>{$manufacturer_full_path}</td>
                           </tr>
                       {/if}
                       {if !$disable_footprints}
                           <tr>
                               <td><b>Footprint:</b></td>
                               <td>
                                   {$footprint_full_path}<br>
                                   {if isset($footprint_filename)}<img align="middle" src="{$footprint_filename}" alt="" height="70">{/if}
                               </td>
                           </tr>
                       {/if}
                       <tr>
                           <td valign="top"><b>Kommentar:</b></td>
                           <td>{if isset($comment)}{$comment}{else}-{/if}</td>
                       </tr>
                    </table>
                    
                    <a class="btn btn-primary" href="edit_part_info.php?pid={$pid}">Angaben verändern</a>
                </div>

                <div class="col-md-3">
                        <form action="" method="post">
                            <input type="hidden" name="pid" value="{$pid}">
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
                            <input type="hidden" name="pid" value="{$pid}">
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
                            <input type="hidden" name="pid" value="{$pid}">
                            <div class="row">
                                <div class="col-md-12">
                                    {if $manual_order_exists}
                                        <label for="remove_mark_to_order">Bauteil wurde manuell zum Bestellen vorgemerkt.</label>  
                                        <button type="submit" class="btn btn-default" name="remove_mark_to_order">Aufheben</button>
                                    {else}
                                        {if $auto_order_exists}
                                            <i>Das Bauteil wird unter "Zu bestellende Teile"aufgelistet, da der Bestand kleiner als der Mindestbestand ist.</i>
                                        {else}
                                            <label for="order_quantity">Zum Bestellen vormerken:</label>
                                            <div class="input-group">
                                                <input type="number" min="0" max="999" class="form-control" value="1" name="order_quantity" placeholder="Bestellmenge" onkeypress="validatePosIntNumber(event)"><br>
                                                <div class="input-group-btn">
                                                    <button type="submit" class="btn btn-default" name="mark_to_order">Übernehmen</button>
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

<div class="panel panel-info">
    <div class="panel-heading">
        <h4>Dateianhänge</h4>
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
            <p>Dieses Bauteil besitzt keine Dateianhänge.</p>
            <p><a class="btn btn-default" href="edit_part_info.php?pid={$pid}">Dateianhänge hinzufügen</a></p>
        {/if}
    </div>
</div>
