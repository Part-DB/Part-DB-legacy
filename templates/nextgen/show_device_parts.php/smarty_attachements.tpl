{locale path="nextgen/locale" domain="partdb"}

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-file" aria-hidden="true"></i>
        {t}Dateianhänge{/t}
    </div>
    <div class="panel-body">
        {foreach $attachements_loop as $attach}
            <div class="row">
                <form action="{$relative_path}show_device_parts.php" method="post" enctype="multipart/form-data" class="no-progbar" id="attachements">
                    <div class="col-sm-2">
                        {if $attach.id == "new"}
                            <b>{t}Neue Datei hinzufügen:{/t}</b>
                        {else}
                            {if isset($attach.picture_filename) && $attach.picture_filename !== ""}
                                <a href="{$attach.picture_filename}" class="link-datasheet" target="_blank">
                                    <img class="img-responsive" rel="popover" src="{$attach.picture_filename}" alt="{$attach.name}">
                                </a>
                            {else}
                                {if isset($attach.filename)}
                                    <a href="{$attach.filename}" class="link-external" target="_blank" rel="noopener">{$attach.name}</a>
                                {else}
                                    {$attach.name}
                                {/if}
                            {/if}
                        {/if}
                    </div>


                    <div class="col-sm-7">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>{t}Name:{/t}</label>
                                <input type="text" class="form-control" name="name" size="12" value="{$attach.name}" required
                                       {if !($can_attachement_edit)}disabled{/if}>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>{t}Dateityp:{/t}</label>
                                <select class="form-control" name="attachement_type_id"
                                        {if !($can_attachement_edit)}disabled{/if}>
                                    {$attach.attachement_types_list nofilter}
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-sm-3">
                                {t}Dateiname / URL:{/t}
                            </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="attachement_filename"
                                       value="{$attach.filename_base_relative}" {if !($can_attachement_edit)}disabled{/if}>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3">
                                <b>{t}Neue Datei hochladen:{/t}</b>
                            </label>
                            <div class="col-sm-6">
                                <input data-show-caption="false" data-show-upload="false" type="file" class="file" name="attachement_file"
                                       {if !($can_attachement_edit)}disabled{/if}>
                                <p>(max. {$max_upload_filesize})</p>
                            </div>
                            {if $downloads_enable}
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <input type="checkbox" name="download_file" {if $attach.download_file}checked{/if}
                                                {if !($can_attachement_edit)}disabled{/if}>
                                        <label for="download_file">{t}Downloade Datei{/t}</label>
                                    </div>
                                </div>
                            {/if}

                        </div>
                    </div>

                    <div class="col-sm-3">
                        <input type="hidden" name="device_id" value="{$device_id}">
                        <input type="hidden" name="attachement_id" value="{$attach.id}">
                        {if $attach.id == "new"}
                            <button class="btn btn-success"  type="submit" name="attachement_add" {if !$can_attachement_edit}disabled{/if}>{t}Hinzufügen{/t}</button>
                        {else}
                            <button class="btn btn-success" type="submit" name="attachement_apply" {if !$can_attachement_edit}disabled{/if}>{t}Übernehmen{/t}</button>
                            <button class="btn btn-danger" type="submit" name="attachement_delete" {if !$can_attachement_edit}disabled{/if}>{t}Löschen{/t}</button>
                        {/if}
                    </div>
                </form>
            </div>
            <hr>
        {/foreach}
    </div>
</div>
