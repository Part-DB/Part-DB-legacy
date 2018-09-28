{locale path="nextgen/locale" domain="partdb"}
<div class="card mt-3">
    <div class="card-header"><a data-toggle="collapse" class="link-collapse text-default" href="#panel-export">
            <i class="fas fa-bolt fa-fw"></i> {t}Teile abfassen/einbuchen/vormerken/exportieren{/t}
        </a></div>
    <div class="card-body card-collapse collapse {if isset($export_result)}in{/if}" id="panel-export">
        <form method="post" action="" id="export" class="form-horizontal">
            <input type="hidden" name="device_id" value="{$device_id}">
            <div class="form-group row">
                <label class="col-form-label col-md-3">{t}Multiplikator:{/t}</label>
                <div class="col-md-9">
                    <input type="number" min="1"  class="form-control" name="export_multiplier" size="3"
                           value="{if isset($export_multiplier) && $export_multiplier>0}{$export_multiplier}{else}1{/if}"
                           {if !$can_part_instock}disabled{/if}>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label col-md-3">{t}Teile abfassen oder einbuchen:{/t}</label>
                <div class="col-md-9" class="btn-group" role="group">
                    <button class="btn btn-secondary" type="submit" name="book_parts" {if !$can_part_instock}disabled{/if}>
                        {t}Abfassen (-){/t}</button>
                    <button class="btn btn-secondary" type="submit" name="book_parts_in" {if !$can_part_instock}disabled{/if}>
                        {t}Einbuchen (+){/t}</button>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label col-md-3">{t}Zum Bestellen vormerken:{/t}</label>
                <div class="col-md-9">
                    {if isset($order_quantity) && $order_quantity > 0}
                        <div class="form-control-plaintext">{t 1=$order_quantity}Es sind %1 Stk. von dieser Baugruppe zum Bestellen vorgemerkt{/t}
                            ({if isset($order_only_missing_parts) && $order_only_missing_parts}{t}Nur fehlende Teile{/t}{else}{t}Alle Teile{/t}{/if}).</div>
                        <button class="btn btn-secondary" type="submit" name="remove_order"
                                {if !$can_part_order}disabled{/if}>{t}Aufheben{/t}</button>
                    {else}
                        <button class="btn btn-secondary" type="submit" name="add_order" {if !$can_part_order}disabled{/if}>
                            {t}Alle{/t}</button>
                        <button class="btn btn-secondary" type="submit" name="add_order_only_missing" {if !$can_part_order}disabled{/if}>
                            {t}Nur fehlende Teile{/t}</button>
                    {/if}
                </div>
            </div>

        </form>
        <form method="post" action="" id="export" class="form-horizontal no-ajax">
            <input type="hidden" name="device_id" value="{$device_id}">
            <div class="form-group row">
                <label class="col-form-label col-md-3">{t}Exportieren:{/t}</label>
                <div class="col-md-9">
                    <select name="export_format" class="form-control">
                        {foreach $export_formats as $format}
                            <option value="{$format.value}" {if $format.selected}selected{/if}>{$format.text}</option>
                        {/foreach}
                    </select>
                    <div class="form-check form-check-inline abc-checkbox">
                        <input class="form-check-input" type="checkbox" name="only_missing_material" {if $export_only_missing}checked{/if}>
                        <label class="form-check-label">{t}Nur fehlendes Material{/t}</label>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-secondary" name="export_show">{t}Anzeigen{/t}</button>
                        <button type="submit" class="btn btn-secondary" name="export_download">{t}Herunterladen{/t}</button>
                    </div>
                </div>
            </div>
            {if isset($export_result)}
                <hr>
                <div class="well">
                    <code>
                        {$export_result nofilter}
                    </code>
                </div>
            {/if}
        </form>
    </div>
</div>
