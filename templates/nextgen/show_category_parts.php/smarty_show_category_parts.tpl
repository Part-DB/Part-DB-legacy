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

    {include "../smarty_pagination.tpl"}
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
        <input type="hidden" name="limit" value="{$limit}">
        <input type="hidden" name="page" value="{$page}">

        {include file='../smarty_table.tpl'}
    </form>
</div>

<form method="get">
    <input type="hidden" name="cid" value="{$cid}">
    <input type="hidden" name="subcat" value="{$with_subcategories}">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>
