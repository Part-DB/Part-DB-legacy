{locale path="nextgen/locale" domain="partdb"}

<div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-key" aria-hidden="true"></i>&nbsp;
        {t}Installation/Update: Administratorpasswort festlegen{/t}</div>
    <div class="panel-body">

        <p>{t}Bitte legen Sie ein Password für den "admin" Benutzer fest. Sie können dieses später wieder ändern.{/t}</p>


        <form action="" method="post" class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-md-3">{t}Passwort:{/t}</label>
                <div class="col-md-9">
                    <input class="form-control" type="password" name="adminpass_1" required>
                </div>
            </div>
            <div class="form-group">
                    <label class="col-md-3 control-label">{t}Wiederholung:{/t}</label>
                    <div class="col-md-9">
                        <input type="password" class="form-control" name="adminpass_2" required>
                    </div>
            </div>
            <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                    <button class="btn btn-primary" type="submit" name="save_admin_password">{t}Weiter{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>
