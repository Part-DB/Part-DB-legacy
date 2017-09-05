{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-default">
    <div class="panel-heading"><b>{if isset($device_parts_rowcount)}{$device_parts_rowcount}{else}0{/if}</b> {t}Zugeordnete Teile zu{/t} "<b>{$device_name}</b>"</div>
    <div class="panel-body">
        {if isset($device_parts_rowcount)}
            <form method="post" class="form-horizontal" action="" id="table">
                <input type="hidden" name="device_id" value="{$device_id}">
                <div class="row">
                   {include "../smarty_table.tpl"}
                </div>

                <div class="form-group">
                    <div class="col-md-12"><b>{t}Gesamtpreis:{/t} {$sum_price}</b></div>
                </div>
                
                <div class="form-group">
                    <div class="col-md-12"><i>{t}Teile mit der Stückzahl "0" werden beim Übernehmen aus dieser Baugruppe entfernt.{/t}</i></div>
                </div>
                   
                <input type="hidden" name="device_parts_rowcount" value="{$device_parts_rowcount}">
                <div class="form-group">
                    <div class="col-md-12">
                        <button class="btn btn-success" type="submit" name="device_parts_apply">{t}Änderungen übernehmen{/t}</button>
                        <button class="btn btn-danger" type="submit">{t}Änderungen verwerfen{/t}</button>
                    </div>
                </div>
            </form>
        {else}
            <p>{t}Keine Bauteile zugeordnet.{/t}</p>
        {/if}
    </div>
</div>
