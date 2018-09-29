{locale path="nextgen/locale" domain="partdb"}

<div class="card mt-3">
    <div class="card-header">
        <a data-toggle="collapse" class="link-collapse text-default" href="#panel-devices"><i class="fa fa-briefcase fa-fw" aria-hidden="true"></i>
            {t}Baugruppen mit diesem Bauteil{/t}
        </a>
    </div>
    <div class="card-collapse collapse" id="panel-devices">
        <form method="post">
            <input type="hidden" name="pid" value="{$pid}">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th></th>
                    <th>{t}Baugruppenname{/t}</th>
                    <th>{t}Bestückungszahl{/t}</th>
                    <th>{t}Bestückungsdaten{/t}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {if isset($devices_loop)}
                    {foreach $devices_loop as $device}
                        <tr>
                            <td></td>
                            <td><a href="{$relative_path}show_device_parts.php?device_id={$device.id}"
                                   title="{$device.fullpath}" >{$device.name}</a></td>
                            <td>{$device.mount_quantity}</td>
                            <td>{$device.mount_name}</td>
                            <td></td>
                        </tr>
                    {/foreach}
                {/if}
                <tr>
                    <td><span class="badge badge-primary">{t}Neu:{/t}</span></td>
                    <td><select class="form-control selectpicker" data-live-search="true" name="device_id_new" {if !$can_devicepart_create}disabled{/if}>
                            {$devices_list nofilter}
                        </select>
                    </td>
                    <td><input type="number" name="device_quantity_new" class="form-control" min="1" step="1" required {if !$can_devicepart_create}disabled{/if}></td>
                    <td><input type="text" name="device_name_new" class="form-control" {if !$can_devicepart_create}disabled{/if}></td>
                    <td><button class="btn btn-success" type="submit" name="device_add" {if !$can_devicepart_create}disabled{/if}>{t}Hinzufügen{/t}</button></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>