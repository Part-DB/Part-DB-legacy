{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading">{t}Teile per Name zuordnen{/t}</div>
    <div class="panel-body">
        <form method="post" class="form-horizontal" action="">
           <div class="form-group">
                <label class="control-label col-md-2">{t}Suchwort:{/t}</label>
                <div class="col-md-10">
                   <div class="input-group">
                        <input type="text" class="form-control" name="new_part_name" placeholder="{t}Suchwort:{/t}">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit" name="show_searched_parts">{t}Teile auflisten{/t}</button>
                        </span>
                    </div>
                </div>
                <input type="hidden" name="device_id" value="{$device_id}">
            </div>
        </form>
    {if isset($no_searched_parts_found) && $no_searched_parts_found}
        <div class="row">
            <span class="col-md-12 text-danger">{t}Die Suche ergab keine Treffer!{/t}</span>
        </div>
    {else}
        {if isset($searched_parts_rowcount)}
            <div class="row">
                <form method="post" action="" class="col-md-12">
                    <input type="hidden" name="device_id" value="{$device_id}">
                    <div class="row">
                        {include "../smarty_table.tpl"}
                    </div>
                    <i class="row col-md-12">{t}Falls Sie Bauteile zur Baugruppe hinzufügen, die dort bereits vorhanden sind,
                    dann werden die Stückzahlen addiert und die Bestückungsdaten mit einem Komma aneinandergehängt.{/t}</i>
                    <input type="hidden" name="searched_parts_rowcount" value="{$searched_parts_rowcount}">
                    <button class="btn btn-success" type="submit" name="assign_by_selected">{t}Hinzufügen{/t}</button>
                </form>
            </div>
        {/if}
    {/if}
</div>

</div>
