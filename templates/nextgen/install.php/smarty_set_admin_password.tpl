{locale path="nextgen/locale" domain="partdb"}

<div class="card border">
    <div class="card-header bg-primary text-white"><i class="fa fa-key" aria-hidden="true"></i>&nbsp;
        {t}Installation/Update: Administratorpasswort festlegen{/t}</div>
    <div class="card-body">

        <p>{t}Bitte legen Sie ein Password für den "admin" Benutzer fest. Sie können dieses später wieder ändern.{/t}</p>


        <form action="" method="post" class="form-horizontal">
            <div class="form-group row">
                <label class="col-form-label col-md-3">{t}Passwort:{/t}</label>
                <div class="col-md-9">
                    <input class="form-control" type="password" name="adminpass_1" required>
                </div>
            </div>
            <div class="form-group row">
                    <label class="col-md-3 col-form-label">{t}Wiederholung:{/t}</label>
                    <div class="col-md-9">
                        <input type="password" class="form-control" name="adminpass_2" required>
                    </div>
            </div>
            <div class="form-group row">
                <div class="col-md-9 offset-md-3">
                    <button class="btn btn-primary" type="submit" name="save_admin_password">{t}Weiter{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>
