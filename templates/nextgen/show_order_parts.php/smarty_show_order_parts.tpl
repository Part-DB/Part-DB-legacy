{locale path="nextgen/locale" domain="partdb"}
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
    <div class="panel-heading">{t}Lieferant wählen{/t}</div>
    <div class="panel-body">
        <form method="get" action="" class="form-horizontal">
            <div class="form-group col-md-12">
                <div class="input-group">
                <select class="form-control" name="selected_supplier_id">
                    <option value="0" {if !isset($selected_supplier_id) || $selected_supplier_id == 0}selected{/if}>{t}Alle{/t}</option>
                    {if isset($suppliers)}
                        {foreach $suppliers as $sup}
                            <option value="{$sup.id}" {if $sup.selected}selected{/if}>{$sup.full_path} ({$sup.count_of_parts})</option>
                        {/foreach}
                    {/if}
                </select>
            
                 <span class="input-group-btn">
                    <button type="submit" class="btn btn-success" name="choose_selected_supplier">{t}Übernehmen{/t}</button>
                </span>
                </div>
            </div>
        </form>
        
        <form method="get" action="" class="form-horizontal">
            <div class="form-group">
                <input type="hidden" name="selected_supplier_id" value="0">
                <div class="col-md-12">
                    <button class="btn btn-primary" type="submit" name="choose_selected_supplier">{t}Alle anzeigen{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><b>{if isset($table_rowcount)}{$table_rowcount}{else}0{/if}</b> {t}Zu bestellende Teile{/t}</div>
    <div class="panel-body">
        <form method="post" action="" class="no-progbar">
            {if isset($table_rowcount) && $table_rowcount > 0}
                <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
                <div class="row">
                    {include "../smarty_table.tpl"}
                </div>
                
                <hr>
                
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-md-3">{t}Gesamtpreis:{/t}</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{$sum_price}</p>
                        </div>
                        <input type="hidden" name="selected_supplier_id" value="{$selected_supplier_id}">
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3">{t}Alle an/abwählen:{/t}</label>
                        <div class="col-md-9">
                            <div class="checkbox">
                                <input type="checkbox" onClick="toggle_all_checkboxes(this, 'tostock_')"/>
                                <label>{t}Einbuchen{/t}</label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" onClick="toggle_all_checkboxes(this, 'remove_')"/>
                                <label>{t}Aus Liste löschen (für manuell zum Bestellen markierte Artikel){/t}</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <button type="submit" class="btn btn-success" name="apply_changes">{t}Änderungen übernehmen{/t}</button>
                            <button type="submit" class="btn btn-danger" name="abort">{t}Änderungen verwerfen{/t}</button>
                        </div>
                    </div>
    
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <button type="submit" class="btn btn-default" name="autoset_quantities">{t}Bestellmengen automatisch setzen{/t}</button>
                        </div>
                    </div>              
                </div>
            {else}
                {t}Es gibt keine Teile, die bestellt werden müssen.{/t}
            {/if}
            
        </form>
    </div>
</div>

{if $order_devices_loop}
{if !$selected_supplier_id}
<div class="panel panel-default">
    <div class="panel-heading">{t}Zum Bestellen markierte Baugruppen{/t}</div>
    <div class="panel-body">
        <table>
            <tr class="trcat">
                <td>{t}Name{/t}</td>
                <td>{t}Bestellmenge{/t}</td>
                <td>{t}Teile{/t}</td>
                <td>{t}Anzahl versch. Teile{/t}</td>
                <td>{t}Davon zu wenige an Lager{/t}</td>
                <td>{t}Optionen{/t}</td>
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
                        <form method="post" action="" class="no-progbar">
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
    <div class="panel-heading">{t}Bauteile Export{/t}</div>
    <div class="panel-body">
        <form method="post" action="" class="form-horizontal no-progbar">
            <div class="form-group">
                <label class="control-label col-md-3">{t}Format:{/t}</label>
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
                    <button class="btn btn-default" type="submit" name="export_show">{t}Anzeige{/t}</button>
                    <button class="btn btn-default" type="submit" name="export_download">{t}Herunterladen{/t}</button>
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
