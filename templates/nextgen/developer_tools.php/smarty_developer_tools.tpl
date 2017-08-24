{locale path="nextgen/locale" domain="partdb"}
  
   <div class="panel panel-default">
    <div class="panel-heading">
        {t}Tabs durch Leerzeichen ersetzen / Backup-Dateien löschen{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post">
            <div class="checkbox">
                <input type="checkbox" class="styled" name="trim_exec_output" checked>
                <label for="trim_exec_output">{t}Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen){/t}</label> 
            </div>
            <button class="btn btn-default" type="submit" name="tab2spaces">Ausführen</button>
            <label>{t}Der Vorgang kann mehrere Minuten in Anspruch nehmen!{/t}</label>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        {t}Doxygen-Dokumentation erstellen bzw. updaten{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post">
            <b><span class="text-danger">{t}Doxygen muss auf dem Server installiert sein!{/t}</span></b>
            <div class="checkbox">
                <input type="checkbox" class="styled" name="trim_exec_output" checked>
                <label for="trim_exec_output">{t}Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen){/t}</label>
            </div>
            <button type="submit" class="btn btn-default" name="build_doxygen">{t}Dokumentation erstellen/updaten{/t}</button>
            <label for="build_doxygen">{t}Der Vorgang kann mehrere Minuten in Anspruch nehmen!{/t}</label>
        </form>
    </div>
</div>

<div class="panel panel-default">
       <div class="panel-heading">
        {t}Release-Paket erstellen{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post">
            <b>{t}Version:{/t} {$current_system_version}</b>
            {if isset($release_archive_link)}
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="download" href="{$release_archive_link}">{t}Download{/t} "{$release_archive_basename}"</a>
            {/if}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" class="link-external" href="{$packing_checklist_link}">{t}Checkliste{/t}</a>
            <div class="checkbox">
                <input class="styled" type="checkbox" name="trim_exec_output" checked>
                <label for="trim_exec_output">{t}Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen){/t}</label>
            </div>
            {if isset($release_archive_link)}
                <button class="btn btn-default" type="submit" name="delete_release_package">{t}Paket löschen{/t}</button>
            {/if}
            <button class="btn btn-default" type="submit" name="build_release_package">{t}Paket neu erstellen{/t}</button>
            <label for="build_release_package">{t}Der Vorgang kann mehrere Minuten in Anspruch nehmen!{/t}</label>
        </form>
    </div>
</div>

{if isset($exec_output)}
<div class="panel {if isset($exec_successful)}panel-successful{else}panel-danger{/if}">
    <div class="panel-heading">
        {t}Ausgabe{/t}
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
