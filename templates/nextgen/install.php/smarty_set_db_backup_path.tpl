{locale path="nextgen/locale" domain="partdb"}

<div class="card border-primary">
    <div class="card-header bg-primary text-white"><i class="fa fa-life-ring" aria-hidden="true"></i>&nbsp;
        {t}Installation/Update: Datenbank Backupsystem{/t}</div>
    <div class="card-body">
        <span style="color: red; "><p><b>{t}Es wird dringend empfohlen, regelmässig Sicherungskopien der Datenbank zu erstellen!{/t}</b></p>
            <p>{t}Auch sollte vor jedem Datenbankupdate ein Backup durchgeführt werden. Die Entwickler von Part-DB übernehmen keinerlei Haftung für Schäden jeglicher Art, die durch fehlende Backups oder durch Fehler in Part-DB verursacht werden.{/t}</p></span>
        <p>{t escape=off}Sie können dafür ein externes System benutzen, das sich mit einem Link ins Menü von Part-DB integrieren lässt.
            Ein solches Backup-System ist z.B. <a target="_blank" rel="noopener" href="http://www.mysqldumper.net/">MySQLDumper</a>.{/t}</p>
        <p>{t}Lassen Sie beide Felder leer, wenn Sie keine Verknüpfung zu einem Backup-System haben möchten.{/t}</p>

        <form action="" method="post" class="form-horizontal">
            <div class="form-group row">
                <label class="col-form-label col-md-3">{t}Name des Backup-Systems:{/t}</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="db_backup_name" value="{$db_backup_name}" placeholder='{t}z.B. "MySQLDumper"{/t}'>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-form-label col-md-3">{t}Link zum Backup-System:{/t}</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="db_backup_path" value="{$db_backup_path}" placeholder='{t}z.B. "../mysqldumper/"{/t}'>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-9 offset-md-3">
                    <button class="btn btn-primary" type="submit" name="save_db_backup_path">{t}Weiter{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>
