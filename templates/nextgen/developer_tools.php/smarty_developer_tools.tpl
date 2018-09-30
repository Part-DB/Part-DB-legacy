{locale path="nextgen/locale" domain="partdb"}
  
   <div class="card">
    <div class="card-header">
        {t}Tabs durch Leerzeichen ersetzen / Backup-Dateien löschen{/t}
    </div>
    <div class="card-body">
        <form action="" method="post">
            <div class="form-check form-check-inline abc-checkbox mb-2">
                <input type="checkbox" class="styled form-check-input" name="trim_exec_output" checked>
                <label for="trim_exec_output" class="form-check-label">{t}Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen){/t}</label>
            </div>
            <br>
            <button class="btn btn-secondary mr-3" type="submit" name="tab2spaces">Ausführen</button>
            <label>{t}Der Vorgang kann mehrere Minuten in Anspruch nehmen!{/t}</label>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        {t}Doxygen-Dokumentation erstellen bzw. updaten{/t}
    </div>
    <div class="card-body">
        <form action="" method="post">
            <b><span class="text-danger">{t}Doxygen muss auf dem Server installiert sein!{/t}</span></b> <br>
            <div class="form-check form-check-inline abc-checkbox mb-2">
                <input type="checkbox" class="styled form-check-input" name="trim_exec_output" checked>
                <label for="trim_exec_output" class="form-check-label">{t}Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen){/t}</label>
            </div> <br>
            <button type="submit" class="btn btn-secondary mr-3" name="build_doxygen">{t}Dokumentation erstellen/updaten{/t}</button>
            <label for="build_doxygen">{t}Der Vorgang kann mehrere Minuten in Anspruch nehmen!{/t}</label>
        </form>
    </div>
</div>

<div class="card mt-3">
       <div class="card-header">
        {t}Release-Paket erstellen{/t}
    </div>
    <div class="card-body">
        <form action="" method="post">
            <b>{t}Version:{/t} {$current_system_version}</b>
            {if isset($release_archive_link)}
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="download" href="{$release_archive_link}">{t}Download{/t} "{$release_archive_basename}"</a>
            {/if}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" rel="noopener" class="link-external" href="{$packing_checklist_link}">{t}Checkliste{/t}</a>
            <br>
            <div class="form-check form-check-inline abc-checkbox mb-2">
                <input class="styled form-check-input" type="checkbox" name="trim_exec_output" checked>
                <label class="form-check-label" for="trim_exec_output">{t}Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen){/t}</label>
            </div> <br>
            {if isset($release_archive_link)}
                <button class="btn btn-default" type="submit" name="delete_release_package">{t}Paket löschen{/t}</button>
            {/if}
            <button class="btn btn-secondary" type="submit" name="build_release_package">{t}Paket neu erstellen{/t}</button>
            <label for="build_release_package">{t}Der Vorgang kann mehrere Minuten in Anspruch nehmen!{/t}</label>
        </form>
    </div>
</div>

{if isset($exec_output)}
<div class="card mt-3 {if isset($exec_successful)}border-successful{else}border-danger{/if}">
    <div class="card-header text-white {if isset($exec_successful)}bg-successful{else}bg-danger{/if}">
        {t}Ausgabe{/t}
    </div>
    <div class="card-body">
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
