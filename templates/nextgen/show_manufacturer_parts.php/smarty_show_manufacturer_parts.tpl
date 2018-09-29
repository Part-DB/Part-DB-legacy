{locale path="nextgen/locale" domain="partdb"}

<span id="export-title">{t}Bauteile vom Hersteller{/t}: {$manufacturer_name}</span>
<span id="export-messageTop">{t}Vollständiger Pfad{/t}: {$manufacturer_fullpath}</span>

{include "../smarty_breadcrumb.tpl"}

{if $other_panel_position == "top" || $other_panel_position == "both"}
    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            <a data-toggle="collapse" class="link-collapse text-white" href="#panel-other">
                {t}Sonstiges{/t}
            </a>
        </div>
        <div class="card-body card-collapse collapse {if !$other_panel_collapse}show{/if}" id="panel-other">
            <form action="" method="post" class="form-horizontal no-progbar">
                <input type="hidden" name="mid" value="{$mid}">
                <input type="hidden" name="subman" value="{if $with_submanufacturers}0{else}1{/if}">
                <div class="form-group row">
                    <div class="col-md-10">
                        <button type="submit" class="btn btn-outline-secondary {if $with_submanufacturers}active{/if}" name="subman_button" >{t}Unterhersteller einblenden{/t}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
{/if}

<form method="get">
    <input type="hidden" name="mid" value="{$mid}">
    <input type="hidden" name="subman" value="{$with_submanufacturers}">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

<div class="card">
    <div class="card-header">
        <i class="fa fa-industry" aria-hidden="true"></i>&nbsp;
        <b>{$table_rowcount}</b> {t}Teile mit Hersteller{/t} "<b>{$manufacturer_name}</b>"
    </div>
    <form method="post" action="" class="no-progbar">
        <input type="hidden" name="lid" value="{$mid}">
        <input type="hidden" name="subloc" value="{if $with_submanufacturers}1{else}0{/if}">
        <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
        <input type="hidden" name="limit" value="{$limit}">
        <input type="hidden" name="page" value="{$page}">
        {include file='../smarty_table.tpl' table_selectable=true}
    </form>
</div>

<form method="get">
    <input type="hidden" name="mid" value="{$mid}">
    <input type="hidden" name="subman" value="{$with_submanufacturers}">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

{if $other_panel_position == "bottom" || $other_panel_position == "both"}
    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            <a data-toggle="collapse" class="link-collapse text-white" href="#panel-other">
                {t}Sonstiges{/t}
            </a>
        </div>
        <div class="card-body card-collapse collapse {if !$other_panel_collapse}show{/if}" id="panel-other">
            <form action="" method="post" class="form-horizontal no-progbar">
                <input type="hidden" name="mid" value="{$mid}">
                <input type="hidden" name="subman" value="{if $with_submanufacturers}0{else}1{/if}">
                <div class="form-group row">
                    <div class="col-md-10">
                        <button type="submit" class="btn btn-outline-secondary {if $with_submanufacturers}active{/if}" name="subman_button" >{t}Unterhersteller einblenden{/t}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
{/if}