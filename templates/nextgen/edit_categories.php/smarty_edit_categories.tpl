{locale path="nextgen/locale" domain="partdb"}

{if isset($refresh_navigation_frame) && $refresh_navigation_frame}
    <script type="text/javascript">
        location.reload();
    </script>
{/if}

<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-tags" aria-hidden="true"></i>
        {t}Kategorien{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post" class="row no-progbar">
            <div class="col-md-4">
                <select class="form-control selectpicker"  data-live-search="true" onChange='$("[name=selected_id]").val(this.value); submitForm(this.form);'>
                    <optgroup label="Neu">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neue Kategorie{/t}</option>
                    </optgroup>
                    <optgroup label="Bearbeiten">
                        {$category_list nofilter}
                    </optgroup>
                </select>

                <hr>

                <select class="form-control"  size="30" name="selected_id" onChange='submitForm(this.form)'>
                    <optgroup label="Neu">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neue Kategorie{/t}</option>
                    </optgroup>
                    <optgroup label="Bearbeiten">
                        {$category_list nofilter}
                    </optgroup>
                </select>
            </div>

            <div class="col-md-8 form-horizontal">
                <fieldset>
                    <legend>
                        {if !isset($id) || $id == 0}
                            <strong>{t}Neue Kategorie hinzufügen:{/t}</strong>
                        {else}
                            {if isset($name)}
                                <strong>{t}Kategorie bearbeiten:{/t}</strong>
                            {else}
                                <strong>{t}Es ist keine Kategorie angewählt!{/t}</strong>
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
                            <input type="text" class="form-control" name="name" value="{$name}" required>
                        </div>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="control-label col-md-3">{t}Übergeordnete Kategorie*:{/t}</label>
                    <div class="col-md-9">
                        <select class="form-control selectpicker" data-live-search="true" name="parent_id" size="1">
                            {$parent_category_list nofilter}
                        </select>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label class="control-label col-md-3">{t}Standard Beschreibung:{/t}</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="default_description" value="{$default_description}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3">{t}Standard Kommentar:{/t}</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="default_comment" value="{$default_comment}">
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label class="control-label col-md-3">{t}Footprints deaktivieren:{/t}</label>
                    <div class="col-md-9">
                        <div class="checkbox">
                            <input type="checkbox" name="disable_footprints" {if $disable_footprints}checked{/if} {if isset($parent_disable_footprints) && $parent_disable_footprints}disabled{/if}>
                            <label>{t}Teile in dieser Kategorie (inkl. allen Unterkategorien) können keine Footprints haben{/t}</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">{t}Footprints deaktivieren:{/t}</label>
                        <div class="col-md-9">
                            <div class="checkbox">
                                <input type="checkbox" name="disable_footprints" {if $disable_footprints}checked{/if} {if isset($parent_disable_footprints) && $parent_disable_footprints}disabled{/if}>
                                <label>{t}Teile in dieser Kategorie (inkl. allen Unterkategorien) können keine Footprints haben{/t}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">{t}Hersteller deaktivieren:{/t}</label>
                        <div class="col-md-9">
                            <div class="checkbox">
                                <input type="checkbox" name="disable_manufacturers" {if $disable_manufacturers}checked{/if} {if isset($parent_disable_manufacturers) && $parent_disable_manufacturers}disabled{/if}>
                                <label>{t}Teile in dieser Kategorie (inkl. allen Unterkategorien) können keine Hersteller haben{/t}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3">{t}Automatische erzeugte Bauteileeigenschaften deaktivieren:{/t}</label>
                    <div class="col-md-9">
                        <div class="checkbox">
                            <input type="checkbox" name="disable_properties" {if $disable_properties}checked{/if} {if isset($parent_disable_properties) && $parent_disable_properties}disabled{/if}>
                            <label>{t}Teile in dieser Kategorie (inkl. allen Unterkategorien) haben keine automatisch erzeugten Bauteileigenschaften{/t}</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-9 col-md-offset-3">
                        <i>{t}* = Pflichtfelder{/t}</i>
                    </label>
                </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">{t}Automatische Links zu Datenblättern deaktivieren:{/t}</label>
                        <div class="col-md-9">
                            <div class="checkbox">
                                <input type="checkbox" name="disable_autodatasheets" {if $disable_autodatasheets}checked{/if} {if isset($parent_disable_autodatasheets) && $parent_disable_autodatasheets}disabled{/if}>
                                <label>{t}Teile in dieser Kategorie (inkl. allen Unterkategorien) haben keine automatisch erzeugten Links zu Datenblättern{/t}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-9 col-md-offset-3">
                            <i>{t}* = Pflichtfelder{/t}</i>
                        </label>
                    </div>

                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            {if !isset($id) || $id == 0}
                                <button class="btn btn-success" type="submit" name="add">{t}Neue Kategorie anlegen{/t}</button>
                                <div class="checkbox">
                                    <input type="checkbox" name="add_more" {if $add_more}checked{/if}>
                                    <label>{t}Weitere Kategorien anlegen{/t}</label>
                                </div>
                            {else}
                                <button class="btn btn-success" type="submit" name="apply">{t}Änderungen übernehmen{/t}</button>
                                <button class="btn btn-danger" type="submit" name="delete">{t}Kategorie löschen{/t}</button>
                            {/if}
                        </div>
                    </div>
                </fieldset>
            </div>
        </form>
    </div>
</div>
