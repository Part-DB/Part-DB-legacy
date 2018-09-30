{locale path="nextgen/locale" domain="partdb"}
<div class="card border-primary">
    <div class="card-header bg-primary text-white">
        <i class="fa fa-file" aria-hidden="true"></i>
        {t}Dateitypen für Dateianhänge{/t}
    </div>
    <div class="card-body">
        <form action="" method="post" class="row no-progbar">
            <div class="col-md-4">

                {if !isset($id) || $id == 0}
                    {assign "can_edit" $can_create}
                    {assign "can_move" $can_create}
                {/if}

                <select class="form-control selectpicker"  data-live-search="true" onChange='$("[name=selected_id]").val(this.value); submitForm(this.form);'>
                    <optgroup label="{t}Neu{/t}">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neuer Dateityp{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$attachement_types_list nofilter}
                    </optgroup>
                </select>

                <hr>

                <select name="selected_id" size="30" class="form-control auto-size-select" onChange="submitForm(this.form);">
                    <optgroup label="{t}Neu{/t}">
                        <option value="0" {if !isset($id) || $id == 0 }selected{/if}>{t}Neuer Dateityp{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$attachement_types_list nofilter}
                    </optgroup>
                </select>
            </div>

            <div class="col-md-8 form-horizontal">
                <fieldset>
                    <legend>
                        {if !isset($id) || $id == 0 }
                            <strong>{t}Neuen Dateityp hinzufügen:{/t}</strong>
                        {else}
                            {if isset($name)}
                                <strong>{t}Dateityp bearbeiten:{/t}</strong> {$name}
                            {else}
                                <strong>{t}Es ist keine Dateityp angewählt!{/t}</strong>
                            {/if}
                        {/if}
                    </legend>

                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="link-anchor active nav-link" data-toggle="tab" href="#home">{t}Standard{/t}</a></li>
                        <li class="nav-item"><a data-toggle="tab" class="link-anchor nav-link" href="#info">{t}Infos{/t}</a></li>
                    </ul>

                    <div class="tab-content">

                        <br>

                        <div id="home" class="tab-pane fade show active">
                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Name*:{/t}</label>
                                <div class="col-md-9">
                                    <input class="form-control" type="text" name="name" value="{$name}" placeholder="{t}z.B. Bilder{/t}" {if !$can_edit}disabled{/if}>
                                    <p class="form-text text-muted">{t}Hinweis: Es empfiehlt sich, die Plural-Form zu verwenden.{/t}</p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Übergeordneter Dateityp*:{/t}</label>
                                <div class="col-md-9">
                                    <select class="form-control selectpicker" data-live-search="true" name="parent_id" size="1" {if !$can_move}disabled{/if}>
                                        {$parent_attachement_types_list nofilter}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Kommentar:{/t}</label>
                                <div class="col-md-9">
                                    <textarea name="comment" class="form-control" rows="5" {if !$can_edit}disabled{/if}
                                              placeholder="{t}z.B. für Vorschaubilder{/t}">{if isset($comment)}{$comment}{/if}</textarea>
                                </div>
                            </div>

                        </div>

                        <div id="info" class="tab-pane fade">
                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}ID:{/t}</label>
                                <div class="col-md-9">
                                    <p class="form-control-plaintext">{if isset($id)}{$id}{else}-{/if}</p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Hinzugefügt:{/t}</label>
                                <div class="col-md-9">
                                    <p class="form-control-plaintext">
                                        {if !empty($datetime_added)}{$datetime_added}{else}-{/if}
                                        {if !empty($creation_user)} {t}durch{/t}
                                            {if $can_visit_user}
                                                <a href="{$relative_path}user_info.php?uid={$creation_user_id}">{$creation_user}</a>
                                            {else}
                                                {$creation_user}
                                            {/if}
                                        {/if}
                                    </p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Letzte Änderung:{/t}</label>
                                <div class="col-md-9">
                                    <p class="form-control-plaintext">
                                        {if !empty($last_modified)}{$last_modified}{else}-{/if}
                                        {if !empty($last_modified_user)} {t}durch{/t}
                                            {if $can_visit_user}
                                                <a href="{$relative_path}user_info.php?uid={$last_modified_user_id}">{$last_modified_user}</a>
                                            {else}
                                                {$last_modified_user}
                                            {/if}
                                        {/if}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-9 offset-md-3">
                            <i>{t}* = Pflichtfelder{/t}</i>
                        </label>
                    </div>

                    <div class="form-group">
                        <div class="col-md-9 offset-md-3">
                            {if !isset($id) || $id == 0 }
                                <button class="btn btn-success" type="submit" name="add" {if !$can_create}disabled{/if}>{t}Neuen Dateityp anlegen{/t}</button>
                                <div class="form-check-dropdown form-check abc-checkbox pl-2 mt-2">
                                    <input class="form-check-input" type="checkbox" name="add_more" {if $add_more}checked{/if} {if !$can_create}disabled{/if}>
                                    <label class="form-check-label">{t}Weitere Dateitypen anlegen{/t}</label>
                                </div>
                            {else}
                                <button class="btn btn-success" type="submit" name="apply" {if !$can_edit && !$can_move}disabled{/if}>{t}Änderungen übernehmen{/t}</button>
                                <button class="btn btn-danger" type="submit" name="delete" {if !$can_delete}disabled{/if}>{t}Dateityp löschen{/t}</button>
                            {/if}
                        </div>
                    </div>
                </fieldset>
            </div>
        </form>
    </div>
</div>
