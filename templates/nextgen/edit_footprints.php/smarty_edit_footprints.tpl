{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-microchip" aria-hidden="true"></i>
        {t}Footprints{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post" class="row no-progbar">

            <div class="col-md-4 auto-size-select">

                <select class="form-control selectpicker"  data-live-search="true" onChange='$("[name=selected_id]").val(this.value); submitForm(this.form);'>
                    <optgroup label="Neu">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neuer Footprint{/t}</option>
                    </optgroup>
                    <optgroup label="Bearbeiten">
                        {$footprint_list nofilter}
                    </optgroup>
                </select>

                <hr>

                <select name="selected_id" size="30" class="form-control auto-size-select" onChange="submitForm(this.form);">
                    <optgroup label="Neu">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neuer Footprint{/t}</option>
                    </optgroup>
                    <optgroup label="Bearbeiten">
                        {$footprint_list nofilter}
                    </optgroup>
                </select>
            </div>

            <div class="col-md-8 form-horizontal">
                <fieldset>
                    <legend>
                        {if !isset($id) || $id == 0}
                            <strong>{t}Neuer Footprint hinzufügen:{/t}</strong>
                        {else}
                            {if isset($name)}
                                <strong>{t}Footprint bearbeiten:{/t} <a href="show_footprint_parts.php?fid={$id}&subfoot=0">{$name}</a></strong>
                            {else}
                                <strong>{t}Es ist kein Footprint angewählt!{/t}</strong>
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
                            <input type="text" class="form-control" name="name" value="{$name}" placeholder="{t}z.B. DIP8{/t}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">{t}Übergeordneter Footprint*:{/t}</label>
                        <div class="col-md-9">
                            <select class="form-control selectpicker" data-live-search="true" name="parent_id" size="1">
                                {$parent_footprint_list nofilter}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">{t}Bild:{/t}</label>
                        <div class="col-md-9">
                            <input type="text" name="filename" value="{$filename}" placeholder="{t}z.B. img/footprints/Aktiv/ICs/DIP/IC_DIP8.png{/t}" class="form-control">
                            <p class="help-block">{t}Hinweis: Sie können hier z.B. "DIP28" eintippen und übernehmen. Der Footprint wird dann unter "Footprints mit fehlerhaften Dateinamen" aufgelistet, wo Sie Vorschläge für Dateinamen bekommen und dann einfach übernehmen können.{/t}</p>
                            {if !empty($filename) && $filename_valid}
                                <img class="" rel="popover" height="70" src="{$filename}">
                            {/if}
                        </div>
                    </div>

                    {if $foot3d_active}
                        <div class="form-group">
                            <label class="control-label col-md-3">{t}3D-Footprint:{/t}</label>
                            <div class="col-md-9">
                                <input type="text" name="filename_3d" value="{$filename_3d}" placeholder="{t}z.B. models/Housings_DIP/DIP-8_W7.62mm.x3d{/t}" class="form-control">
                                <p></p>
                                {if !empty($filename_3d) && $filename_3d_valid}
                                    <x3d id="foot3d" class="img-thumbnail" height="150" width="500" >
                                        <scene >
                                            <!-- <Viewpoint id="front" position="0 0 10" orientation="-0.01451 0.99989 0.00319 3.15833" description="camera"></Viewpoint> -->
                                            <transform>
                                                <inline url="{$filename_3d}"> </inline>
                                            </transform>
                                        </scene>
                                        <!-- <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#fullscreen"><i class="fa fa-arrows-alt" aria-hidden="true"></i></button> -->
                                    </x3d>
                                {/if}
                            </div>
                        </div>
                    {/if}

                    <div class="form-group">
                        <label class="col-md-9 col-md-offset-3">
                            <i>{t}* = Pflichtfelder{/t}</i>
                        </label>
                    </div>

                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            {if !isset($id) || $id == 0}
                                <button class="btn btn-success" type="submit" name="add">{t}Neuer Footprint anlegen{/t}</button>
                                <div class="checkbox">
                                    <input type="checkbox" name="add_more" {if $add_more}checked{/if}>
                                    <label>{t}Weitere Footprints anlegen{/t}</label>
                                </div>
                            {else}
                                <button class="btn btn-success" type="submit" name="apply">{t}Änderungen übernehmen{/t}</button>
                                <button class="btn btn-danger" type="submit" name="delete">{t}Footprint löschen{/t}</button>
                            {/if}
                        </div>
                    </div>
                </fieldset>
            </div>
        </form>
    </div>
</div>

{if isset($broken_filename_footprints) && $broken_filename_footprints}
    <div class="panel panel-danger">
        <div class="panel-heading">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            {t}Footprints mit fehlerhaften Dateinamen{/t} ({$broken_footprints_count}/{$broken_footprints_count_total})
        </div>
        <div class="panel-body">
            {t}Die Dateinamen der folgenden Footprints konnten keiner Bilddatei zugeordnet werden. Bitte überprüfen bzw. korrigieren Sie die vorgeschlagenen Dateien, um sie dann zu übernehmen. Bereits gesetzte Haken bedeuten, dass für die jeweiligen Footprints exakt gleichnamige Dateien gefunden wurden.{/t}
            <form action="" method="post" >
                <div class="row">
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th>{t}Footprint{/t}</th>
                            <th>{t}Fehlerhafter Dateiname{/t}</th>
                            <th>{t}Vorgeschlagene Dateinamen{/t}</th>
                        </tr>
                        </thead>

                        <tbody>

                        {foreach $broken_filename_footprints as $fp}
                        <!--the alternating background colors are created here-->
                        <tr>

                            <input type="hidden" name="broken_footprint_id_{$fp.index}" value="{$fp.broken_id}">

                            <!--checkbox + footprint name-->
                            <td class="tdrow0">
                                <div class="checkbox">
                                    <input type="checkbox" {if $fp.checked}checked {/if}
                                           name="filename_checkbox_{$fp.index}">
                                    <label>{$fp.broken_full_path}</label>
                                </div>
                            </td>

                            <!--broken filename-->
                            <td class="tdrow1 form-group">
                                <p class="form-control-static text-danger">{$fp.broken_filename}</p>
                            </td>

                            <!--proposed filenames-->
                            <td class="tdrow0 form-horizontal">
                                <label class="col-md-1 control-label">({$fp.proposed_filenames_count})</label>
                                {if $fp.proposed_filenames_count > 0}
                                    <div class="col-md-11">
                                        <select class="form-control selectpicker" data-live-search="true" name="proposed_filename_{$fp.index}">
                                            <option value="">{t}Dateiname löschen und später selber von Hand setzen.{/t}</option>
                                            {foreach $fp.proposed_filenames as $filename}
                                                <option {if $filename.selected}selected{/if} value="{$filename.proposed_filename}">{$filename.proposed_filename}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                {else}
                                    <input type="hidden" name="proposed_filename_{$fp.index}" value="">
                                    <p class="text-danger">{t}Dateiname löschen und später selber von Hand setzen.{/t}</p>
                                {/if}
                            </td>
                            {/foreach}

                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <label class="control-label">{t}Vorgeschlagene Dateinamen übernehmen:{/t}</label>
                    <input type="hidden" name="broken_footprints_count" value="{$broken_footprints_count}">
                    <div class="form-group">
                        <button class="btn btn-default" type="submit" name="save_proposed_filenames">Nur die markierten</button>
                        <button  class="btn btn-default" type="submit" name="save_all_proposed_filenames">Alle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
{/if}


{if $foot3d_active && !empty($broken_3d_filename_footprints)}
    <div class="panel panel-danger">
        <div class="panel-heading">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            {t}Footprints mit fehlerhaften Modelnamen{/t} ({$broken_3d_footprints_count}/{$broken_3d_footprints_count_total})
        </div>
        <div class="panel-body">
            {t}Die Dateinamen der folgenden Footprints konnten keinem Model zugeordnet werden. Bitte überprüfen bzw. korrigieren Sie die vorgeschlagenen Dateien, um sie dann zu übernehmen. Bereits gesetzte Haken bedeuten, dass für die jeweiligen Footprints exakt gleichnamige Dateien gefunden wurden.{/t}
            <form action="" method="post" >
                <div class="row">
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th>{t}Footprint{/t}</th>
                            <th>{t}Fehlerhafter Dateiname{/t}</th>
                            <th>{t}Vorgeschlagene Dateinamen{/t}</th>
                        </tr>
                        </thead>

                        <tbody>

                        {foreach $broken_3d_filename_footprints as $fp}
                        <!--the alternating background colors are created here-->
                        <tr>

                            <input type="hidden" name="broken_3d_footprint_id_{$fp.index}" value="{$fp.broken_id}">

                            <!--checkbox + footprint name-->
                            <td class="tdrow0">
                                <div class="checkbox">
                                    <input type="checkbox" {if $fp.checked}checked {/if}
                                           name="filename_3d_checkbox_{$fp.index}">
                                    <label>{$fp.broken_full_path}</label>
                                </div>
                            </td>

                            <!--broken filename-->
                            <td class="tdrow1 form-group">
                                <p class="form-control-static text-danger">{$fp.broken_filename}</p>
                            </td>

                            <!--proposed filenames-->
                            <td class="tdrow0 form-horizontal">
                                <label class="col-md-1 control-label">({$fp.proposed_filenames_count})</label>
                                {if $fp.proposed_filenames_count > 0}
                                    <div class="col-md-11">
                                        <select class="form-control selectpicker" data-live-search="true" name="proposed_3d_filename_{$fp.index}">
                                            <option value="">{t}Dateiname löschen und später selber von Hand setzen.{/t}</option>
                                            {foreach $fp.proposed_filenames as $filename}
                                                <option {if $filename.selected}selected{/if} value="{$filename.proposed_filename}">{$filename.proposed_filename}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                {else}
                                    <input type="hidden" name="proposed_3d_filename_{$fp.index}" value="">
                                    <p class="text-danger">{t}Dateiname löschen und später selber von Hand setzen.{/t}</p>
                                {/if}
                            </td>
                            {/foreach}

                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <label class="control-label">{t}Vorgeschlagene Dateinamen übernehmen:{/t}</label>
                    <input type="hidden" name="broken_3d_footprints_count" value="{$broken_3d_footprints_count}">
                    <div class="form-group">
                        <button class="btn btn-default" type="submit" name="save_proposed_3d_filenames">{t}Nur die markierten{/t}</button>
                        <button  class="btn btn-default" type="submit" name="save_all_proposed_3d_filenames">{t}Alle{/t}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
{/if}


