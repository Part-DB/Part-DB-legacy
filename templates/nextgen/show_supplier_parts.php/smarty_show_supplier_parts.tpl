{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading">
        {t}Sonstiges{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post" class="form-horizontal no-progbar">
            <input type="hidden" name="sid" value="{$sid}">
            <input type="hidden" name="subsup" value="{if $with_subsuppliers}0{else}1{/if}">
            <div class="form-group">
                <div class="col-md-10">
                    <button type="submit" class="btn btn-default {if $with_subsuppliers}active{/if}" name="subsup_button">{t}Unterlieferanten einblenden{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<form method="get">
    <input type="hidden" name="sid" value="{$sid}">
    <input type="hidden" name="subsup" value="{$with_subsuppliers}">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-truck fa-fw" aria-hidden="true"></i>&nbsp;
        <b>{$table_rowcount}</b> {t}Teile mit Lieferant{/t} "<b>{$supplier_name}</b>"
    </div>
    <form method="post" action="" class="no-progbar">
        <input type="hidden" name="lid" value="{$sid}">
        <input type="hidden" name="subloc" value="{if $with_subsuppliers}1{else}0{/if}">
        <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
           {include file='../smarty_table.tpl'}
    </form>
</div>

<form method="get">
    <input type="hidden" name="sid" value="{$sid}">
    <input type="hidden" name="subsup" value="{$with_subsuppliers}">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>
