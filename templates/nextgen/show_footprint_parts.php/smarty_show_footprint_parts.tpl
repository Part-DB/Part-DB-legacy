{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading">
        {t}Sonstiges{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post" class="form-horizontal no-progbar">
            <input type="hidden" name="fid" value="{$fid}">
            <input type="hidden" name="subfoot" value="{if $with_subfoot}0{else}1{/if}">

            <div class="form-group">
                <div class="col-md-10">
                    <button type="submit" class="btn btn-default {if $with_subfoot}active{/if}" name="subfoot_button" >{t}Unterfootprints einblenden{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<form method="get">
    <input type="hidden" name="fid" value="{$fid}">
    <input type="hidden" name="subfoot" value="{$with_subfoot}">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-cube" aria-hidden="true"></i>&nbsp;
        <b>{$table_rowcount}</b> {t}Teile mit Footprint{/t} "<b>{$footprint_name}</b>"
    </div>
    <form method="post" action="" class="no-progbar">
        <input type="hidden" name="lid" value="{$fid}">
        <input type="hidden" name="subloc" value="{if $with_subfoot}1{else}0{/if}">
        <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
           {include file='../smarty_table.tpl'}
    </form>
</div>

<form method="get">
    <input type="hidden" name="fid" value="{$fid}">
    <input type="hidden" name="subcat" value="{$with_subfoot}">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>