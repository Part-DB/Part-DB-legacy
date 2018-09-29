{locale path="nextgen/locale" domain="partdb"}

{if isset($perm_loop)}

    <div class="">
        <label>{t}Erläuterung der Zustände:{/t}</label>
        <div>
            <div class="form-check form-check-inline abc-checkbox ml-3 mr-4">
                <input type="checkbox" hidden class="form-check-input">
                <label class="form-check-label">{t}Verboten{/t}</label>
            </div>
            <div class="form-check form-check-inline abc-checkbox mr-4">
                <input class="form-check-input" type="checkbox" hidden checked>
                <label class="form-check-label">{t}Erlaubt{/t}</label>
            </div>
            <div class="form-check form-check-inline abc-checkbox">
                <input type="checkbox" class="tristate form-check-input" hidden indeterminate="indeterminate">
                <label class="form-check-label">{t}Erbe von (übergeordneter) Gruppe{/t}</label>
            </div>
        </div>
    </div>

    <br>

    <ul class="nav nav-pills">
        {foreach from=$perm_loop item=perm_group key=n}
            <li class=" nav-item"><a data-toggle="pill" class="link-anchor nav-link {if $n==0}active{/if}" href="#perm_tab_{$n}">{$perm_group.title}</a></li>
        {/foreach}
    </ul>

    <div class="tab-content">
        {foreach from=$perm_loop item=perm_group key=n}
            <div id="perm_tab_{$n}" class="tab-pane fade {if $n==0}in show active{/if}">
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
                                    <div class="form-check form-check-inline abc-checkbox">
                                        <input type="checkbox" class="tristate-toggle-all form-check-input" data-target="{$perm.name}">
                                        <label class="form-check-label"><b>{$perm.description}</b></label>
                                    </div>
                                {/if}

                            </td>
                            <td>
                                {foreach from=$perm.ops  item=op key=m}
                                    <div class="form-check form-check-inline abc-checkbox">
                                        <input type="checkbox" class="styled tristate form-check-input" name="perm/{$perm.name}/{$op.name}"
                                                {if $op.value == 0} indeterminate="indeterminate"{elseif $op.value == 1} checked="checked"{/if}
                                                {if $perm.readonly}disabled{/if}>
                                        <label class="form-check-label">{$op.description}</label>
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
            change: function () {
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
