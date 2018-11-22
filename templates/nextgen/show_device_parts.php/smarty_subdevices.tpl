{locale path="nextgen/locale" domain="partdb"}
<div class="card">
    <div class="card-header"><i class="fas fa-archive fa-fw"></i> {t}Unterbaugruppen von{/t} "{$device_name}"</div>
    <form method="post" class="no-progbar">
        <table class="table table-striped table-hover table-condensed vertical-align-table">
            <thead>
                <tr class="">
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
                    <td>
                        <div class="form-check form-check-inline abc-radio">
                            <input class="form-check-input" type="radio" name="primary_device" value="{$dev.id}"
                                   onchange="submitForm($(this).closest('form'));" {if $dev.is_primary}checked{/if}>
                            <label class="form-check-label"><a href="show_device_parts.php?device_id={$dev.id}">{$dev.name}</a></label>
                        </div>
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
    </form>
    <div class="card-body" style="padding-top: 0;">
        <b>{t}Alle Angaben betreffen nur die jeweilige Baugruppe, deren evtl. vorhandenen Unterbaugruppen werden nicht berücksichtigt!{/t}</b>
        <p>{t}Mit den Radiobuttons lassen sich die Primäre Baugruppe auswählen. Diese wird standardmäßig verwendet, wenn auf der Übersichtsseite eines Bauteils, das Bauteil einer Baugruppe hinzugefügt wird.{/t}</p>
    </div>
</div>
