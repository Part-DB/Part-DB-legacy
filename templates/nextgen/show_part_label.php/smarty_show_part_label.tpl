{locale path="nextgen/locale" domain="partdb"}

<form method="post">
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
                        <select class="form-control" name="type">
                            {foreach $supported_types as $t}
                                {if $t == 2}<option value="2" {if isset($type) && $type==2}selected{/if}>{t}1D-Barcode (EAN8){/t}</option>{/if}
                                {if $t == 1}<option value="1" {if isset($type) && $type==1}selected{/if}>{t}QR-Code{/t}</option>{/if}
                                {if $t == 0}<option value="0" {if isset($type) && $type==0}selected{/if}>{t}kein Barcode{/t}</option>{/if}
                            {/foreach}
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
                            <optgroup label="{t}Benutzerdefiniert{/t}">
                                <option value="custom" {if $selected_preset == "custom"}selected{/if}>{t}Benutzerdefiniert{/t}</option>
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

    <div class="panel panel-default">
        <div class="panel-heading">
            <a data-toggle="collapse" class="link-collapse text-default" href="#panel-advanced">
                {t}Erweiterte Einstellungen{/t}
            </a>
        </div>
        <div class="panel-body panel-collapse collapse {if !empty($comment)}in{/if}" id="panel-advanced">
            <ul class="nav nav-pills">
                <li role="presentation" class="active"><a href="#tab-text" data-toggle="tab" class="link-anchor"><i class="fas fa-font"></i> {t}Text{/t}</a></li>
                <li role="presentation"><a href="#tab-barcode" data-toggle="tab" class="link-anchor"><i class="fas fa-barcode"></i> {t}Barcode{/t}</a></li>
                {*<li role="presentation"><a href="#">Messages</a></li>*}
            </ul>

            <br>

            <div class="tab-content">

                <div id="tab-text" class="tab-pane fade in active">
                    <div class="form-horizontal">

                        <div class="form-group">
                            <label class="col-md-3 control-label">{t}Ausgabemodus:{/t}</label>
                            <div class="col-md-9">
                                <div class="radio radio-inline">
                                    <input type="radio" name="radio_output" value="html" {if $radio_output == "html"}checked{/if}><label>{t}HTML{/t}</label>
                                </div>
                                <div class="radio radio-inline">
                                    <input type="radio" name="radio_output" value="text" {if $radio_output == "text"}checked{/if}><label>{t}Text{/t}</label>
                                </div>
                                <p class="help-block">{t}Wenn sie die Ausgabe auf Text umstellen, wird die ausgegebene PDF Datei kleiner, dafür werden evtl. vorhandene HTML Tags als Text dargestellt und nicht interpretiert!{/t}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">{t}Schriftstil:{/t}</label>
                            <div class="col-md-9">
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" name="text_bold" {if $text_bold}checked{/if}><label><b>{t}Fett{/t}</b></label>
                                </div>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" name="text_italic" {if $text_italic}checked{/if}><label><i>{t}Kursiv{/t}</i></label>
                                </div>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" name="text_underline" {if $text_underline}checked{/if}><label><u>{t}Unterstrichen{/t}</u></label>
                                </div>
                                <p class="help-block">{t}Beachten Sie dass sich diese Einstellung auf alle Textzeilen auswirkt!{/t}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">{t}Schriftgröße:{/t}</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" min="1" max="72" name="text_size" placeholder="8" value="{$text_size}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">{t}Benutzerdefinierte Zeilen:{/t}</label>
                            <div class="col-md-9">
                                <textarea rows="4" class="form-control" name="custom_rows">{$custom_rows}</textarea>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="tab-barcode" class="tab-pane fade">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-md-3 control-label">{t}Barcode-Ausrichtung:{/t}</label>
                            <div class="col-md-9">
                                <div class="radio radio-inline">
                                    <input type="radio" name="barcode_alignment" value="left" {if $barcode_alignment == "left"}checked{/if}><label>{t}Links{/t}</label>
                                </div>
                                <div class="radio radio-inline">
                                    <input type="radio" name="barcode_alignment" value="center" {if $barcode_alignment == "center"}checked{/if}><label>{t}Zentrieren{/t}</label>
                                </div>
                                <div class="radio radio-inline">
                                    <input type="radio" name="barcode_alignment" value="right" {if $barcode_alignment == "right"}checked{/if}><label>{t}Rechts{/t}</label>
                                </div>
                            </div>
                        </div>
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
            {* <embed width="100%" height="200" type="application/pdf" src="{$preview_src}"> *}
            <object width="100%" height="200" type="application/pdf" data="{$preview_src}" id="pdf_content">
                <p>{t}Ihr Browser unterstützt keine Vorschau von PDF-Dateien. Um die Datei anzusehen, laden Sie die Datei herunter{/t}</p>
            </object>
        </div>
    </div>
{/if}