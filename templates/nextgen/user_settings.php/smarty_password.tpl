{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-key" aria-hidden="true"></i>
        {t}Passwort ändern{/t}</div>
    <div class="panel-body">
        <form class="no-progbar form-horizontal" method="post">
            <div class="form-group">
                <label class="control-label col-md-2">{t}Altes Passwort:{/t}</label>
                <div class="col-md-10">
                    <input class="form-control" type="password" name="pw_old" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-2">{t}Neues Passwort:{/t}</label>
                <div class="col-md-10">
                    <input class="form-control" type="password" name="pw_1" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-2">{t}Passwort Bestätigung:{/t}</label>
                <div class="col-md-10">
                    <input class="form-control" type="password" name="pw_2" required>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2 col-md-offset-2">
                    <button type="submit" class="btn btn-primary" name="change_pw">{t}Passwort ändern{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>