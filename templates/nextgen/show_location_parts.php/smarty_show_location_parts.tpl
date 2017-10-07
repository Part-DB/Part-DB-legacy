{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading">
        {t}Sonstiges{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post" class="form-horizontal no-progbar">
            <input type="hidden" name="lid" value="{$lid}">
            <input type="hidden" name="subloc" value="{if $with_sublocations}0{else}1{/if}">

            <div class="form-group">
                <div class="col-md-10">
                    <button type="submit" class="btn btn-default {if $with_sublocations}active{/if}" name="subloc_button" >{t}Unterlagerorte einblenden{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<form method="get">
    <input type="hidden" name="lid" value="{$lid}">
    <input type="hidden" name="subloc" value="{$with_sublocations}">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-cube" aria-hidden="true"></i>&nbsp;
        <b>{$table_rowcount}</b> {t}Teile im Lagerort{/t} "<b>{$location_name}</b>"
    </div>
    <form method="post" action="" class="no-progbar">
        <input type="hidden" name="lid" value="{$lid}">
        <input type="hidden" name="subloc" value="{if $with_sublocations}1{else}0{/if}">
        <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
        <input type="hidden" name="limit" value="{$limit}">
        <input type="hidden" name="page" value="{$page}">
           {include file='../smarty_table.tpl'}
    </form>
</div>

<form method="get">
    <input type="hidden" name="lid" value="{$lid}">
    <input type="hidden" name="subloc" value="{$with_sublocations}">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>
