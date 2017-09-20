{locale path="nextgen/locale" domain="partdb"}
{if isset($refresh_navigation_frame) && $refresh_navigation_frame}
    <script type="text/javascript">
        location.reload();
    </script>
{/if}


<div class="panel panel-default">
    <div class="panel-heading">{t}Baugruppe inklusive Bauteile kopieren{/t}</div>
    <div class="panel-body">
        <form method="post" class="form-horizontal" action="">
            <input type="hidden" name="device_id" value="{$device_id}">
                <div class="form-group">
                    <label class="control-label col-md-3">{t}Name der Kopie:{/t}</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="copy_new_name"
                               value="{t}Kopie_von_{/t}{$device_name}" {if !$can_devices_add}disabled{/if}>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">{t}Übergeordnete Baugruppe:{/t}</label>
                    <div class="col-md-9">
                        <select class="form-control" name="copy_new_parent_id" size="1" {if !$can_devices_add}disabled{/if}>
                            {$parent_device_list nofilter}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">{t}Rekursiv:{/t}</label>
                    <div class="col-md-9">
                        <div class="checkbox">
                            <input type="checkbox" name="copy_recursive" {if !$can_devices_add}disabled{/if}>
                            <label>{t}Alle Unterbaugruppen mit all deren Teilen auch mitkopieren{/t}</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                        <button class="btn btn-success" type="submit" name="copy_device" {if !$can_devices_add}disabled{/if}>
                            {t}Kopie anlegen{/t}</button>
                    </div>
                </div>
        </form>
    </div>
</div>

