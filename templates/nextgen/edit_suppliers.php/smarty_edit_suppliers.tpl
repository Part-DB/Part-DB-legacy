{locale path="nextgen/locale" domain="partdb"}
<div class="card border-primary">
    <div class="card-header bg-primary text-white">
        <i class="fa fa-truck" aria-hidden="true"></i>
        {t}Lieferanten{/t}
    </div>
    <div class="card-body">
        <form action="" method="post" class="row no-progbar">

            {if !isset($id) || $id == 0}
                {assign "can_edit" $can_create}
                {assign "can_move" $can_create}
            {/if}

            <div class="col-md-4">
                <select class="form-control selectpicker"  data-live-search="true" onChange='$("[name=selected_id]").val(this.value); submitForm(this.form);'>
                    <optgroup label="{t}Neu{/t}">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neuer Lieferant{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$supplier_list nofilter}
                    </optgroup>
                </select>

                <hr>

                <select name="selected_id" size="40" class="form-control" onChange="submitForm(this.form);">
                    <optgroup label="{t}Neu{/t}">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neuer Lieferant{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$supplier_list nofilter}
                    </optgroup>
                </select>
            </div>

            <div class="col-md-8 form-horizontal">
                <fieldset>
                    <legend>
                        {if !isset($id) || $id == 0}
                            <strong>{t}Neuen Lieferanten hinzufügen:{/t}</strong>
                        {else}
                            {if !empty($name)}
                                <strong>{t}Lieferant bearbeiten:{/t}</strong> <a href="show_supplier_parts.php?sid={$id}&subsup=0">{$name}</a>
                            {else}
                                <strong>{t}Es ist kein Lieferant angewählt!{/t}</strong>
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
                                <label class="col-md-3 col-form-label">{t}Name*:{/t}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="name" value="{if isset($name)}{$name}{/if}"
                                           placeholder="{t}z.B. ACME AG{/t}" required {if !$can_edit}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{t}Übergeordneter Lieferant*:{/t}</label>
                                <div class="col-md-9">
                                    <select name="parent_id" size="1" class="form-control selectpicker" data-live-search="true" {if !$can_move}disabled{/if}>
                                        {$parent_supplier_list nofilter}
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
                                    <input type="tel" name="phone_number" class="form-control" value="{if isset($phone_number)}{$phone_number|escape}{/if}"
                                           placeholder="{t}z.B. (030) 12345 67{/t}" {if !$can_edit}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{t}Faxnummer:{/t}</label>
                                <div class="col-md-9">
                                    <input type="tel" class="form-control" name="fax_number" value="{if isset($fax_number)}{$fax_number}{/if}"
                                           placeholder="{t}z.B. (030) 12345 67{/t}" {if !$can_edit}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{t}E-Mail Adresse:{/t}</label>
                                <div class="col-md-9">
                                    {if isset($email_address)}
                                        <a href="mailto:{$email_address}">{$email_address}</a><br>
                                    {/if}
                                    <input type="email" name="email_address" class="form-control" value="{if isset($email_address)}{$email_address}{/if}"
                                           placeholder="{t}z.B. contact@foo.bar{/t}" {if !$can_edit}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{t}Webseite:{/t}</label>
                                <div class="col-md-9">
                                    {if isset($website)}
                                        <a href="{$website}" target="_blank" rel="noopener">{$website}</a><br>
                                    {/if}
                                    <input type="url" class="form-control" name="website" value="{if isset($website)}{$website}{/if}"
                                           placeholder="{t}z.B. www.foo.bar{/t}" {if !$can_edit}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{t}Artikel-Direktlink:{/t}</label>
                                <div class="col-md-9">
                                    <input type="url" class="form-control" name="auto_product_url" value="{if isset($auto_product_url)}{$auto_product_url}{/if}"
                                           placeholder="{t}z.B. www.foo.bar/%PARTNUMBER%{/t}" {if !$can_edit}disabled{/if}>
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

                    <div class="form-group row">
                        <label class="col-md-9 offset-md-3">
                            <i>{t}* = Pflichtfelder{/t}</i>
                        </label>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-9 offset-md-3">
                            {if !isset($id) || $id == 0}
                                <button class="btn btn-success" type="submit" name="add" {if !$can_create}disabled{/if}>{t}Neuen Lieferanten anlegen{/t}</button>
                                <div class="form-check-dropdown form-check abc-checkbox pl-2 mt-2">
                                    <input class="form-check-input" type="checkbox" name="add_more" {if $add_more}checked{/if} {if !$can_create}disabled{/if}>
                                    <label class="form-check-label">{t}Weitere Lieferanten anlegen{/t}</label>
                                </div>
                            {else}
                                <button class="btn btn-success" type="submit" name="apply" {if !$can_edit && !$can_move}disabled{/if}>{t}Änderungen übernehmen{/t}</button>
                                <button class="btn btn-danger" type="submit" name="delete" {if !$can_delete}disabled{/if}>{t}Lieferant löschen{/t}</button>
                            {/if}
                        </div>
                    </div>
                </fieldset>
            </div>
        </form>
    </div>
</div>
