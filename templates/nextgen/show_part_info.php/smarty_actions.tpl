{locale path="nextgen/locale" domain="partdb"}

<div class="card d-print-none mt-3">
    <div class="card-header">
        <a data-toggle="collapse" class="link-collapse text-default" href="#panel-actions"><i class="fa fa-bolt fa-fw" aria-hidden="true"></i>
            {t}Aktionen{/t}</a>
    </div>
    <div class="card-body card-collapse collapse" id="panel-actions">

        <form class="form-horizontal no-progbar" method="post">
            <input type="hidden" name="pid" value="{$pid}">
            <div class="form-group row">
                <label class="col-form-label col-sm-2">{t}Favorit:{/t}</label>
                <div class="col-sm-10">
                    <button class="btn btn-primary" name="toggle_favorite" value="" type="submit" {if !$can_favorite}disabled{/if}
                    >{if !$is_favorite}<i class="fa fa-star fa-fw" aria-hidden="true"></i> {t}Bauteil favorisieren{/t}
                        {else}<i class="far fa-star fa-fw" aria-hidden="true"></i> {t}Favorisierung aufheben{/t}{/if}</button>
                </div>
            </div>
        </form>

        <br>

        <form class="form-horizontal no-progbar" action="{$relative_path}edit_part_info.php" method="get">
            <div class="form-group row">
                <label class="col-form-label col-sm-2">{t}Bauteil löschen:{/t}</label>
                <input type="hidden" name="pid" value="{$pid}">
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-danger" name="delete_part" {if !$can_delete}disabled{/if}>
                        <i class="fa fa-trash" aria-hidden="true"></i> {t}Lösche Teil!{/t}
                    </button>
                    <br>
                    <div class="form-check form-check-dropdown abc-checkbox-danger abc-checkbox mt-2 pl-2">
                        <input class="form-check-input" type="checkbox" class="styled" id="delete_files_from_hdd" name="delete_files_from_hdd" {if !$can_delete}disabled{/if}>
                        <label class="form-check-label text-danger" for="delete_files_from_hdd">{t}Dateien dieses Bauteiles, die von keinem anderen Bauteil verwendet werden, auch von der Festplatte löschen{/t}</label>
                    </div>
                </div>
            </div>
        </form>

        <br>

        <form action="{$relative_path}edit_part_info.php" class="form-horizontal no-progbar" method="post">
            <div class="form-group row">
                <label  class="col-form-label col-sm-2">{t}Weiteres Bauteil anlegen:{/t}</label>
                <input type="hidden" name="pid" value="{$pid}">
                <div class="col-sm-10">
                    <button class="btn btn-success" type="submit" name="add_one_more_part" {if !$can_create}disabled{/if}>
                        <i class="fa fa-plus-square" aria-hidden="true"></i> {t}Neues Bauteil erfassen{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>
