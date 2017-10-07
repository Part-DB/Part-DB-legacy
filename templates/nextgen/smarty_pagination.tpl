<div class="row">
    <div class="col-md-12">
        <nav aria-label="Page navigation" class="pull-right">
            <ul class="pagination" style="margin-top: 0; margin-bottom: 5px;">
                <li class="disabled"><a class="no-progbar">{$pagination.lower_result}-{$pagination.upper_result}/{$pagination.max_entries}</a></li>
                {foreach $pagination.entries as $page}
                    <li {if isset($page.disabled) && $page.disabled}class="disabled" {/if}
                            {if isset($page.active) && $page.active}class="active"{/if}>
                        <a {if !isset($page.disabled) || !$page.disabled}href="{$page.href}{/if}"
                           {if isset($page.hint)}title="{$page.hint}" {/if}
                        >{$page.label nofilter}</a></li>
                {/foreach}
                <li class="disabled"><select name="limit" onchange="submitForm(this.form);">
                        <option value="25" {if $limit == 25}selected{/if}>25</option>
                        <option value="50" {if $limit == 50}selected{/if}>50</option>
                        <option value="100" {if $limit == 100}selected{/if}>100</option>
                        <option value="150" {if $limit == 150}selected{/if}>125</option>
                        <option value="200" {if $limit == 200}selected{/if}>200</option>
                        <option value="250" {if $limit == 250}selected{/if}>250</option>
                    </select></li>
            </ul>
        </nav>
    </div>
</div>