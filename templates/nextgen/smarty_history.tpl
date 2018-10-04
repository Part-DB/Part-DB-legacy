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
            <td>{$entry.user_name}</td>
            <td>{if isset($entry.message)}{$entry.message}{/if}</td>
            <td>{if isset($entry.instock)}{$entry.instock}{/if}</td>
            <td>{if isset($entry.price)}{$entry.price}{/if}</td>
        </tr>
    {/foreach}
    </tbody>
</table>