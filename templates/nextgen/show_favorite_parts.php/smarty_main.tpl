{locale path="nextgen/locale" domain="partdb"}

<form method="get">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

<div class="card border-primary">
    <div class="card-header bg-primary text-white"><i class="fa fa-star fa-fw" aria-hidden="true"></i>
         {t}Favorisierte Bauteile{/t}</div>
    {include file="../smarty_table.tpl"}
</div>

<form method="get">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>