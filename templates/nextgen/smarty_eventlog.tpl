<table class="table table-hover table-bordered table-sortable table-condensed">
    <thead>
        <tr>
            <th>{t}Zeitstempel{/t}</th>
            <th>{t}Level{/t}</th>
            <th>{t}Ereignis{/t}</th>
            <th>{t}Benutzer{/t}</th>
            <th>{t}Ziel{/t}</th>
            <th>{t}Kommentar{/t}</th>
        </tr>
    </thead>
    <tbody>
        {foreach $log as $entry}
            {assign color "info"}
            {if $entry.level_id == 5}
                {assign color "info"}
            {elseif $entry.level_id == 4}
                {assign color "warning"}
            {elseif $entry.level_id <= 3}
                {assign color "danger"}
            {/if}
            <tr class="info">
                <td>{$entry.timestamp}</td>
                <td>{$entry.level}</td>
                <td>{$entry.type}</td>
                <td>{$entry.user}</td>
                <td></td>
                <td>{$entry.comment}</td>
            </tr>
        {/foreach}
    </tbody>
</table>