<table class="table table-hover table-bordered table-sortable table-striped table-condensed
    {if isset($log_delete) && $log_delete} table-selectable{/if} ">
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

        <tr class="
            {if $entry.level_id == 5}
                table-info
            {elseif $entry.level_id == 4}
                table-warning
            {elseif $entry.level_id <= 3}
                table-danger
            {/if}">

            {if isset($entry.id)}
                <input type="hidden" name="id_{$entry.row_index}" value="{$entry.id}">
            {/if}

            <td>{$entry.timestamp}</td>
            <td>{$entry.level}</td>
            <td>{$entry.type}</td>
            <td>{if isset($can_show_user) && $can_show_user}
                    <a href="{$relative_path}user_info.php?uid={$entry.user_id}">{$entry.user}</a>
                {else}
                    {$entry.user}
                {/if}
            </td>
            <td>{if $entry.target_link != ""}
                    <a href="{$entry.target_link}" title="{t}ID: {/t}{$entry.target_id}">{$entry.target_text}</a>
                {else}
                    {$entry.target_text}
                {/if}</td>
            <td>{$entry.comment}</td>
        </tr>
    {/foreach}
    </tbody>
</table>