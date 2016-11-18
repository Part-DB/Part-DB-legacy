<script language="JavaScript">
    function toggle_all_checkboxes(source, prefix)
    {
        for(var i=0, n={TMPL_VAR NAME="table_rowcount"};i<n;i++)
        {
            var elements = document.getElementsByName(prefix + i);
            if (elements.length > 0)
                elements[0].checked = source.checked;
        }
    }
</script>

<div class="panel panel-default">
    <h4>Lieferant wählen</h2>
    <div class="panel-body">
        <form method="get" action="" style="display:inline">
            <select name="selected_supplier_id">
                <option value="0" {TMPL_IF NAME="selected_supplier_id" VALUE="0"}selected{/TMPL_IF}>Alle</option>
                {TMPL_IF NAME="suppliers"}
                    {TMPL_LOOP NAME="suppliers"}
                        <option value="{TMPL_VAR NAME="id"}" {TMPL_IF NAME="selected"}selected{/TMPL_IF}>{TMPL_VAR NAME="full_path"} ({TMPL_VAR NAME="count_of_parts"})</option>
                    {/TMPL_LOOP}
                {/TMPL_IF}
            </select>

            <input type="submit" name="choose_selected_supplier" value="Übernehmen">
        </form>
        <form method="get" action="" style="display:inline">
            <input type="hidden" name="selected_supplier_id" value="0">
            <input type="submit" name="choose_selected_supplier" value="Alle anzeigen">
        </form>
    </div>
</div>

<div class="outer">
    <h2>Zu bestellende Teile</h2>
    <div class="inner">
        <form method="post" action="">
            {TMPL_IF NAME="table_rowcount"}
                <input type="hidden" name="table_rowcount" value="{TMPL_VAR NAME="table_rowcount"}">
                <table>
                    {TMPL_INCLUDE FILE="../vlib_table.tmpl"}
                </table>
                <br>
                <b>Gesamtpreis: {TMPL_VAR NAME="sum_price"}</b><br>
                <br>
                <input type="hidden" name="selected_supplier_id" value="{TMPL_VAR NAME="selected_supplier_id"}">

                Alle an/abwählen:&nbsp;&nbsp;
                <input type="checkbox" onClick="toggle_all_checkboxes(this, 'tostock_')"/>Einbuchen &nbsp;&nbsp;&nbsp;
                <input type="checkbox" onClick="toggle_all_checkboxes(this, 'remove_')"/>Aus Liste löschen (für manuell zum Bestellen markierte Artikel)
                <br>
                <input type="submit" name="apply_changes" value="Änderungen übernehmen">
                <input type="submit" name="abort" value="Änderungen verwerfen">
                <input type="submit" name="autoset_quantities" value="Bestellmengen automatisch setzen">
            {TMPL_ELSE}
                Es gibt keine Teile, die bestellt werden müssen.
            {/TMPL_IF}
        </form>
    </div>
</div>

{TMPL_IF NAME="order_devices_loop"}
{TMPL_UNLESS NAME="selected_supplier_id"}
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

            {TMPL_LOOP NAME="order_devices_loop"}
                <!--the alternating background colors are created here-->
                <tr class="{TMPL_IF NAME="row_odd"}trlist_odd{TMPL_ELSE}trlist_even{/TMPL_IF}">
                    <td class="tdrow1">
                        <a title="{TMPL_VAR NAME="full_path"}" href="show_device_parts.php?device_id={TMPL_VAR NAME="id"}">{TMPL_VAR NAME="name"}</a>
                    </td>

                    <td class="tdrow2">
                        {TMPL_VAR NAME="order_quantity"}
                    </td>

                    <td class="tdrow2">
                        {TMPL_IF NAME="only_missing_parts"}nur fehlende{TMPL_ELSE}alle{/TMPL_IF}
                    </td>

                    <td class="tdrow2">
                        {TMPL_VAR NAME="parts_count"}
                    </td>

                    <td class="tdrow3">
                        {TMPL_VAR NAME="parts_count_to_order"}
                    </td>

                    <td class="tdrow3">
                        <form method="post" action="">
                            <input type="hidden" name="selected_supplier_id" value="{TMPL_VAR NAME="selected_supplier_id"}">
                            <input type="hidden" name="device_id" value="{TMPL_VAR NAME="id"}">
                            <input type="submit" name="remove_device" value="Entfernen">
                        </form>
                    </td>
                </tr>
            {/TMPL_LOOP}
        </table>
        <i>Die zu bestellenden Teile werden in der Liste "Zu bestellende Teile" in der Spalte "Bestellmenge" zur Anzahl "(mind. ...)" hinzuaddiert.
        <u>Nachdem</u> Sie die Bestellungen getätigt haben, können Sie die Baugruppen wieder aus dieser Liste entfernen,
        damit die Teile nicht mehr unter "Zu bestellende Teile" aufgelistet werden (Bauteile von Baugruppen, die als "alle Teile bestellen" markiert wurden,
        verschwinden <u>nicht</u> automatisch aus der Liste "Zu bestellende Teile", auch wenn Sie den Lagerbestand erhöhen!).</i>
    </div>
</div>
{/TMPL_UNLESS}
{/TMPL_IF}

<div class="outer">
    <h2>Bauteile Export</h2>
    <div class="inner">
        <form method="post" action="">
            <b>Format:</b>
            <select name="export_format">
                {TMPL_LOOP NAME = "export_formats"}
                    <option value="{TMPL_VAR NAME="value"}" {TMPL_IF NAME="selected"}selected{/TMPL_IF}>{TMPL_VAR NAME="text"}</option>
                {/TMPL_LOOP}
            </select>
            <input type="submit" name="export_show" value="Anzeigen">
            <input type="submit" name="export_download" value="Herunterladen">
        </form>
        {TMPL_IF NAME="export_result"}
            <hr>{TMPL_VAR NAME="export_result" ESCAPE="none"}
        {/TMPL_IF}
    </div>
</div>
