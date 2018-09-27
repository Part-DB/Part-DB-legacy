{locale path="nextgen/locale" domain="partdb"}

{include "../smarty_breadcrumb.tpl"}

<span id="export-title">{t}Bauteile in Kategorie{/t}: {$category_name}</span>
<span id="export-messageTop">{t}Vollständiger Pfad{/t}: {$category_fullpath}</span>

{if $other_panel_position == "top" || $other_panel_position == "both"}
    <div class="card border-primary">
        <div class="card-header text-white bg-primary">
            <a data-toggle="collapse" class="link-collapse text-white" href="#panel-other">
                {t}Sonstiges{/t}
            </a>
        </div>
        <div class="card-body collapse {if !$other_panel_collapse}in{/if}" id="panel-other">
            <form action="" method="post" class="form-horizontal">
                <input type="hidden" name="cid" value="{$cid}">
                <input type="hidden" name="subcat" value="{if $with_subcategories}0{else}1{/if}">

                <div class="form-group row">
                    <div class="col mr-auto">
                        <button type="submit" class="btn btn-outline-secondary {if $with_subcategories}active{/if}" name="subcat_button" >{t}Unterkategorien einblenden{/t}</button>
                    </div>
                </div>
            </form>

            <div class="row">

                {if $can_create}
                    <div class="form-inline col-8">
                        <div class="form-group">
                            <div class="col-md-12"></div>
                            <a class="btn btn-primary" href="edit_part_info.php?category_id={$cid}">
                                {t}Neues Teil in dieser Kategorie{/t}
                            </a>
                        </div>
                    </div>
                {/if}

                <div class="form-inline col-4 float-right" style="">
                    <form action="" method="post" class="no-progbar no-ajax align-self-end">
                        <input type='hidden' name='cid'   value='{$cid}'>
                        <input type="hidden" name="subcat" value="{$with_subcategories}">

                        <div class="form-group">
                            <label class="mr-2">{t}Exportieren:{/t}</label>
                            <select name="export_format" class="form-control mr-2">
                                {foreach $export_formats as $format}
                                    <option value="{$format.value}" {if isset($format.selected)}selected{/if}>{$format.text}</option>
                                {/foreach}
                            </select>

                            <button class="btn btn-primary" type="submit" name="export">{t}OK{/t}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
{/if}

<form method="post">
    <input type="hidden" name="cid" value="{$cid}">
    <input type="hidden" name="subcat" value="{$with_subcategories}">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>


<div class="card bg-light">
    <div class="card-header">
        <i class="fa fa-tag" aria-hidden="true"></i>&nbsp;
        <b>{$table_rowcount}</b> {t}Teile in der Kategorie{/t} <b>"{$category_name}"</b>
    </div>
    <form method="post" action="" class="no-progbar">
        <input type="hidden" name="cid" value="{$cid}">
        <input type="hidden" name="subcat" value="{if $with_subcategories}1{else}0{/if}">
        <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
        <input type="hidden" name="limit" value="{$limit}">
        <input type="hidden" name="page" value="{$page}">

        {include file='../smarty_table.tpl' table_selectable=true}
    </form>
</div>

<form method="post">
    <input type="hidden" name="cid" value="{$cid}">
    <input type="hidden" name="subcat" value="{$with_subcategories}">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl" }
</form>

{if $other_panel_position == "bottom" || $other_panel_position == "both"}
    <div class="card border-primary">
        <div class="card-header text-white bg-primary">
            <a data-toggle="collapse" class="link-collapse text-white" href="#panel-other">
                {t}Sonstiges{/t}
            </a>
        </div>
        <div class="card-body collapse {if !$other_panel_collapse}in{/if}" id="panel-other">
            <form action="" method="post" class="form-horizontal">
                <input type="hidden" name="cid" value="{$cid}">
                <input type="hidden" name="subcat" value="{if $with_subcategories}0{else}1{/if}">

                <div class="form-group row">
                    <div class="col mr-auto">
                        <button type="submit" class="btn btn-outline-secondary {if $with_subcategories}active{/if}" name="subcat_button" >{t}Unterkategorien einblenden{/t}</button>
                    </div>
                </div>
            </form>

            <div class="row">

                {if $can_create}
                    <div class="form-inline col-8">
                        <div class="form-group">
                            <div class="col-md-12"></div>
                            <a class="btn btn-primary" href="edit_part_info.php?category_id={$cid}">
                                {t}Neues Teil in dieser Kategorie{/t}
                            </a>
                        </div>
                    </div>
                {/if}

                <div class="form-inline col-4 float-right" style="">
                    <form action="" method="post" class="no-progbar no-ajax align-self-end">
                        <input type='hidden' name='cid'   value='{$cid}'>
                        <input type="hidden" name="subcat" value="{$with_subcategories}">

                        <div class="form-group">
                            <label class="mr-2">{t}Exportieren:{/t}</label>
                            <select name="export_format" class="form-control mr-2">
                                {foreach $export_formats as $format}
                                    <option value="{$format.value}" {if isset($format.selected)}selected{/if}>{$format.text}</option>
                                {/foreach}
                            </select>

                            <button class="btn btn-primary" type="submit" name="export">{t}OK{/t}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
{/if}