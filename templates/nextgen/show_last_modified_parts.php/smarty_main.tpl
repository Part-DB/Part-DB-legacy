{locale path="nextgen/locale" domain="partdb"}

<form method="get">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

<div class="panel panel-primary">
    <div class="panel-heading"><i class="fas fa-clock fa-fw"></i>
        {if $mode == "last_modified"}
            {t}Zuletzt bearbeitete Bauteile{/t}
        {else}
            {t}Zuletzt hinzugefügte Bauteile{/t}
        {/if}</div>
    {include file="../smarty_table.tpl"}
</div>

<form method="get">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>