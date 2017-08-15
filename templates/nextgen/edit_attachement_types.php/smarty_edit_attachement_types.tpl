{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-file" aria-hidden="true"></i> 
        {t}Dateitypen für Dateianhänge{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post" class="row no-progbar">
            <div class="col-md-4">

                <select class="form-control selectpicker"  data-live-search="true" onChange='$("[name=selected_id]").val(this.value); submitForm(this.form);'>
                    <optgroup label="Neu">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neuer Dateityp{/t}</option>
                    </optgroup>
                    <optgroup label="Bearbeiten">
                        {$attachement_types_list nofilter}
                    </optgroup>
                </select>

                <hr>

                <select name="selected_id" size="30" class="form-control auto-size-select" onChange="submitForm(this.form);">
                    <optgroup label="Neu">
                        <option value="0" {if !isset($id) || $id == 0 }selected{/if}>{t}Neuer Dateityp{/t}</option>
                    </optgroup>
                    <optgroup label="Bearbeiten">
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
                                <strong>{t}Dateityp bearbeiten:{/t} {$name}</strong>
                            {else}
                                <strong>{t}Es ist keine Dateityp angewählt!{/t}</strong>
                            {/if}
                        {/if}
                    </legend>

                    <div class="form-group">
                        <label class="control-label col-md-3">{t}ID:{/t}</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{if isset($id)}{$id}{else}-{/if}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">{t}Name*:{/t}</label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" name="name" value="{$name}" placeholder="{t}z.B. Bilder{/t}">
                            <p class="help-block">{t}Hinweis: Es empfiehlt sich, die Plural-Form zu verwenden.{/t}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">{t}Übergeordneter Dateityp*:{/t}</label>
                        <div class="col-md-9">
                            <select class="form-control selectpicker" data-live-search="true" name="parent_id" size="1">
                                {$parent_attachement_types_list nofilter}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-9 col-md-offset-3">
                            <i>{t}* = Pflichtfelder{/t}</i>
                        </label>
                    </div>

                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            {if !isset($id) || $id == 0 }
                                <button class="btn btn-success" type="submit" name="add">{t}Neuen Dateityp anlegen{/t}</button>
                                <div class="checkbox">
                                    <input type="checkbox" name="add_more" {if $add_more}checked{/if}>
                                    <label>{t}Weitere Dateitypen anlegen{/t}</label>
                                </div>
                            {else}
                                <button class="btn btn-success" type="submit" name="apply">{t}Änderungen übernehmen{/t}</button>
                                <button class="btn btn-danger" type="submit" name="delete">{t}Dateityp löschen{/t}</button>
                            {/if}
                        </div>
                    </div>
                </fieldset>
            </div>
        </form>
    </div>
</div>
