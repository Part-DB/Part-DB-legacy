{locale path="nextgen/locale" domain="partdb"}

<script type="text/javascript">
    function switch_series()
    {
        if(document.edit.series.checked)
        {
            document.edit.series_from.disabled=false;
            document.edit.series_to.disabled=false;
        }
        else
        {
            document.edit.series_from.disabled=true;
            document.edit.series_to.disabled=true;
        }
    }
</script>

<div class="card border-primary">
    <div class="card-header bg-primary text-white">
        <i class="fa fa-cube" aria-hidden="true"></i>&nbsp;
        {t}Lagerorte{/t}
    </div>
    <div class="card-body">
        <form action="" method="post" name="edit" class="row no-progbar">
            <div class="col-md-4">

                {if !isset($id) || $id == 0}
                    {assign "can_edit" $can_create}
                    {assign "can_move" $can_create}
                {/if}

                <select class="form-control selectpicker"  data-live-search="true" onChange='$("[name=selected_id]").val(this.value); submitForm(this.form);'>
                    <optgroup label="{t}Neu{/t}">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neuer Lagerort{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$storelocation_list nofilter}
                    </optgroup>
                </select>

                <hr>

                <select name="selected_id" size="30" class="form-control" onChange="submitForm(this.form);">
                    <optgroup label="{t}Neu{/t}">
                        <option value="0" {if isset($id) && $id == 0}selected{/if}>{t}Neuer Lagerort{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$storelocation_list nofilter}
                    </optgroup>
                </select>
            </div>

            <div class="col-md-8 form-horizontal">
                <fieldset>
                    <legend>
                        {if !isset($id) || $id == 0}
                            <strong>{t}Neuen Lagerort hinzufügen:{/t}</strong>
                        {else}
                            {if isset($name)}
                                <strong>{t}Lagerort bearbeiten:{/t}</strong> <a href="show_location_parts.php?lid={$id}&subloc=0">{$name}</a>
                            {else}
                                <strong>{t}Es ist kein Lagerort angewählt!{/t}</strong>
                            {/if}
                        {/if}
                    </legend>

                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="link-anchor nav-link active" data-toggle="tab" href="#home">{t}Standard{/t}</a></li>
                        <li class="nav-item"><a data-toggle="tab" class="link-anchor nav-link" href="#info">{t}Infos{/t}</a></li>
                    </ul>

                    <div class="tab-content">
                        <br>
                        <div id="home" class="tab-pane fade show active">
                            <div class="form-group row">
                                <label class="control-label col-md-3">{t}Name*:{/t}</label>
                                <div class="col-md-9">
                                    <input class="form-control" placeholder="{t}z.B. Aktive Bauteile I{/t}"
                                           type="text" name="name" value="{$name}" required {if !$can_edit}disabled{/if}>
                                </div>
                            </div>

                            {if !isset($id) || $id == 0}
                                <div class="form-group row">
                                    <label class="control-label col-md-3">{t}Serie:{/t}</label>
                                    <div class="col-md-9">
                                        <div class="abc-checkbox form-check form-check-dropdown pl-2 mt-2">
                                            <input class="form-check-input" type="checkbox" name="series" {if isset($series)}checked{/if}
                                                   onclick="switch_series()" {if !$can_edit}disabled{/if}>
                                            <label class="form-check-label">{t}Serie erzeugen{/t}</label>
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <div class="col-md-9 offset-md-3 row">
                                        <p class="col-md-2 form-control-plaintext">{t}von{/t}</p>
                                        <div class="col-md-4">
                                            <input type="number" class="form-control" min="0"  name="series_from" value="{if isset($series_from)}{$series_from}{else}1{/if}" disabled>
                                        </div>
                                        <p class="col-md-2 form-control-plaintext">{t}bis{/t}</p>
                                        <div class="col-md-4">
                                            <input type="number" class="form-control" min="1" name="series_to" value="{if isset($series_to)}{$series_to}{else}3{/if}" disabled>
                                        </div>
                                    </div>

                                </div>

                            {/if}

                            <div class="form-group row">
                                <label class="control-label col-md-3">{t}Übergeordneter Lagerort*:{/t}</label>
                                <div class="col-md-9">
                                    <select name="parent_id" class="form-control selectpicker" data-live-search="true" size="1" {if !$can_move}disabled{/if}>
                                        {$parent_storelocation_list nofilter}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3">{t}Voll:{/t}</label>
                                <div class="col-md-9">
                                    <div class="abc-checkbox form-check form-check-dropdown pl-2 mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_full" {if $is_full}checked{/if} {if !$can_edit}disabled{/if}>
                                        <label class="form-check-label">{t}Diesen Lagerort als "voll" markieren{/t}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3">{t}Kommentar:{/t}</label>
                                <div class="col-md-9">
                                    <textarea name="comment" class="form-control" rows="5" {if !$can_edit}disabled{/if}
                                              placeholder="{t}z.B. blaue Dose{/t}">{if isset($comment)}{$comment}{/if}</textarea>
                                </div>
                            </div>
                        </div>

                        <div id="info" class="tab-pane fade">
                            <div class="form-group row">
                                <label class="control-label col-md-3">{t}ID:{/t}</label>
                                <div class="col-md-9">
                                    <p class="form-control-plaintext">{if isset($id)}{$id}{else}-{/if}</p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3">{t}Hinzugefügt:{/t}</label>
                                <div class="col-md-9">
                                    <p class="form-control-plaintext">{if !empty($datetime_added)}{$datetime_added}{else}-{/if}</p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3">{t}Letzte Änderung:{/t}</label>
                                <div class="col-md-9">
                                    <p class="form-control-plaintext">{if !empty($last_modified)}{$last_modified}{else}-{/if}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-9 offset-md-3">
                            <i>{t}* = Pflichtfelder{/t}</i>
                        </label>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-9 offset-md-3">
                            {if !isset($id) || $id == 0}
                                <button type="submit" class="btn btn-success" name="add" {if !$can_create}disabled{/if}>{t}Neuen Lagerort anlegen{/t}</button>
                                <div class="form-check-dropdown form-check abc-checkbox pl-2 mt-2">
                                    <input type="checkbox" name="add_more" {if $add_more}checked{/if} {if !$can_create}disabled{/if}>
                                    <label>{t}Weitere Lagerorte anlegen{/t}</label>
                                </div>
                            {else}
                                <button type="submit" class="btn btn-success" name="apply" {if !$can_edit && !$can_move}disabled{/if}>{t}Änderungen übernehmen{/t}</button>
                                <button type="submit" class="btn btn-danger" name="delete" {if !$can_delete}disabled{/if}>{t}Lagerort löschen{/t}</button>
                            {/if}
                        </div>
                    </div>
                </fieldset>
            </div>
        </form>
    </div>
</div>
