{locale path="nextgen/locale" domain="partdb"}
{if isset($refresh_navigation_frame) && $refresh_navigation_frame}
    <script type="text/javascript">
        location.reload();
    </script>
{/if}

<div class="card mt-3">
    <div class="card-header"><a data-toggle="collapse" class="link-collapse text-default" href="#panel-copy">
            <i class="fas fa-clone fa-fw"></i> {t}Baugruppe inklusive Bauteile kopieren{/t}
        </a>
    </div>
    <div class="card-body card-collapse collapse" id="panel-copy">
        <form method="post" class="form-horizontal" action="">
            <input type="hidden" name="device_id" value="{$device_id}">
                <div class="form-group row">
                    <label class="col-form-label col-md-3">{t}Name der Kopie:{/t}</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="copy_new_name"
                               value="{t}Kopie_von_{/t}{$device_name}" {if !$can_devices_add}disabled{/if}>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-3">{t}Übergeordnete Baugruppe:{/t}</label>
                    <div class="col-md-9">
                        <select class="form-control" name="copy_new_parent_id" size="1" {if !$can_devices_add}disabled{/if}>
                            {$parent_device_list nofilter}
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-3">{t}Rekursiv:{/t}</label>
                    <div class="col-md-9">
                        <div class="form-check form-check-inline abc-checkbox form-control-plaintext">
                            <input class="form-check-input" type="checkbox" name="copy_recursive" {if !$can_devices_add}disabled{/if}>
                            <label class="form-check-label">{t}Alle Unterbaugruppen mit all deren Teilen auch mitkopieren{/t}</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-9 offset-md-3">
                        <button class="btn btn-success" type="submit" name="copy_device" {if !$can_devices_add}disabled{/if}>
                            {t}Kopie anlegen{/t}</button>
                    </div>
                </div>
        </form>
    </div>
</div>

