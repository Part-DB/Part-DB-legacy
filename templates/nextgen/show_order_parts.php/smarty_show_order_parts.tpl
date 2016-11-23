<script language="JavaScript">
    function toggle_all_checkboxes(source, prefix)
    {
        for(var i=0, n={$table_rowcount};i<n;i++)
        {
            var elements = document.getElementsByName(prefix + i);
            if (elements.length > 0)
                elements[0].checked = source.checked;
        }
    }
</script>

<div class="panel panel-primary">
    <div class="panel-heading"><h4>Lieferant wählen</h4></div>
    <div class="panel-body">
        <form method="get" action="" class="form-horizontal">
            <div class="form-group col-md-12">
                <div class="input-group">
                <select class="form-control" name="selected_supplier_id">
                    <option value="0" {if !isset($selected_supplier_id) || $selected_supplier_id == 0}selected{/if}>Alle</option>
                    {if isset($suppliers)}
                        {foreach $suppliers as $sup}
                            <option value="{$sup.id}" {if $sup.selected}selected{/if}>{$sup.full_path} ({$sup.count_of_parts})</option>
                        {/foreach}
                    {/if}
                </select>
            
                 <span class="input-group-btn">
                    <button type="submit" class="btn btn-success" name="choose_selected_supplier">Übernehmen</button>
                </span>
                </div>
            </div>
        </form>
        
        <form method="get" action="" class="form-horizontal">
            <div class="form-group">
                <input type="hidden" name="selected_supplier_id" value="0">
                <div class="col-md-12">
                    <button class="btn btn-primary" type="submit" name="choose_selected_supplier">Alle anzeigen</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><h4>Zu bestellende Teile</h4></div>
    <div class="panel-body">
        <form method="post" action="">
            {if isset($table_rowcount) && $table_rowcount > 0}
                <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
                <div class="row">
                    {include "../smarty_table.tpl"}
                </div>
                
                <hr>
                
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-md-3">Gesamtpreis:</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{$sum_price}</p>
                        </div>
                        <input type="hidden" name="selected_supplier_id" value="{$selected_supplier_id}">
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3">Alle an/abwählen:</label>
                        <div class="col-md-9">
                            <div class="checkbox">
                                <input type="checkbox" onClick="toggle_all_checkboxes(this, 'tostock_')"/>
                                <label>Einbuchen</label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" onClick="toggle_all_checkboxes(this, 'remove_')"/>
                                <label>Aus Liste löschen (für manuell zum Bestellen markierte Artikel)</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <button type="submit" class="btn btn-success" name="apply_changes">Änderungen übernehmen</button>
                            <button type="submit" class="btn btn-danger" name="abort">Änderungen verwerfen</button>
                        </div>
                    </div>
    
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <button type="submit" class="btn btn-default" name="autoset_quantities">Bestellmengen automatisch setzen</button>
                        </div>
                    </div>              
                </div>
            {else}
                Es gibt keine Teile, die bestellt werden müssen.
            {/if}
            
        </form>
    </div>
</div>

{if $order_devices_loop}
{if !$selected_supplier_id}
<div class="outer">
    <h2>Zum Bestellen markierte Baugruppen</h2>
    <div class="inner">
        <table>
            <tr class="trcat">
                <td>Name</td>
                <td>Bestellmenge</td>
                <td>Teile</td>
                <td>Anzahl versch. Teile</td>
                <td>Davon zu wenige an Lager</td>
                <td>Optionen</td>
            </tr>

            {foreach $order_devices_loop as $dev}
                <!--the alternating background colors are created here-->
                <tr>
                    <td class="tdrow1">
                        <a title="{if isset($dev.full_path)}" href="show_device_parts.php?device_id={$dev.id}">{$dev.name}</a>
                    </td>

                    <td class="tdrow2">
                        {$dev.order_quantity}
                    </td>

                    <td class="tdrow2">
                        {$dev.only_missing_parts}nur fehlende{else}alle{/if}
                    </td>

                    <td class="tdrow2">
                        {$dev.parts_count}
                    </td>

                    <td class="tdrow3">
                        {$dev.parts_count_to_order}
                    </td>

                    <td class="tdrow3">
                        <form method="post" action="">
                            <input type="hidden" name="selected_supplier_id" value="{$dev.selected_supplier_id}">
                            <input type="hidden" name="device_id" value="{$dev.id}">
                            <input type="submit" name="remove_device" value="Entfernen">
                        </form>
                    </td>
                </tr>
            {/foreach}
        </table>
        <i>Die zu bestellenden Teile werden in der Liste "Zu bestellende Teile" in der Spalte "Bestellmenge" zur Anzahl "(mind. ...)" hinzuaddiert.
        <u>Nachdem</u> Sie die Bestellungen getätigt haben, können Sie die Baugruppen wieder aus dieser Liste entfernen,
        damit die Teile nicht mehr unter "Zu bestellende Teile" aufgelistet werden (Bauteile von Baugruppen, die als "alle Teile bestellen" markiert wurden,
        verschwinden <u>nicht</u> automatisch aus der Liste "Zu bestellende Teile", auch wenn Sie den Lagerbestand erhöhen!).</i>
    </div>
</div>
{/if}
{/if}

<div class="panel panel-default">
    <div class="panel-heading"><h4>Bauteile Export</h4></div>
    <div class="panel-body">
        <form method="post" action="" class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-md-3">Format:</label>
                <div class="col-md-9"> 
                    <select name="export_format" class="form-control">
                        {foreach $export_formats as $format}
                        <option value="{$format.value}" {if $format.selected}selected{/if}>{$format.text}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                    <button class="btn btn-default" type="submit" name="export_show">Anzeige</button>
                    <button class="btn btn-default" type="submit" name="export_download">Herunterladen</button>
                </div>
            </div>
        </form>
        {if isset($export_result) && !empty($export_result)}
        <hr>
        <code>
            {$export_result}
        </code>
        {/if}
    </div>
</div>
