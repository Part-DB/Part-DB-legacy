{locale path="nextgen/locale" domain="partdb"}

<div class="card mt-3">
    <div class="card-header">
        <i class="fa fa-file fa-fw" aria-hidden="true"></i>
        {t}Dateianhänge{/t}
    </div>
    <div class="card-body">
        {foreach from=$attachements_loop item=attach key=attach_n}
            {if $attach_n > 0}
                <hr>
            {/if}
            <form action="{$relative_path}edit_part_info.php" method="post" enctype="multipart/form-data" class="no-progbar row" id="attachements">
                <div class="col-sm-2">
                    {if $attach.id == "new"}
                        <b>{t}Neue Datei hinzufügen:{/t}</b>
                    {else}
                        {if isset($attach.picture_filename) && $attach.picture_filename !== ""}
                            <a href="{$attach.picture_filename}" class="link-datasheet" rel="noopener" target="_blank">
                                <img class="img-fluid" rel="popover" src="{$attach.picture_filename}" alt="{$attach.name}">
                            </a>
                        {else}
                            {if isset($attach.filename) && $attach.filename !== ""}
                                <a href="{$attach.filename}" rel="noopener" class="link-external" target="_blank">{$attach.name}</a>
                            {else}
                                {$attach.name}
                            {/if}
                        {/if}
                    {/if}
                </div>


                <div class="col-sm-7">
                    <div class="row">
                        <div class="col-sm-3 form-group">
                            <label>{t}Name:{/t}</label>
                            <input type="text" class="form-control" name="name" size="12" value="{$attach.name}"
                                   {if !($can_attachement_edit || ($can_attachement_create && $attach.id == "new"))}disabled{/if}>
                        </div>
                        <div class="col-sm-5 form-group">
                            <label>{t}Dateityp:{/t}</label>
                            <select class="form-control" name="attachement_type_id"
                                    {if !($can_attachement_edit || ($can_attachement_create && $attach.id == "new"))}disabled{/if}>
                                {$attach.attachement_types_list nofilter}
                            </select>
                        </div>
                        <div class="col-sm-4 form-group">
                            <div class="form-check form-check-dropdown pl-2 abc-checkbox">
                                <input type="checkbox" class="form-check-input" name="show_in_table" {if $attach.show_in_table} checked{/if}
                                        {if !($can_attachement_edit || ($can_attachement_create && $attach.id == "new"))}disabled{/if}>
                                <label class="form-check-label" for="show_in_table">{t}In Tabelle anzeigen{/t}</label>
                            </div>

                            {if $attach.is_picture}
                                <div class="form-check form-check-dropdown pl-2 abc-checkbox">
                                    <input type="checkbox" class="form-check-input" name="is_master_picture" {if $attach.is_master_picture}checked{/if}
                                            {if !($can_attachement_edit || ($can_attachement_create && $attach.id == "new"))}disabled{/if}>
                                    <label class="form-check-label" for="is_master_picture">{t}Als Hauptbild verwenden{/t}</label>
                                </div>
                            {/if}
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-sm-3">
                            {t}Dateiname / URL:{/t}
                        </label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="attachement_filename"
                                   value="{$attach.filename_base_relative}" {if !($can_attachement_edit || ($can_attachement_create && $attach.id == "new"))}disabled{/if}>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <label class="col-sm-3">
                            {t}Neue Datei hochladen:{/t}
                        </label>
                        <div class="col-sm-5">
                            <input data-show-caption="false" data-show-upload="false" data-show-preview="false" type="file" class="file" name="attachement_file"
                                   {if !($can_attachement_edit || ($can_attachement_create && $attach.id == "new"))}disabled{/if}>
                            <p class="mb-0">(max. {$max_upload_filesize})</p>
                        </div>
                        {if $downloads_enable}
                            <div class="col-sm-4">
                                <div class="form-check form-check-inline abc-checkbox">
                                    <input class="form-check-input" type="checkbox" name="download_file" {if $attach.download_file}checked{/if}
                                            {if !($can_attachement_edit || ($can_attachement_create && $attach.id == "new"))}disabled{/if}>
                                    <label class="form-check-label" for="download_file">{t}Downloade Datei{/t}</label>
                                </div>
                            </div>
                        {/if}

                    </div>
                </div>

                <div class="col-sm-3">
                    <input type="hidden" name="pid" value="{$pid}">
                    <input type="hidden" name="attachement_id" value="{$attach.id}">
                    {if $attach.id == "new"}
                        <button class="btn btn-success"  type="submit" name="attachement_add" {if !$can_attachement_delete}disabled{/if}>{t}Hinzufügen{/t}</button>
                    {else}
                        <button class="btn btn-success" type="submit" name="attachement_apply" {if !$can_attachement_edit}disabled{/if}>{t}Übernehmen{/t}</button>
                        <button class="btn btn-danger" type="submit" name="attachement_delete" {if !$can_attachement_delete}disabled{/if}>{t}Löschen{/t}</button>
                    {/if}
                </div>
            </form>
        {/foreach}
    </div>
</div>
