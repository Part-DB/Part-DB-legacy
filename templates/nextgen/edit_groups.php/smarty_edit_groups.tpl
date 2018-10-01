{locale path="nextgen/locale" domain="partdb"}
{if isset($refresh_navigation_frame) && $refresh_navigation_frame}
    <script type="text/javascript">
        AjaxUI.getInstance().updateTrees();
    </script>
{/if}

{if !isset($id) || $id == 0}
    {assign "can_edit" $can_create}
    {assign "can_move" $can_create}
{/if}

<div class="card border-primary">
    <div class="card-header bg-primary text-white">
        <i class="fa fa-users" aria-hidden="true"></i>
        {t}Gruppen{/t}
    </div>
    <div class="card-body">
        <form action="" method="post" class="row no-progbar">
            <div class="col-md-4">

                <select class="form-control selectpicker"  data-live-search="true" onChange='$("[name=selected_id]").val(this.value); submitForm(this.form);'>
                    <optgroup label="{t}Neu{/t}">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neue Gruppe{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$group_list nofilter}
                    </optgroup>
                </select>

                <hr>

                <select name="selected_id" size="30" class="form-control" onChange="submitForm(this.form);">
                    <optgroup label="{t}Neu{/t}">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neue Gruppe{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$group_list nofilter}
                    </optgroup>
                </select>
            </div>

            <div class="col-md-8 form-horizontal">
                <fieldset>
                    <legend>
                        {if !isset($id) || $id == -1}
                            <strong>{t}Neue Gruppe hinzufügen:{/t}</strong>
                        {else}
                            {if isset($name)}
                                <strong>{t}Gruppe bearbeiten:{/t}</strong> {$name}
                            {else}
                                <strong>{t}Es ist keine Gruppe angewählt!{/t}</strong>
                            {/if}
                        {/if}
                    </legend>

                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="link-anchor active nav-link" data-toggle="tab" href="#home">{t}Allgemein{/t}</a></li>
                        <li class="nav-item"><a data-toggle="tab" class="link-anchor nav-link" href="#permissions">{t}Berechtigungen{/t}</a></li>
                        <li class="nav-item"><a data-toggle="tab" class="link-anchor nav-link" href="#info">{t}Infos{/t}</a></li>
                    </ul>

                    <div class="tab-content">
                        <br>
                        <div id="home" class="tab-pane fade in active show">
                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Gruppenname*:{/t}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="name" value="{$name}"
                                           placeholder="{t}z.B. admins{/t}" required {if !$can_edit}disabled{/if}>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}übergeordnete Gruppe*:{/t}</label>
                                <div class="col-md-9">
                                    <select class="form-control selectpicker" data-live-search="true"
                                            name="parent_id" size="1" {if !$can_move}disabled{/if}>
                                        {$parent_group_list nofilter}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">{t}Kommentar:{/t}</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" name="comment" rows="4"
                                              placeholder="{t}z.B. für Administratoren{/t}" {if !$can_edit}disabled{/if}
                                    >{if isset($comment)}{$comment}{/if}</textarea>
                                </div>
                            </div>


                        </div>

                        <div id="permissions" class="tab-pane fade">
                            {include file='../smarty_permissions.tpl'}
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

                        <div class="form-group row">
                            <label class="col-md-9 offset-md-3">
                                <i>* = {t}Pflichtfelder{/t}</i>
                            </label>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                {if !isset($id) || $id == 0}
                                    <button class="btn btn-success" type="submit" name="add" {if !$can_create}disabled{/if}>{t}Neue Gruppe anlegen{/t}</button>
                                    <div class="abc-checkbox form-check form-check-dropdown pl-2 mt-2">
                                        <input class="form-check-input" type="checkbox" name="add_more" {if $add_more}checked{/if} {if !$can_create}disabled{/if}>
                                        <label class="form-check-label">{t}Weitere Gruppe anlegen{/t}</label>
                                    </div>
                                {else}
                                    <button class="btn btn-success" type="submit" name="apply"
                                            {if !$can_move && !$can_edit && !$can_permission}disabled{/if}>
                                        {t}Änderungen übernehmen{/t}</button>
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
