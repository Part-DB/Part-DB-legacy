<form method="post">

    <div class="panel panel-primary">
        <div class="panel-heading">{t}Label erzeugen{/t}</div>
        <div class="panel-body">
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Part-ID:{/t}</label>
                    <div class="col-md-9">
                        <input class="form-control" min="1" name="pid" type="number" value="{if $pid!=0}{$pid}{/if}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Barcode Typ:{/t}</label>
                    <div class="col-md-9">
                        <select class="form-control" name="type">
                            <option value="2" {if isset($type) && $type=="2"}selected{/if}>{t}1D-Barcode (EAN8){/t}</option>
                            <option value="3" {if isset($type) && $type=="3"}selected{/if}>{t}QR-Code{/t}</option>
                            <option value="0" {if isset($type) && $type=="0"}selected{/if}>{t}kein Barcode{/t}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Größe:{/t}</label>
                    <div class="col-md-9">
                        <select class="form-control">

                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Line Preset:{/t}</label>
                    <div class="col-md-9">
                        <select class="form-control">
                            <optgroup label="{t}Presets{/t}">
                                <option>{t}Preset A{/t}</option>
                                <option>{t}Preset B{/t}</option>
                                <option>{t}Preset C{/t}</option>
                            </optgroup>
                            <optgroup label="{t}Benutzerdefiniert{/t}">
                                <option onchange="">{t}Benutzerdefiniert{/t}</option>
                            </optgroup>
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