<div class="row">
    <div class="col-md-6">
        <nav aria-label="Multi Actions" class="select_actions" style="display: none;">
            <ul class="pagination" style="margin-top: 0; margin-bottom: 5px;">
                <li class="disabled">
                    <span><span class="selected_n">10</span> {t}Bauteile ausgewählt{/t}</span>
                </li>
                <li><select name="action">
                        <option value="">{t}Auswählen{/t}</option>
                        <option value="delete">{t}Löschen{/t}</option>
                        <option value="move">{t}Verschieben nach{/t}</option>
                    </select>
                </li>
                <li>
                    <select name="target">
                        <option>{t}Auswählen{/t}</option>
                        <optgroup label="{t}Kategorie{/t}">

                        </optgroup>
                        <optgroup label="{t}Footprint{/t}">

                        </optgroup>
                    </select>
                </li>
                <li><button type="submit" name="multi_action">Ok</button></li>
            </ul>
        </nav>
    </div>
    <div class="col-md-6">
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