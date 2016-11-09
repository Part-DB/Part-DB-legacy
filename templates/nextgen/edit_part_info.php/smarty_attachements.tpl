<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Dateianhänge</h4>
    </div>
    <div class="panel-body table-responsive">
        <table class="table  table-condensed tabel-hover table-striped">
           <thead>
                <tr class="trcat">
                    <th>Bild / Link</th>
                    <th>Eigenschaften</th>
                    <th></th>
                </tr>
            </thead>

            {foreach $attachements_loop as $attach}
                <form action="edit_part_info.php" method="post" enctype="multipart/form-data">
                    <tr>

                        <!--Picture-->
                        <td class="tdrow0">
                            {if $attach.id == "new"}
                                <b>Neue Datei hinzufügen:</b>
                            {else}
                                {if isset($attach.picture_filename)}
                                    <a href="{$attach.picture_filename}">
                                        <img style="max-height:180px; max-width:180px" src="{$attach.picture_filename}" alt="{$attach.name}">
                                    </a>
                                {else}
                                    {if isset($attach.filename)}
                                        <a href="{$attach.filename}">{$attach.name}</a>
                                    {else}
                                        {$attach.name}
                                    {/if}
                                {/if}
                            {/if}
                        </td>

                        <td>
                            <table class="table table-striped  table-hover table-bordered">
                                <tr>
                                    <td>
                                        <b>Name:</b><br>
                                        <input type="text" class="form-control" name="name" size="12" value="{$attach.name}">
                                    </td>
                                    <td>
                                        <b>Dateityp:</b><br>
                                        <select class="form-control" name="attachement_type_id">
                                            {$attach.attachement_types_list}
                                        </select>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <input type="checkbox" class="styled" name="show_in_table" {if $attach.show_in_table} checked{/if}>
                                            <label for="show_in_table">In Tabelle anzeigen</label>
                                        </div>
                            
                                        {if $attach.is_picture}
                                        <div class="checkbox">
                                            <input type="checkbox" class="styled" name="is_master_picture" {if $attach.is_master_picture} checked{/if}><label for="is_master_picture">Als Hauptbild verwenden</label>
                                        </div>
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Dateiname / URL:</b>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" class="form-control" name="attachement_filename" value="{$attach.filename_base_relative}" style="width:98%">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Neue Datei hochladen:</b>
                                    </td>
                                    <td colspan="2">
                                        <input data-show-caption="false" type="file" name="attachement_file">
                                        <!--(max. {$max_upload_filesize}) -->
                                    </td>
                                </tr>
                            </table>
                        </td>

                        <td class="tdrow1">
                            <input type="hidden" name="pid" value="{$pid}">
                            <input type="hidden" name="attachement_id" value="{$attach.id}">
                            {if $attach.id == "new"}
                                <button class="btn btn-success" type="submit" name="attachement_add">Hinzufügen</button>
                            {else}
                                <button class="btn btn-success" type="submit" name="attachement_apply">Übernehmen</button>
                                <button class="btn btn-danger" type="submit" name="attachement_delete">Löschen</button>
                            {/if}
                        </td>
                    </tr>
                </form>
            {/foreach}
        </table>
    </div>
</div>
