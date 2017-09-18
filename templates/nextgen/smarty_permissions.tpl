{locale path="nextgen/locale" domain="partdb"}

{if isset($perm_loop)}
    {foreach $perm_loop as $perm_group}

        <table class="table table-bordered table-condensed">
            <caption><h4>{$perm_group.title}</h4></caption>
            <thead>
            <tr>
                <th>{t}Berechtigung{/t}</th>
                <th>{t}Wert{/t}</th>
            </tr>
            </thead>
            <tbody>
            {foreach $perm_group.permissions as $perm}
                <tr>
                    <td style="vertical-align: middle;"><b>{$perm.description}</b></td>
                    <td>
                        {foreach $perm.ops as $op}
                            <div class="checkbox checkbox-inline">
                                <input type="checkbox" class="styled tristate" name="perm/{$perm.name}/{$op.name}"
                                        {if $op.value == 0} indeterminate="indeterminate"{elseif $op.value == 1} checked="checked"{/if}>
                                <label>{$op.description}</label>
                            </div>
                        {/foreach}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {/foreach}
{/if}
