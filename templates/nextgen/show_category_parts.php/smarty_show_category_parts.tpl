{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading">
        {t}Sonstiges{/t}
    </div>
    <div class="panel-body">

        <form action="" method="post" class="form-horizontal">
            <input type="hidden" name="cid" value="{$cid}">
            <input type="hidden" name="subcat" value="{if $with_subcategories}0{else}1{/if}">

            <div class="form-group">
                <div class="col-md-10">
                    <button type="submit" class="btn btn-default {if $with_subcategories}active{/if}" name="subcat_button" >{t}Unterkategorien einblenden{/t}</button>
                </div>
            </div>
        </form>


        <div style="float: right;">
            <form action="" method="post" class="no-progbar no-ajax">
                <input type='hidden' name='cid'   value='{$cid}'>
                <input type="hidden" name="subcat" value="{if $with_subcategories}0{else}1{/if}">

                <div class="form-inline">
                    <label>{t}Exportieren:{/t}</label>
                    <select name="export_format" class="form-control">
                        {foreach $export_formats as $format}
                            <option value="{$format.value}" {if isset($format.selected)}selected{/if}>{$format.text}</option>
                        {/foreach}
                    </select>

                    <button class="btn btn-primary" type="submit" name="export">{t}OK{/t}</button>
                </div>
            </form>
        </div>

        {if $can_create}
            <a class="btn btn-primary" href="edit_part_info.php?category_id={$cid}">
                {t}Neues Teil in dieser Kategorie{/t}
            </a>
        {/if}



    </div>
</div>

<form method="get">
    <input type="hidden" name="cid" value="{$cid}">
    <input type="hidden" name="subcat" value="{$with_subcategories}">
    <input type="hidden" name="page" value="1">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="Page navigation" class="pull-right">
                <ul class="pagination" style="margin-top: 0; margin-bottom: 5px;">
                    <li class="disabled"><a class="no-progbar">{$pagination.lower_result}-{$pagination.upper_result}/{$pagination.max_entries}</a></li>
                    {foreach $pagination.entries as $page}
                        <li {if isset($page.disabled) && $page.disabled}class="disabled" {/if}
                                {if isset($page.active) && $page.active}class="active"{/if}>
                            <a {if !isset($page.disabled) || !$page.disabled}href="{$page.href}{/if}"
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
</form>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-tag" aria-hidden="true"></i>&nbsp;
        <b>{$table_rowcount}</b> {t}Teile in der Kategorie{/t} <b>"{$category_name}"</b>
    </div>
    <form method="post" action="" class="no-progbar">
        <input type="hidden" name="cid" value="{$cid}">
        <input type="hidden" name="subcat" value="{if $with_subcategories}1{else}0{/if}">
        <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
        {include file='../smarty_table.tpl'}
    </form>
</div>

<form method="get">
    <input type="hidden" name="cid" value="{$cid}">
    <input type="hidden" name="subcat" value="{$with_subcategories}">
    <input type="hidden" name="page" value="1">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="Page navigation" class="pull-right">
                <ul class="pagination" style="margin-top: 0; margin-bottom: 5px;">
                    <li class="disabled"><a class="no-progbar">{$pagination.lower_result}-{$pagination.upper_result}/{$pagination.max_entries}</a></li>
                    {foreach $pagination.entries as $page}
                        <li {if isset($page.disabled) && $page.disabled}class="disabled" {/if}
                                {if isset($page.active) && $page.active}class="active"{/if}>
                            <a {if !isset($page.disabled) || !$page.disabled}href="{$page.href}{/if}"
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
</form>
