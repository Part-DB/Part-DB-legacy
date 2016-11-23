<div class="panel panel-primary">
    <div class="panel-heading"><h4>Footprints</h4></div>
    <div class="panel-body">
        <form action="" method="post" class="row">
            <div class="col-md-4">
                <td rowspan="6">
                    <select name="selected_id" size="30"  class="form-control" onChange="this.form.submit()">
                        <optgroup label="Neu">
                            <option value="0" {if !isset($id) || $id == 0}selected{/if}>Neuer Footprint</option>
                        </optgroup>
                        <optgroup label="Bearbeiten">
                            {$footprint_list}
                        </optgroup>
                    </select>
                </td>
            </div>
            
            <div class="col-md-8 form-horizontal">
                <h4>
                    {if !isset($id) || $id == 0}
                        <strong>Neuer Footprint hinzufügen:</strong>
                    {else}
                        {if isset($name)}
                            <strong>Footprint bearbeiten:</strong>
                        {else}
                            <strong>Es ist kein Footprint angewählt!</strong>
                        {/if}
                    {/if}
                </h4>
            
                <div class="form-group">
                    <label class="control-label col-md-3">ID:</label>
                    <div class="col-md-9">
                        <p class="form-control-static">{if isset($id)}{$id}{else}-{/if}</p>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="control-label col-md-3">Name*:</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="name" value="{$name}" required>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="control-label col-md-3">Übergeordneter Footprint*:</label>
                    <div class="col-md-9">
                        <select class="form-control" name="parent_id" size="1">
                            {$parent_footprint_list}
                        </select>
                    </div>
                </div>
            
               <div class="form-group">
                    <label class="control-label col-md-3">Bild:</label>
                    <div class="col-md-9">
                        <input type="text" name="filename" value="{$filename}" class="form-control">
                        <p><i>Hinweis: Sie können hier z.B. "DIP28" eintippen und übernehmen.
                        Der Footprint wird dann unter "Footprints mit fehlerhaften Dateinamen" aufgelistet,
                            wo Sie Vorschläge für Dateinamen bekommen und dann einfach übernehmen können.</i></p>
                        {if isset($filename) && !empty($filename)}
                            <img rel="popover" data-img="//placehold.it/400x200" height="70" src="{$filename}">
                        {/if}
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
                        <button class="btn btn-success" type="submit" name="add">Neuer Footprint anlegen</button>
                        <div class="checkbox">
                            <input type="checkbox" name="add_more" {if $add_more}checked{/if}>
                            <label>Weitere Footprints anlegen</label>
                        </div>
                    {else}
                        <button class="btn btn-success" type="submit" name="apply">Änderungen übernehmen</button>
                        <button class="btn btn-danger" type="submit" name="delete">Footprint löschen</button>
                    {/if}
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{if isset($broken_filename_footprints) && $broken_filename_footprints}
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h4>Footprints mit fehlerhaften Dateinamen ({$broken_footprints_count}/{$broken_footprints_count_total})</h4>
        </div>
        <div class="panel-body">
            Die Dateinamen der folgenden Footprints konnten keiner Bilddatei zugeordnet werden.
            Bitte überprüfen bzw. korrigieren Sie die vorgeschlagenen Dateien, um sie dann zu übernehmen.
            Bereits gesetzte Haken bedeuten, dass für die jeweiligen Footprints exakt gleichnamige Dateien gefunden wurden.<br>
            <form action="" method="post" > 
               <div class="row">
                <table class="table table-hover table-striped">
                    <thead>
                        <th>Footprint</th>
                        <th>Fehlerhafter Dateiname</th>
                        <th>Vorgeschlagene Dateinamen</th>
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
                                    <select class="form-control" name="proposed_filename_{$fp.index}">
                                        <option value="">Dateiname löschen und später selber von Hand setzen.</option>
                                        {foreach $fp.proposed_filenames as $filename}
                                            <option {if $filename.selected}selected{/if} value="{$filename.proposed_filename}">{$filename.proposed_filename}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                {else}
                                    <input type="hidden" name="proposed_filename_{$fp.index}" value="">
                                    <p class="text-danger">Dateiname löschen und später selber von Hand setzen.</p>
                                {/if}
                            </td>
                        {/foreach}
                    
                    </tbody>
                </table>
                </div>
                <div class="form-group">
                    <label class="control-label">Vorgeschlagene Dateinamen übernehmen:</label>
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
