{locale path="nextgen/locale" domain="partdb"}

{if $refresh_navigation_frame}
    <script type="text/javascript">
        location.reload();
    </script>
{/if}

{if !$hide_status}
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-info-circle" aria-hidden="true"></i>
            {t}Datenbank Status / Update{/t}
        </div>
        <div class="panel-body">
            <form action="" method="post" class="form-horizontal no-progbar">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Eigenschaft</th>
                        <th>Wert</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            {t}Aktuelle Version:{/t}
                        </td>
                        <td>
                            {$current_version}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            {t}Benötigte Version:{/t}
                        </td>
                        <td>
                            {$latest_version}
                        </td>
                    </tr>
                    </tbody>
                </table>

                {if isset($update_required) && $update_required}
                    <strong><span style="color: red; ">{t}Die Datenbank benötigt ein Update!{/t}</span></strong><br>
                    {if $last_update_failed}
                        <br>
                        <strong><span style="color: red; ">
                                        {t}ACHTUNG:{/t}<br>
                                {t}Das letzte Update ist fehlgeschlagen. Sie können beliebig oft versuchen,
                                    das Update an der Stelle des letzten Abbruchs fortzusetzen.
                                    Falls Sie zwischenzeitlich aber eine neue Datenbank geladen haben
                                    (z.B. ein Backup eingespielt), muss das Update jedoch wieder von Vorne gestartet werden.<br>
                                    Sie haben deshalb die folgenden zwei Möglichkeiten:{/t}
                                    </span></strong>
                        <br>
                        <button type="submit" class="btn btn-default" name="make_update">{t}Letztes, fehlgeschlagenes Update fortsetzen{/t}</button>
                        <button type="submit" class="btn btn-default" name="make_new_update">{t}Neuer Update-Versuch beginnen (von Vorne){/t}</button>
                    {else}
                        <br>
                        <button class="btn btn-success" type="submit" name="make_update">{t}Jetzt Datenbank updaten{/t}</button>
                    {/if}
                {else}
                    <span style="color: darkgreen; ">{t}Die Datenbank ist auf dem neusten Stand.{/t}</span>
                {/if}

            </form>
        </div>
    </div>
{/if}

<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-database" aria-hidden="true"></i>
        {t}Datenbank-Einstellungen{/t}
    </div>
    <div class="panel-body">
        <form action="" class="form-horizontal" method="post">
            <div class="form-group">
                <label class="control-label col-sm-3">{t}Datenbanktyp:{/t}</label>
                <div class="col-sm-9">
                    <select name="db_type" class="form-control">
                        {foreach $db_type_loop as $db}
                            <option value="{$db.value}" {if $db.selected}selected{/if}>{$db.text}</option>
                        {/foreach}
                    </select>
                </div>
            </div>


            <div class="form-group">
                <label class="control-label col-sm-3">{t}Host:{/t}</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="db_host" value="{if !$is_online_demo}{$db_host}{/if}">
                    <!-- (nicht nötig für SQLite) -->
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-3">{t}Datenbankname:{/t}</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="db_name" value="{if !$is_online_demo}{$db_name}{/if}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-3">{t}Benutzer:{/t}</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="db_user" value="{if !$is_online_demo}{$db_user}{/if}">
                    <!-- (nicht nötig für SQLite) -->
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-3">{t}Datenbankpasswort:{/t}</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" name="db_password" value="">
                    <!-- (nicht nötig für SQLite) -->
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-3">{t}Administratorpasswort:{/t}</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" name="admin_password" value="">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn btn-primary" name="apply_connection_settings" {if $is_online_demo}disabled{/if}>{t}Einstellungen übernehmen{/t}</button>
                </div>
            </div>

            <hr>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <div class="checkbox">
                        <input type="checkbox" name="automatic_updates_enabled" {if $automatic_updates_enabled} checked{/if}>
                        <label for="automatic_updated_enabled">{t}Automatische Updates aktivieren{/t}</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <button class="btn btn-success" type="submit" name="apply_auto_updates">{t}Übernehmen{/t}</button>
                </div>
            </div>

        </form>
    </div>
</div>
