{locale path="nextgen/locale" domain="partdb"}

<form method="post" class="no-progbar">
    <div class="panel panel-primary">
        <div class="panel-heading"><i class="fa fa-barcode" aria-hidden="true"></i>
            {t}Label erzeugen{/t}</div>
        <div class="panel-body">
            <div class="form-horizontal">

                <ul class="nav nav-tabs">
                    <li role="presentation" class="active"><a href="#tab-settings" data-toggle="tab" class="link-anchor"><i class="fas fa-wrench"></i> {t}Einstellungen{/t}</a></li>
                    <li role="presentation"><a href="#tab-profiles" data-toggle="tab" class="link-anchor"><i class="fas fa-bookmark"></i> {t}Profile{/t}</a></li>
                    {*<li role="presentation"><a href="#">Messages</a></li>*}
                </ul>

                <br>

                <div class="tab-content">

                    <div id="tab-settings" class="tab-pane fade in active">
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
                                <select class="form-control" name="type" {if !$can_edit_option}disabled{/if}>
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
                            <div class="col-md-4" >
                                <select class="form-control" name="size" id="size" {if !$can_edit_option}disabled{/if} onchange="updateCustomSizeStatus();">
                                    <optgroup label="{t}Vorgaben{/t}">
                                    {foreach $supported_sizes as $size}
                                        <option value="{$size}" {if $selected_size == $size}selected{/if}>{$size} mm</option>
                                    {/foreach}
                                    </optgroup>
                                    <optgroup label="{t}Benutzerdefiniert{/t}">
                                        <option value="custom" {if $selected_size == "custom"}selected{/if}>{t}Benutzerdefiniert{/t}</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">

                                    <input type="number" min="0" class="form-control" placeholder="{t}Breite{/t}" name="custom_width" required {if !$can_edit_option}disabled{/if} value="{$custom_width}">
                                    <span class="input-group-addon" id="basic-addon1">x</span>
                                    <input type="number" min="0" class="form-control" placeholder="{t}Höhe{/t}" name="custom_height" required {if !$can_edit_option}disabled{/if} value="{$custom_height}">
                                    <span class="input-group-addon">mm</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" >{t}Line Preset:{/t}</label>
                            <div class="col-md-9">
                                <select class="form-control" name="preset" id="preset" {if !$can_edit_option}disabled{/if} onchange="updateCustomRowStatus()">
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

                    <div id="tab-profiles" class="tab-pane fade">
                        {* Save profile section *}
                        <div class="form-group">
                            <label class="col-md-3 control-label">{t}Profilname:{/t}</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="save_name" value="{$save_name}" id="save-name" {if !$can_save_profile}disabled{/if}>
                                <div class="checkbox">
                                    <input type="checkbox" name="save_name" value="default" onchange="$('#save-name').prop('disabled', $(this).prop('checked'));" {if !$can_save_profile}disabled{/if}>
                                    <label>{t}Standard für den aktuellen Generatortyp{/t}</label>
                                </div>
                                <p class="help-block">{t}Wenn bereits ein Profil mit dem aktuellem Namen existiert, dann wird es überschrieben/bearbeitet!{/t}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-success" name="save_profile" {if !$can_save_profile}disabled{/if}><i class="fas fa-save fa-fw"></i> {t}Speichere Profil{/t}</button>
                            </div>
                        </div>
                        <hr>

                        {* Load/Remove profile section *}
                        <div class="form-group">
                            <label class="col-md-3 control-label">{t}Profilname:{/t}</label>
                            <div class="col-md-9">
                                <!-- <input type="text" class="form-control" name="save_name" value="{$save_name}" id="save-name">-->
                                <select class="form-control selectpicker" data-live-search="true" name="selected_profile">
                                    {foreach $profiles as $p}
                                        <option value="{$p}" {if $p == $selected_profile}selected{/if}>{$p}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-primary" name="load_profile" ><i class="fas fa-folder-open fa-fw"></i> {t}Lade Profil{/t}</button>
                                <button type="submit" class="btn btn-danger" name="delete_profile" {if !$can_delete_profile}disabled{/if}><i class="fas fa-trash"></i> {t}Lösche Profil{/t}</button>
                            </div>
                        </div>
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
            <ul class="nav nav-tabs">
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
                                    <input type="radio" name="radio_output" value="html" {if $radio_output == "html"}checked{/if} {if !$can_edit_option}disabled{/if} ><label>{t}HTML{/t}</label>
                                </div>
                                <div class="radio radio-inline">
                                    <input type="radio" name="radio_output" value="text" {if $radio_output == "text"}checked{/if} {if !$can_edit_option}disabled{/if}><label>{t}Text{/t}</label>
                                </div>
                                <p class="help-block">{t}Wenn sie die Ausgabe auf Text umstellen, wird die ausgegebene PDF Datei evtl. kleiner, dafür werden evtl. vorhandene HTML Tags als Text dargestellt und nicht interpretiert!{/t}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">{t}Schriftstil:{/t}</label>
                            <div class="col-md-9">
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" name="text_bold" {if $text_bold}checked{/if} {if !$can_edit_option}disabled{/if}><label><b>{t}Fett{/t}</b></label>
                                </div>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" name="text_italic" {if $text_italic}checked{/if} {if !$can_edit_option}disabled{/if}><label><i>{t}Kursiv{/t}</i></label>
                                </div>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" name="text_underline" {if $text_underline}checked{/if} {if !$can_edit_option}disabled{/if}><label><u>{t}Unterstrichen{/t}</u></label>
                                </div>
                                <p class="help-block">{t}Beachten Sie dass sich diese Einstellung auf alle Textzeilen auswirkt!{/t}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">{t}Schriftgröße:{/t}</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" min="1" max="72" name="text_size" placeholder="8" value="{$text_size}" required {if !$can_edit_option}disabled{/if}>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">{t}Benutzerdefinierte Zeilen:{/t}</label>
                            <div class="col-md-9">
                                <textarea rows="4" class="form-control" name="custom_rows" {if !$can_edit_option}disabled{/if}>{$custom_rows}</textarea>
                                <p class="help-block">{t}Für die Formatierung können HTML-Tags verwendet werden.{/t}</p>
                                <p class="help-block">{t escape=false}Sie können dynamische Daten wie Uhrzeit oder Bauteilname über Platzhalter der Form %PLATZHALTER% einfügen. Im <a target="_blank" class="link-external" rel="noopener" href="https://github.com/jbtronics/Part-DB/wiki/Labels">Wiki</a> finden Sie eine Übersicht aller möglichen Platzhalter.{/t}</p>
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
                                    <input type="radio" name="barcode_alignment" value="left" {if $barcode_alignment == "left"}checked{/if} {if !$can_edit_option}disabled{/if}><label>{t}Links{/t}</label>
                                </div>
                                <div class="radio radio-inline">
                                    <input type="radio" name="barcode_alignment" value="center" {if $barcode_alignment == "center"}checked{/if} {if !$can_edit_option}disabled{/if}><label>{t}Zentrieren{/t}</label>
                                </div>
                                <div class="radio radio-inline">
                                    <input type="radio" name="barcode_alignment" value="right" {if $barcode_alignment == "right"}checked{/if} {if !$can_edit_option}disabled{/if}><label>{t}Rechts{/t}</label>
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


<script>
        var should_open = Cookies.get("labels_advanced_settings_open");
        if (should_open == "true") {
            $("#panel-advanced").addClass('in');
        }

        $('#panel-advanced').on('shown.bs.collapse', function () {
            Cookies.set("labels_advanced_settings_open", true)
        });

        $('#panel-advanced').on('hidden.bs.collapse', function () {
            Cookies.set("labels_advanced_settings_open", false)
        });
</script>

<script>
    function updateCustomRowStatus() {
        var selectedPreset = $('#preset').find(":selected").val();
        $("[name=custom_rows]").prop("disabled", selectedPreset != "custom");
    }

    updateCustomRowStatus();
</script>

<script>
    function updateCustomSizeStatus() {
        var selectedSize = $('#size').find(":selected").val();
        $("[name=custom_height]").prop("disabled", selectedSize != "custom");
        $("[name=custom_width]").prop("disabled", selectedSize != "custom");
    }

    updateCustomSizeStatus();
</script>