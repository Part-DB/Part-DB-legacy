{locale path="nextgen/locale" domain="partdb"}
{if isset($refresh_navigation_frame) && $refresh_navigation_frame}
    <script type="text/javascript">
        AjaxUI.getInstance().updateTrees();
    </script>
{/if}

{if !isset($id) || $id == -1}
    {assign "can_infos" $can_create}
    {assign "can_username" $can_create}
{/if}

<div class="card border-primary">
    <div class="card-header bg-primary text-white">
        <i class="fa fa-user" aria-hidden="true"></i>
        {t}Benutzer{/t}
    </div>
    <div class="card-body">
        <form action="" method="post" class="row no-progbar">
            <div class="col-md-4">

                <select class="form-control selectpicker"  data-live-search="true" onChange='$("[name=selected_id]").val(this.value); submitForm(this.form);'>
                    <optgroup label="{t}Neu{/t}">
                        <option value="-1" {if !isset($id) || $id == -1}selected{/if}>{t}Neuer Benutzer{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$user_list nofilter}
                    </optgroup>
                </select>

                <hr>

                <select name="selected_id" size="30" class="form-control" onChange="submitForm(this.form);">
                    <optgroup label="{t}Neu{/t}">
                        <option value="-1" {if !isset($id) || $id == -1}selected{/if}>{t}Neuer Benutzer{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$user_list nofilter}
                    </optgroup>
                </select>
            </div>

            <div class="col-md-8 form-horizontal">
                <fieldset>
                    <legend>
                        {if !isset($id) || $id == -1}
                            <strong>{t}Neuen Benutzer hinzufügen:{/t}</strong>
                        {else}
                            {if isset($name)}
                                <strong>{t}Benutzer bearbeiten:{/t}</strong> {$name}
                            {else}
                                <strong>{t}Es ist kein Benutzer angewählt!{/t}</strong>
                            {/if}
                        {/if}
                    </legend>

                    <ul class="nav nav-tabs">
                        <li class="active nav-item"><a class="link-anchor active nav-link" data-toggle="tab" href="#home">{t}Allgemein{/t}</a></li>
                        <li class="nav-item"><a data-toggle="tab" class="link-anchor nav-link" href="#permissions">{t}Berechtigungen{/t}</a></li>
                        <li class="nav-item"><a data-toggle="tab" class="link-anchor nav-link" href="#password">{t}Passwort setzen{/t}</a></li>
                        <li class="nav-item"><a data-toggle="tab" class="link-anchor nav-link" href="#config">{t}Konfiguration{/t}</a></li>
                        <li class="nav-item"><a data-toggle="tab" class="link-anchor nav-link" href="#info">{t}Infos{/t}</a></li>
                    </ul>

                    <div class="tab-content">
                        <br>
                        <div id="home" class="tab-pane fade in active show">

                            {if $no_password}
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <b>{t}Der gewählte Nutzer hat bisher noch kein Password und kann sich daher nicht einloggen{/t}</b>
                                        <p>{t}Um ein Password zu setzen, gehen sie in den Reiter "Password setzen"{/t}</p>
                                    </div>
                                </div>
                            {/if}

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Benutzername*:{/t}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="name" value="{$name}"
                                           placeholder="{t}z.B. m.muster{/t}" required {if !$can_username}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Gruppe*:{/t}</label>
                                <div class="col-md-9">
                                    <select class="form-control selectpicker" data-live-search="true"
                                            name="group_id" size="1" {if !$can_group}disabled{/if}>
                                        {$group_list nofilter}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Vorname:{/t}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="first_name" value="{if isset($first_name)}{$first_name}{/if}"
                                           placeholder="{t}z.B. Max{/t}" {if !$can_infos}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Nachname:{/t}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="last_name" value="{if isset($last_name)}{$last_name}{/if}"
                                           placeholder="{t}z.B. Muster{/t}" {if !$can_infos}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Email:{/t}</label>
                                <div class="col-md-9">
                                    <input type="email" class="form-control" name="email" value="{if isset($email)}{$email}{/if}"
                                           placeholder="{t}z.B. m.muster@ecorp.com{/t}" {if !$can_infos}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Abteilung:{/t}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="department" value="{if isset($first_name)}{$department}{/if}"
                                           placeholder="{t}z.B. Entwicklung{/t}" {if !$can_infos}disabled{/if}>
                                </div>
                            </div>
                        </div>

                        <div id="permissions" class="tab-pane fade">
                            {if isset($is_current_user) && $is_current_user}
                                <p><strong>{t}Achtung:{/t}</strong>
                                    {t}Aus Sicherheitsgründen darf ein Benutzer seine eigenen Berechtigungen bezüglich Benutzern und Gruppen nicht bearbeiten!{/t}</p>
                            {/if}
                            {include file='../smarty_permissions.tpl'}
                        </div>


                        <div id="password" class="tab-pane fade">

                            <div class="form-group row">
                                <div class="col-md-9 offset-md-3">
                                    <p class="form-text text-muted">{t}Füllen sie die folgenden Felder aus, um dem Nutzer ein neues Password zu setzen!{/t}</p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Neues Password:{/t}</label>
                                <div class="col-md-9">
                                    <input type="password" class="form-control" name="password_1" value=""
                                           placeholder="{t}Neues Password{/t}" {if !$can_password}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Password Bestätigung:{/t}</label>
                                <div class="col-md-9">
                                    <input type="password" class="form-control" name="password_2" value=""
                                           placeholder="{t}Password Bestätigung{/t}" {if !$can_password}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-9 offset-md-3">
                                    <div class="form-check abc-checkbox form-check-inline">
                                        <input type="checkbox" class="form-check-input"
                                               name="must_change_pw" {if !$can_password}disabled{/if} checked>
                                        <label class="form-check-label">{t}Benutzer muss Passwort nach Login ändern{/t}</label>
                                    </div>
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

                        <div id="config" class="tab-pane fade">
                            <div class="form-group row">
                                <label class="col-form-label col-md-3" for="custom_css">{t}Theme:{/t}</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="custom_css" {if !$can_config}disabled{/if}>
                                        <option value="">{t}Benutze das serverweite Theme{/t}</option>
                                        {foreach $custom_css_loop as $css}
                                            <option value="{$css.value}" {if $css.selected}selected{/if}>{$css.text}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3" for="timezon">{t}Zeitzone:{/t}</label>
                                <div class="col-sm-9">
                                    <select class="form-control selectpicker" data-live-search="true" name="timezone" {if !$can_config}disabled{/if}>
                                        <option value="">{t}Benutze die serverweite Zeitzone{/t}</option>
                                        {foreach $timezone_loop as $timezone}
                                            <option value="{$timezone.value}" {if $timezone.selected}selected{/if}>{$timezone.text}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3" for="language">{t}Sprache:{/t}</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="language" {if !$can_config}disabled{/if}>
                                        <option value="">{t}Benutze die serverweite Sprache{/t}</option>
                                        {foreach $language_loop as $lang}
                                            <option value="{$lang.value}" {if $lang.selected}selected{/if}>{$lang.text}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="col-md-9 offset-md-3">
                                <i>* = {t}Pflichtfelder{/t}</i>
                            </label>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                {if !isset($id) || $id == -1}
                                    <button class="btn btn-success" type="submit" name="add" {if !$can_create}disabled{/if}>
                                        {t}Neuen Benutzer anlegen{/t}</button>
                                    <div class="form-check-dropdown form-check abc-checkbox pl-2 mt-2">
                                        <input class="form-check-input" type="checkbox" name="add_more" {if $add_more}checked{/if} {if !$can_create}disabled{/if}>
                                        <label class="form-check-label">{t}Weiteren Benutzer anlegen{/t}</label>
                                    </div>
                                {else}
                                    <button class="btn btn-success" type="submit" name="apply">{t}Änderungen übernehmen{/t}</button>
                                    <button class="btn btn-danger" type="submit" name="delete" {if !$can_delete}disabled{/if}>
                                        {t}Benutzer löschen{/t}</button>
                                {/if}
                            </div>
                        </div>
                </fieldset>
            </div>
        </form>
    </div>
</div>
