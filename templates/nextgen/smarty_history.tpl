<table class="table table-hover table-bordered table-sortable table-striped table-condensed">
    <thead>
    <tr>
        <th>{t}Zeitstempel{/t}</th>
        <th>{t}Ereignis{/t}</th>
        <th>{t}Benutzer{/t}</th>
        <th>{t}Nachricht{/t}</th>
        <th>{t}Neue Anzahl der Bauteile{/t}</th>
        <th>{t}Betrag{/t}</th>
    </tr>
    </thead>
    <tbody>
    {foreach $history as $entry}
        <tr>
            <td>{$entry.timestamp_formatted}</td>
            <td>{$entry.type_text}</td>
            <td>{if isset($entry.user_link)}<a href="{$entry.user_link}">{$entry.user_name}</a>{else}{$entry.user_name}{/if}</td>
            <td>{if isset($entry.message)}{$entry.message}{/if}</td>
            <td>
                {if isset($entry.instock)}{$entry.instock}
                    {if isset($entry.difference)}
                        <span class="{if $entry.difference > 0}text-success{else}text-danger{/if}">({$entry.difference})</span>
                    {/if}
                {/if}
            </td>
            <td>{if isset($entry.price_string)}{$entry.price_string}{/if}</td>
        </tr>
    {/foreach}
    </tbody>
</table>