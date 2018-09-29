{locale path="nextgen/locale" domain="partdb"}

<form method="get">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

<div class="card border-primary">
    <div class="card-header bg-primary text-white">
        <b>{$table_rowcount}</b> {t}Teile ohne Preis{/t}</div>
    {include file="../smarty_table.tpl"}
</div>

<form method="get">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>