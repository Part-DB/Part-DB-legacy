<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Tabs durch Leerzeichen ersetzen / Backup-Dateien löschen</h4>
    </div>
    <div class="panel-body">
        <form action="" method="post">
            <div class="checkbox">
                <input type="checkbox" class="styled" name="trim_exec_output" checked>
                <label for="trim_exec_output">Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen)</label> 
            </div>
            <button class="btn btn-default" type="submit" name="tab2spaces">Ausführen</button>
            <label><i>Der Vorgang kann mehrere Minuten in Anspruch nehmen!</i></label>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Doxygen-Dokumentation erstellen bzw. updaten</h4>
    </div>
    <div class="panel-body">
        <form action="" method="post">
            <b><span class="text-danger">Doxygen muss auf dem Server installiert sein!</span></b>
            <div class="checkbox">
                <input type="checkbox" class="styled" name="trim_exec_output" checked>
                <label for="trim_exec_output">Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen)</label>
            </div>
            <button type="submit" class="btn btn-default" name="build_doxygen">Dokumentation erstellen/updaten</button>
            <label for="build_doxygen"><i>Der Vorgang kann mehrere Minuten in Anspruch nehmen!</i></label>
        </form>
    </div>
</div>

<div class="panel panel-default">
       <div class="panel-heading">
        <h4>Release-Paket erstellen</h4>
    </div>
    <div class="panel-body">
        <form action="" method="post">
            <b>Version: {$current_system_version}</b>
            {if isset($release_archive_link)}
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="download" href="{$release_archive_link}">Download "{$release_archive_basename}"</a>
            {/if}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="{$packing_checklist_link}">Checkliste</a>
            <div class="checkbox">
                <input class="styled" type="checkbox" name="trim_exec_output" checked>
                <label for="trim_exec_output">Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen)</label>
            </div>
            {if isset($release_archive_link)}
                <button class="btn btn-default" type="submit" name="delete_release_package">Paket löschen</button>
            {/if}
            <button class="btn btn-default" type="submit" name="build_release_package">Paket neu erstellen</button>
            <label for="build_release_package"><i>Der Vorgang kann mehrere Minuten in Anspruch nehmen!</i></label>
        </form>
    </div>
</div>

{if isset($exec_output)}
<div class="panel {if isset($exec_successful)}panel-successful{else}panel-danger{/if}">
    <div class="panel-heading">
        <h4>Ausgabe</h4>
    </div>
    <div class="panel-body">
            <pre>
                <code>
                {foreach $exec_output as $line }
                    {$line.text}
                {/foreach}
                </code>
            </pre>
    </div>
</div>
{/if}
