{locale path="nextgen/locale" domain="partdb"}

<form method="post">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

<div class="card border-primary">
    <div class="card-header bg-primary text-white"><i class="fas fa-clock fa-fw"></i>
        {if $mode == "last_modified"}
            {t}Zuletzt bearbeitete Bauteile{/t}
        {else}
            {t}Zuletzt hinzugefügte Bauteile{/t}
        {/if}</div>
    {include file="../smarty_table.tpl"}
</div>

<form method="post">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>