{locale path="nextgen/locale" domain="partdb"}

<!--suppress Annotator -->
<div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-database" aria-hidden="true"></i>&nbsp
        {t}Installation/Update: Datenbank konfigurieren{/t}</div>
    <div class="panel-body">
        <b>{t}Die Datenbank für Part-DB muss bereits existieren, damit Sie Part-DB installieren können.
            Wenn Sie Part-DB bereits benutzt haben, können Sie die vorhandene Datenbank weiter benutzen,
            ansonsten sollte die Datenbank komplett leer sein.{/t}
            <br><br>
            <span style="color:red;">
                {t}Achtung:{/t}
                <ul>
                    <li>{t}Damit Part-DB korrekt funktioniert, müssen Sie dem Benutzer jegliche Rechte an der Datenbank gewähren!{/t}</li>
                    <li>{t}Benutzen Sie eine bereits vorhandene Datenbank weiter, sollten Sie jetzt ein Backup davon anlegen!{/t}</li>
                </ul>
            </span></b>

        <form action="" method="post" class="form-horizontal">
            <div class="form-group">
                <label class="col-md-3 control-label">{t}Datenbanktyp:{/t}</label>
                <div class="col-md-9">
                        <select name="db_type" class="form-control">
                            {foreach $db_type_loop as $db}
                                <option value="{$db.value}" {if $db.selected}selected{/if}>{$db.text}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Host:{/t}</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="db_host" value="{$db_host}" placeholder="{t}z.B. localhost{/t}" required>
                        <!-- (nicht nötig für SQLite) -->
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Datenbankname:{/t}<!--/<br>Dateiname--></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="db_name" value="{$db_name}" placeholder="{t}z.B. part-db{/t}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Benutzer:{/t}</label>
                    <div class="col-md-9">
                        <input type="text" name="db_user" class="form-control" value="{$db_user}" placeholder="{t}z.B. part-db{/t}" required>
                        <!-- (nicht nötig für SQLite) -->
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Datenbankpasswort:{/t}</label>
                    <div class="col-md-9">
                        <input type="password" class="form-control" name="db_password" value="">
                        <!-- (nicht nötig für SQLite) -->
                    </div>
                </div>

                <hr>

                <label>{t}Sollte es nicht möglich sein mit der Datenbank zu verbinden, versuchen sie eine der untenstehenden Optionen anzuwählen:{/t}</label>

                <div class="form-group">
                    {* <label class="col-md-3 control-label">{t}Leerzeichen in PDO-String einfügen:{/t}</label> *}
                    <div class="col-md-9 col-md-offset-3">
                        <div class="checkbox">
                            <input type="checkbox" class="form-control" name="space_fix" value="" {if $space_fix}checked{/if}>
                            <label>{t}Leerzeichen in PDO-String einfügen{/t}</label>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                        <button class="btn btn-primary" type="submit" name="save_db_settings">{t}Weiter{/t}</button>
                    </div>
                </div>
        </form>
    </div>
</div>

</div> <!-- for header-->
</body>
</html>