{locale path="nextgen/locale" domain="partdb"}

{if isset($perm_loop)}

    <div class="">
        <label>{t}Erläuterung der Zustände:{/t}</label>
        <div>
            <div class="checkbox checkbox-inline">
                <input type="checkbox" hidden>
                <label>{t}Verboten{/t}</label>
            </div>
            <div class="checkbox checkbox-inline">
                <input type="checkbox" hidden checked>
                <label>{t}Erlaubt{/t}</label>
            </div>
            <div class="checkbox checkbox-inline">
                <input type="checkbox" class="tristate" hidden indeterminate="indeterminate">
                <label>{t}Erbe von (übergeordneter) Gruppe{/t}</label>
            </div>
        </div>
    </div>

    <br>

    <ul class="nav nav-pills">
        {foreach from=$perm_loop item=perm_group key=n}
            <li {if $n==0}class="active"{/if}><a data-toggle="pill" class="link-anchor" href="#perm_tab_{$n}">{$perm_group.title}</a></li>
        {/foreach}
    </ul>

    <div class="tab-content">
        {foreach from=$perm_loop item=perm_group key=n}
            <div id="perm_tab_{$n}" class="tab-pane fade {if $n==0}in active{/if}">
                <br>
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th>{t}Berechtigung{/t}</th>
                        <th>{t}Wert{/t}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach $perm_group.permissions as $perm}
                        <tr>
                            <td style="vertical-align: middle;">
                                {if $perm.readonly}
                                    <b>{$perm.description}</b>
                                {else}
                                    <div class="checkbox checkbox-inline">
                                        <input type="checkbox" class="tristate-toggle-all" data-target="{$perm.name}">
                                        <label><b>{$perm.description}</b></label>
                                    </div>
                                {/if}

                            </td>
                            <td>
                                {foreach from=$perm.ops  item=op key=m}
                                    <div class="checkbox checkbox-inline"
                                         {if $m==0}style="margin-left: 10px;"{/if}>
                                        <input type="checkbox" class="styled tristate" name="perm/{$perm.name}/{$op.name}"
                                                {if $op.value == 0} indeterminate="indeterminate"{elseif $op.value == 1} checked="checked"{/if}
                                                {if $perm.readonly}disabled{/if}>
                                        <label>{$op.description}</label>
                                    </div>
                                {/foreach}
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        {/foreach}
    </div>

    <script>
        $("input.tristate-toggle-all").tristate({
            change: function (state, value) {
                var $this = $(this);
                var target = $this.data('target');
                var state = $this.tristate('state');
                $("input.tristate[name^='perm/" + target + "/']").tristate('state', state);
            }
        });

    
        function toggleAllPermCheckboxes(element) {
            var $this = $(element);
            var state = $this.tristate('state');
            var target = $this.data('target');
            $("input.tristate[name^='perm/" + target + "/']").tristate('state', state);
        }
    </script>


{/if}
