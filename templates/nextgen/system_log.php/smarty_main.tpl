{locale path="nextgen/locale" domain="partdb"}

<form method="get">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

<div class="panel panel-primary">
    <div class="panel-heading"><i class="fas fa-crosshairs fa-fw"></i>
            {t}Eventlog{/t}
    </div>
    {include file="../smarty_eventlog.tpl"}
</div>

<form method="get">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>