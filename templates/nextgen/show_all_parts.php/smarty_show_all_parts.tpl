{locale path="nextgen/locale" domain="partdb"}

<form method="post">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

<div class="card border-primary">
    <div class="card-header bg-primary text-white">
        <i class="fa fa-globe" aria-hidden="true"></i>&nbsp;
        {t}Alle Bauteile{/t}
    </div>
    <form method="post" action="" class="no-progbar">
        <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
           {include file='../smarty_table.tpl' table_selectable=true}
    </form>
</div>

<form method="post">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>