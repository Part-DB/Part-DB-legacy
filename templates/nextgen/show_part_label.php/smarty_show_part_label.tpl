<form method="get">

    <div class="panel panel-primary">
        <div class="panel-heading"><i class="fa fa-barcode" aria-hidden="true"></i>
            {t}Label erzeugen{/t}</div>
        <div class="panel-body">
            <div class="form-horizontal">

                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Typ:{/t}</label>
                    <div class="col-md-9">
                        <select class="form-control" name="generator">
                            <option value="part">{t}Bauteil{/t}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">{t}ID:{/t}</label>
                    <div class="col-md-9">
                        <input class="form-control" min="1" name="id" type="number" value="{if $id>0}{$id}{/if}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Barcode Typ:{/t}</label>
                    <div class="col-md-9">
                        <select class="form-control" name="type" disabled>
                            <option value="2" {if isset($type) && $type=="2"}selected{/if}>{t}1D-Barcode (EAN8){/t}</option>
                            <option value="3" {if isset($type) && $type=="3"}selected{/if}>{t}QR-Code{/t}</option>
                            <option value="0" {if isset($type) && $type=="0"}selected{/if}>{t}kein Barcode{/t}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Größe:{/t}</label>
                    <div class="col-md-9" >
                        <select class="form-control" name="size">
                            {foreach $supported_sizes as $size}
                                <option value="{$size}" {if $selected_size == $size}selected{/if}>{$size} mm</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Line Preset:{/t}</label>
                    <div class="col-md-9">
                        <select class="form-control" name="preset">
                            <optgroup label="{t}Presets{/t}">
                                {foreach $available_presets as $preset}
                                    <option value="{$preset.name}" {if $selected_preset == $preset.name}selected{/if}>{$preset.name}</option>
                                {/foreach}
                            </optgroup>
                            {*
                            <optgroup label="{t}Benutzerdefiniert{/t}">
                                <option onchange="">{t}Benutzerdefiniert{/t}</option>
                            </optgroup> *}
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn btn-primary" name="label_generate">{t}Erzeuge Label{/t}</button>
                        {if isset($download_link)}<a class="link-external" href="{$download_link}">{t}Download Label{/t}</a>{/if}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="custom" class="panel panel-default" hidden>
        <div class="panel-heading">{t}Benutzerdefinierter Text{/t}</div>
        <div class="panel-body">
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Text:{/t}</label>
                    <div class="col-md-9">
                        <textarea class="form-control" rows="5" placeholder="z.B. %pid%"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{if !empty($preview_src)}
<div class="panel panel-default">
    <div class="panel-heading">{t}Vorschau{/t}</div>
    <div class="">
        <embed width="100%" height="200" type="application/pdf" src="{$preview_src}">
    </div>
</div>
{/if}