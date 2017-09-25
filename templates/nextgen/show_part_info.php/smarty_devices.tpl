{locale path="nextgen/locale" domain="partdb"}

<div class="panel panel-default">
    <div class="panel-heading">
        <a data-toggle="collapse" class="link-collapse text-default" href="#panel-devices"><i class="fa fa-briefcase fa-fw" aria-hidden="true"></i>
            {t}Baugruppen mit diesem Bauteil{/t}
        </a>
    </div>
    <div class="panel-collapse collapse" id="panel-devices">
        {if isset($devices_loop)}
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>{t}Baugruppenname{/t}</th>
                    <th>{t}Bestückungszahl{/t}</th>
                    <th>{t}Bestückungsdaten{/t}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $devices_loop as $device}
                    <tr>
                        <td><a href="{$relative_path}show_device_parts.php?device_id={$device.id}"
                            title="{$device.fullpath}" >{$device.name}</a></td>
                        <td>{$device.mount_quantity}</td>
                        <td>{$device.mount_name}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {else}
            {t}Es gibt keine Baugruppen mit diesem Bauteil.{/t}
        {/if}
    </div>
</div>