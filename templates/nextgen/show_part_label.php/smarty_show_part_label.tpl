<form method="post">

    <div class="panel panel-primary">
        <div class="panel-heading">Label erzeugen</div>
        <div class="panel-body">
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-md-3 control-label">Part-ID:</label>
                    <div class="col-md-9">
                        <input class="form-control" min="1" name="pid" type="number" value="{if $pid!=0}{$pid}{/if}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Barcode Typ:</label>
                    <div class="col-md-9">
                        <select class="form-control" name="type">
                            <option value="2" {if isset($type) && $type=="2"}selected{/if}>1D-Barcode (EAN8)</option>
                            <option value="3" {if isset($type) && $type=="3"}selected{/if}>QR-Code</option>
                            <option value="0" {if isset($type) && $type=="0"}selected{/if}>kein Barcode</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Größe:</label>
                    <div class="col-md-9">
                        <select class="form-control">

                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Line Preset:</label>
                    <div class="col-md-9">
                        <select class="form-control">
                            <optgroup label="Presets">
                                <option>Preset A</option>
                                <option>Preset B</option>
                                <option>Preset C</option>
                            </optgroup>
                            <optgroup label="Benutzerdefiniert">
                                <option onchange="">Benutzerdefiniert</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn btn-primary" name="label_generate">Erzeuge Label</button>
                        {if isset($download_link)}<a class="link-anchor" href="{$download_link}">{t}Download Label{/t}</a>{/if}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="custom" class="panel panel-default" hidden>
        <div class="panel-heading">Benutzerdefinierter Text</div>
        <div class="panel-body">
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-md-3 control-label">Text:</label>
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
    <div class="panel-heading">Vorschau</div>
    <div class="">
        <embed width="100%" height="200" type="application/pdf" src="{$preview_src}">
    </div>
</div>
{/if}