{locale path="nextgen/locale" domain="partdb"}

<form method="post" class="no-progbar">
    <div class="card border-primary">
        <div class="card-header bg-primary text-white"><i class="fa fa-barcode" aria-hidden="true"></i>
            {t}Label erzeugen{/t}</div>
        <div class="card-body">
            <div class="form-horizontal">

                <ul class="nav nav-tabs">
                    <li role="presentation" class="active nav-item"><a href="#tab-settings" data-toggle="tab" class="link-anchor nav-link active"><i class="fas fa-wrench"></i> {t}Einstellungen{/t}</a></li>
                    <li role="presentation" class="nav-item"><a href="#tab-profiles" data-toggle="tab" class="link-anchor nav-link"><i class="fas fa-bookmark"></i> {t}Profile{/t}</a></li>
                    {*<li role="presentation"><a href="#">Messages</a></li>*}
                </ul>

                <br>

                <div class="tab-content">

                    <div id="tab-settings" class="tab-pane fade show active">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">{t}Typ:{/t}</label>
                            <div class="col-md-10">
                                <select class="form-control" name="generator">
                                    <option value="part" {if $generator == "part"}selected{/if}>{t}Bauteil{/t}</option>
                                    <option value="location" {if $generator == "location"}selected{/if}>{t}Lagerort{/t}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">{t}ID:{/t}</label>
                            <div class="col-md-10">
                                <input class="form-control" min="1" name="id" type="number" value="{if $id>0}{$id}{/if}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">{t}Barcode Typ:{/t}</label>
                            <div class="col-md-10">
                                <select class="form-control" name="type" {if !$can_edit_option}disabled{/if}>
                                    {foreach $supported_types as $t}
                                        {if $t == 2}<option value="2" {if isset($type) && $type==2}selected{/if}>{t}1D-Barcode (EAN8){/t}</option>{/if}
                                        {if $t == 3}<option value="3" {if isset($type) && $type==3}selected{/if}>{t}1D-Barcode (Code 39){/t}</option>{/if}
                                        {if $t == 1}<option value="1" {if isset($type) && $type==1}selected{/if}>{t}QR-Code{/t}</option>{/if}
                                        {if $t == 0}<option value="0" {if isset($type) && $type==0}selected{/if}>{t}kein Barcode{/t}</option>{/if}
                                    {/foreach}
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">{t}Größe:{/t}</label>
                            <div class="col-md-5" >
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
                                    <div class="input-group-append"> <span class="input-group-text" id="basic-addon1">x</span></div>
                                    <input type="number" min="0" class="form-control" placeholder="{t}Höhe{/t}" name="custom_height" required {if !$can_edit_option}disabled{/if} value="{$custom_height}">
                                    <div class="input-group-append"> <span class="input-group-text" id="basic-addon2">mm</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" >{t}Line Preset:{/t}</label>
                            <div class="col-md-10">
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

                        <div class="form-group row">
                            <div class="offset-md-2 col-md-10">
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

    <div class="card mt-3">
        <div class="card-header">
            <a data-toggle="collapse" class="link-collapse text-default" href="#panel-advanced">
                {t}Erweiterte Einstellungen{/t}
            </a>
        </div>
        <div class="card-body card-collapse collapse {if !empty($comment)}in{/if}" id="panel-advanced">
            <ul class="nav nav-tabs">
                <li role="presentation" class="active nav-item"><a href="#tab-text" data-toggle="tab" class="link-anchor nav-link"><i class="fas fa-font"></i> {t}Text{/t}</a></li>
                <li role="presentation" class="nav-item"><a href="#tab-barcode" data-toggle="tab" class="link-anchor nav-link"><i class="fas fa-barcode"></i> {t}Barcode{/t}</a></li>
                {*<li role="presentation"><a href="#">Messages</a></li>*}
            </ul>

            <br>

            <div class="tab-content">

                <div id="tab-text" class="tab-pane fade in active show">
                    <div class="form-horizontal">

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{t}Ausgabemodus:{/t}</label>
                            <div class="col-md-9">
                                <div class="form-check form-check-inline abc-radio">
                                    <input class="form-check-input" type="radio" name="radio_output" value="html" {if $radio_output == "html"}checked{/if} {if !$can_edit_option}disabled{/if} ><label class="form-check-label">{t}HTML{/t}</label>
                                </div>
                                <div class="form-check form-check-inline abc-radio">
                                    <input class="form-check-input" type="radio" name="radio_output" value="text" {if $radio_output == "text"}checked{/if} {if !$can_edit_option}disabled{/if}><label class="form-check-label">{t}Text{/t}</label>
                                </div>
                                <p class="text-muted form-text">{t}Wenn sie die Ausgabe auf Text umstellen, wird die ausgegebene PDF Datei evtl. kleiner, dafür werden evtl. vorhandene HTML Tags als Text dargestellt und nicht interpretiert!{/t}</p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{t}Text-Ausrichtung:{/t}</label>
                            <div class="col-md-9">
                                <div class="form-check form-check-inline abc-radio">
                                    <input class="form-check-input" type="radio" name="text_alignment" value="left" {if $text_alignment == "left"}checked{/if} {if !$can_edit_option}disabled{/if}><label class="form-check-label">{t}Links{/t}</label>
                                </div>
                                <div class="form-check form-check-inline abc-radio">
                                    <input class="form-check-input" type="radio" name="text_alignment" value="center" {if $text_alignment == "center"}checked{/if} {if !$can_edit_option}disabled{/if}><label class="form-check-label">{t}Zentrieren{/t}</label>
                                </div>
                                <div class="form-check form-check-inline abc-radio">
                                    <input class="form-check-input" type="radio" name="text_alignment" value="right" {if $text_alignment == "right"}checked{/if} {if !$can_edit_option}disabled{/if}><label class="form-check-label">{t}Rechts{/t}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{t}Schriftstil:{/t}</label>
                            <div class="col-md-9">
                                <div class="form-check form-check-inline abc-radio">
                                    <input class="form-check-input" type="checkbox" name="text_bold" {if $text_bold}checked{/if} {if !$can_edit_option}disabled{/if}><label class="form-check-label"><b>{t}Fett{/t}</b></label>
                                </div>
                                <div class="form-check form-check-inline abc-radio">
                                    <input class="form-check-input" type="checkbox" name="text_italic" {if $text_italic}checked{/if} {if !$can_edit_option}disabled{/if}><label class="form-check-label"><i>{t}Kursiv{/t}</i></label>
                                </div>
                                <div class="form-check form-check-inline abc-radio">
                                    <input class="form-check-input" type="checkbox" name="text_underline" {if $text_underline}checked{/if} {if !$can_edit_option}disabled{/if}><label class="form-check-label"><u>{t}Unterstrichen{/t}</u></label>
                                </div>
                                <p class="text-muted form-text">{t}Beachten Sie dass sich diese Einstellung auf alle Textzeilen auswirkt!{/t}</p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{t}Schriftgröße:{/t}</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" min="1" max="72" name="text_size" placeholder="8" value="{$text_size}" required {if !$can_edit_option}disabled{/if}>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{t}Benutzerdefinierte Zeilen:{/t}</label>
                            <div class="col-md-9">
                                <textarea rows="4" class="form-control" name="custom_rows" {if !$can_edit_option}disabled{/if}>{$custom_rows}</textarea>
                                <p class="form-text text-muted">{t}Für die Formatierung können HTML-Tags verwendet werden.{/t}</p>
                                <p class="form-text text-muted">{t escape=false}Sie können dynamische Daten wie Uhrzeit oder Bauteilname über Platzhalter der Form %PLATZHALTER% einfügen. Im <a target="_blank" class="link-external" rel="noopener" href="https://github.com/jbtronics/Part-DB/wiki/Labels">Wiki</a> finden Sie eine Übersicht aller möglichen Platzhalter.{/t}</p>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="tab-barcode" class="tab-pane fade">
                    <div class="form-horizontal">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{t}Barcode-Ausrichtung:{/t}</label>
                            <div class="col-md-9">
                                <div class="form-check form-check-inline abc-radio">
                                    <input class="form-check-input" type="radio" name="barcode_alignment" value="left" {if $barcode_alignment == "left"}checked{/if} {if !$can_edit_option}disabled{/if}><label class="form-check-label">{t}Links{/t}</label>
                                </div>
                                <div class="form-check form-check-inline abc-radio">
                                    <input class="form-check-input" type="radio" name="barcode_alignment" value="center" {if $barcode_alignment == "center"}checked{/if} {if !$can_edit_option}disabled{/if}><label class="form-check-label">{t}Zentrieren{/t}</label>
                                </div>
                                <div class="form-check form-check-inline abc-radio">
                                    <input class="form-check-input" type="radio" name="barcode_alignment" value="right" {if $barcode_alignment == "right"}checked{/if} {if !$can_edit_option}disabled{/if}><label class="form-check-label">{t}Rechts{/t}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 control-label">{t}Pfad zu Logo:{/t}</label>
                            <div class="col-md-6">
                                <input name="logo_path" type="text" class="form-control" placeholder="{t}z.B. data/labels/logo.png{/t}" value="{$logo_path}"  {if !$can_edit_option}disabled{/if}>
                                <p class="help-block">{t}Sie können hier einen Pfad zu einem Logo angeben. Dies wird in der unteren linken Ecke auf der Höhe des Barcodes angezeigt. Lassen Sie das Feld leer, um das Logo zu deaktivieren.{/t}</p>
                                <p class="help-block">{t}Um diese Funktion nutzen zu können muss die Imagick oder GD Erweiterung in PHP aktiviert sein.{/t}</p>
                            </div>
                            <div class="col-sm-3 pull-right">
                                <input data-show-caption="false" data-show-preview="false" data-show-upload="false" type="file" class="file" name="logo_file"  {if !$can_edit_option}disabled{/if}>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 col-md-offset-3">
                                <div class="checkbox">
                                    <input name="use_footprint_image" type="checkbox" {if $use_footprint_image}checked{/if} {if !$can_edit_option}disabled{/if}>
                                    <label>{t}Benutze Footprintbild als Icon{/t}</label>
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
                <div class="container-fluid">
                    <p>{t}Ihr Browser unterstützt keine Vorschau von PDF-Dateien. Um die Datei anzusehen, laden Sie die Datei herunter{/t}</p>
                </div>
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