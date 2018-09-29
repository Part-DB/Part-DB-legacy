{locale path="nextgen/locale" domain="partdb"}
<div class="card border-primary">
    <div class="card-header bg-primary text-white">
        <i class="fa fa-industry" aria-hidden="true"></i>
        {t}Hersteller{/t}
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
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neuer Hersteller:{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$manufacturer_list nofilter}
                    </optgroup>
                </select>

                <hr>

                <select name="selected_id" size="40" class="form-control" onChange="submitForm(this.form);">
                    <optgroup label="{t}Neu{/t}">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neuer Hersteller{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$manufacturer_list nofilter}
                    </optgroup>
                </select>
            </div>

            <div class="col-md-8 form-horizontal">
                <fieldset>
                    <legend>
                        {if !isset($id) || $id == 0}
                            <strong>{t}Neuen Hersteller hinzufügen:{/t}</strong>
                        {else}
                            {if isset($name)}
                                <strong>{t}Hersteller bearbeiten:{/t}</strong> <a href="show_manufacturer_parts.php?mid={$id}&subman=0">{$name}</a>
                            {else}
                                <strong>{t}Es ist kein Hersteller angewählt!{/t}</strong>
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
                                <label class="col-md-3 col-form-label">{t}Name*:{/t}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="name" value="{if isset($name)}{$name}{/if}" placeholder="{t}z.B. ACME AG{/t}"
                                           required {if !$can_edit}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{t}Übergeordneter Hersteller*:{/t}</label>
                                <div class="col-md-9">
                                    <select name="parent_id" data-live-search="true" size="1" class="form-control selectpicker" {if !$can_move}disabled{/if}>
                                        {$parent_manufacturer_list nofilter}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{t}Adresse:{/t}</label>
                                <div class="col-md-9">
                        <textarea name="address" class="form-control" rows="5" placeholder="{t}z.B. Musterstraße 1{/t}"
                                  {if !$can_edit}disabled{/if}>{if isset($address)}{$address|escape}{/if}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{t}Telefonnummer:{/t}</label>
                                <div class="col-md-9">
                                    <input type="tel" name="phone_number" class="form-control" placeholder="{t}z.B. (030) 12345 67{/t}"
                                           value="{if isset($phone_number)}{$phone_number|escape}{/if}" {if !$can_edit}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{t}Faxnummer:{/t}</label>
                                <div class="col-md-9">
                                    <input type="tel" class="form-control" name="fax_number" placeholder="{t}z.B. (030) 12345 67{/t}"
                                           value="{if isset($fax_number)}{$fax_number}{/if}" {if !$can_edit}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{t}E-Mail Adresse:{/t}</label>
                                <div class="col-md-9">
                                    {if isset($email_address)}
                                        <a href="mailto:{$email_address}">{$email_address}</a><br>
                                    {/if}
                                    <input type="email" name="email_address" class="form-control" placeholder="{t}z.B. contact@foo.bar{/t}"
                                           value="{if isset($email_address)}{$email_address}{/if}" {if !$can_edit}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{t}Webseite:{/t}</label>
                                <div class="col-md-9">
                                    {if isset($website)}
                                        <a href="{$website}" target="_blank" rel="noopener">{$website}</a><br>
                                    {/if}
                                    <input type="url" class="form-control" name="website" placeholder="{t}z.B. www.foo.bar{/t}"
                                           value="{if isset($website)}{$website}{/if}" {if !$can_edit}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{t}Artikel-Direktlink:{/t}</label>
                                <div class="col-md-9">
                                    <input type="url" class="form-control" name="auto_product_url" placeholder="{t}z.B. www.foo.bar/%PARTNUMBER%{/t}"
                                           value="{if isset($auto_product_url)}{$auto_product_url}{/if}" {if !$can_edit}disabled{/if}>
                                    <p class="form-text text-muted">{t}Platzhalter für die Bestellnummer:{/t} <i>%PARTNUMBER%</i></p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Kommentar:{/t}</label>
                                <div class="col-md-9">
                                    <textarea name="comment" class="form-control" rows="5" {if !$can_edit}disabled{/if}
                                              placeholder="{t}z.B. Kundennummer: xxxx{/t}">{if isset($comment)}{$comment}{/if}</textarea>
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
                                    <p class="form-control-plaintext">{if !empty($datetime_added)}{$datetime_added}{else}-{/if}</p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Letzte Änderung:{/t}</label>
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
                        <div class="col-md-12 offset-md-3">
                            {if !isset($id) || $id == 0}
                                <button class="btn btn-success" type="submit" name="add" {if !$can_create}disabled{/if}>{t}Neuen Hersteller anlegen{/t}</button>
                                <div class="form-check-dropdown form-check abc-checkbox pl-2 mt-2">
                                    <input class="form-check-input" type="checkbox" name="add_more" {if isset($add_more) && $add_more}checked{/if} {if !$can_delete}disabled{/if}>
                                    <label class="form-check-label">{t}Weitere Hersteller anlegen{/t}</label>
                                </div>
                            {else}
                                <button class="btn btn-success" type="submit" name="apply" {if !$can_edit && !$can_move}disabled{/if}>{t}Änderungen übernehmen{/t}</button>
                                <button class="btn btn-danger" type="submit" name="delete" {if !$can_delete}disabled{/if}>{t}Hersteller löschen{/t}</button>
                            {/if}
                        </div>

                    </div>

                </fieldset>
            </div>
        </form>
    </div>
</div>
