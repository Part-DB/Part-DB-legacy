{locale path="nextgen/locale" domain="partdb"}
<div class="card mt-3">
    <div class="card-header"><i class="fa fa-key" aria-hidden="true"></i>
        {t}Passwort ändern{/t}</div>
    <div class="card-body">
        <form class="no-progbar form-horizontal" method="post">
            <div class="form-group row">
                <label class="col-form-label col-md-2">{t}Altes Passwort:{/t}</label>
                <div class="col-md-10">
                    <input class="form-control" type="password" name="pw_old" autocomplete="current-password" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-form-label col-md-2">{t}Neues Passwort:{/t}</label>
                <div class="col-md-10">
                    <input class="form-control" type="password" name="pw_1" required autocomplete="new-password">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-form-label col-md-2">{t}Passwort Bestätigung:{/t}</label>
                <div class="col-md-10">
                    <input class="form-control" type="password" name="pw_2" required autocomplete="new-password">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-2 offset-md-2">
                    <button type="submit" class="btn btn-primary" name="change_pw">{t}Passwort ändern{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>