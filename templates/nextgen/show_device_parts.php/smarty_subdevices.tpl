{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-default">
    <div class="panel-heading">{t}Unterbaugruppen von{/t} "{$device_name}"</div>
        <table class="table table-striped table-hover">
            <thead>
                <tr class="trcat">
                    <th>{t}Name{/t}</th>
                    <th>{t}Anzahl versch. Teile{/t}</th>
                    <th>{t}Anzahl Einzelteile{/t}</th>
                    <th>{t}Gesamtpreis{/t}</th>
                </tr>
            </thead>

            <tbody>
            {foreach $subdevices as $dev}
                <!--the alternating background colors are created here-->
                <tr>
                    <td class="tdrow1">
                        <a href="show_device_parts.php?device_id={$dev.id}">{$dev.name}</a>
                    </td>
                    <td class="tdrow2">
                        {$dev.parts_count}
                    </td>

                    <td class="tdrow3">
                        {$dev.parts_sum_count}
                    </td>

                    <td class="tdrow3">
                        {$dev.sum_price}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    <div class="panel-body">
        <b>{t}Alle Angaben betreffen nur die jeweilige Baugruppe, deren evtl. vorhandenen Unterbaugruppen werden nicht berücksichtigt!{/t}</b>
    </div>
</div>
