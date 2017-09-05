{locale path="nextgen/locale" domain="partdb"}

{if isset($refresh_navigation_frame) && $refresh_navigation_frame}
    <script type="text/javascript">
        AjaxUI.getInstance().updateTrees();
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
                                <strong>{t}Kategorie bearbeiten:{/t} <a href="show_category_parts.php?cid={$id}&subcat=0">{$name}</a></strong>
                            {else}
                                <strong>{t}Es ist keine Kategorie angewählt!{/t}</strong>
                            {/if}
                        {/if}
                    </legend>

                    <ul class="nav nav-tabs">
                        <li class="active"><a class="link-anchor" data-toggle="tab" href="#home">{t}Standard{/t}</a></li>
                        <li><a data-toggle="tab" class="link-anchor" href="#menu1">{t}Optionen{/t}</a></li>
                        <li><a data-toggle="tab" class="link-anchor" href="#menu2">{t}Erweitert{/t}</a></li>
                    </ul>

                    <div class="tab-content">

                        <br>

                        <div id="home" class="tab-pane fade in active">
                            <div class="form-group">
                                <label class="control-label col-md-3">{t}ID:{/t}</label>
                                <div class="col-md-9">
                                    <p class="form-control-static">{if isset($id)}{$id}{else}-{/if}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">{t}Name*:{/t}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="name" value="{$name}" placeholder="{t}z.B. Kondensatoren{/t}" required>
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

                        </div>

                        <div id="menu2" class="tab-pane fade">

                            <br>

                            <div class="form-group">
                                <label class="control-label col-md-3">{t}Filter für Bauteilenamen (RegEx):{/t}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="partname_regex" value="{$partname_regex}"
                                           placeholder="{if !empty($partname_regex_parent)}{$partname_regex_parent}{else}{t}z.B. /([^\/]+)/(^\/]+)/@f$Kapazität$Spannung{/t}{/if}"
                                            pattern="{$partname_input_pattern}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">{t}Hinweis für Bauteilenamen:{/t}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="partname_hint" value="{$partname_hint}"
                                           placeholder="{if !empty($partname_hint_parent)}{$partname_hint_parent}{else}{t}z.B. Kapazität/Spannung{/t}{/if}">
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label class="control-label col-md-3">{t}Standard Beschreibung:{/t}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="default_description" value="{$default_description}"
                                           placeholder="{if !empty($default_description_parent)}{$default_description_parent}{else}{t}z.B. Durchmesser: ,Höhe:{/t}{/if}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">{t}Standard Kommentar:{/t}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="default_comment" value="{$default_comment}"
                                           placeholder="{if !empty($default_comment_parent)}{$default_comment_parent}{else}{t}z.B. RM:{/t}{/if}">
                                </div>
                            </div>

                        </div>


                        <div id="menu1" class="tab-pane fade">

                            <br>

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
                                <label class="control-label col-md-3">{t}Automatische erzeugte Bauteileeigenschaften deaktivieren:{/t}</label>
                                <div class="col-md-9">
                                    <div class="checkbox">
                                        <input type="checkbox" name="disable_properties" {if $disable_properties}checked{/if} {if isset($parent_disable_properties) && $parent_disable_properties}disabled{/if}>
                                        <label>{t}Teile in dieser Kategorie (inkl. allen Unterkategorien) haben keine automatisch erzeugten Bauteileigenschaften{/t}</label>
                                    </div>
                                </div>
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
