{locale path="nextgen/locale" domain="partdb"}

<form method="get">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-globe" aria-hidden="true"></i>&nbsp;
        {t}Alle Bauteile{/t}
    </div>
    <form method="post" action="" class="no-progbar">
        <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
           {include file='../smarty_table.tpl' table_selectable=true}
    </form>
</div>

<form method="get">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>