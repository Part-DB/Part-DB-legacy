<div class="card border-primary">
    <div class="card-header bg-primary text-white"><i class="fa fa-info-circle" aria-hidden="true"></i>
        {t}Benutzerinformationen{/t}</div>
    <div class="card-body row">
        <div class="col-md-2">
            {*
            <i class="fa fa-user fa-5x fa-border fa-pull-left" aria-hidden="true"></i>
             *}
            <div class="img-thumbnail">
                <img class="img-fluid img-rounded" src="{$avatar_url}">
            </div>
        </div>
        <div class="col-md-5">
            <div class="form form-horizontal">
                <div class="form-group row">
                    <label class="col-form-label col-md-4">{t}Vorname:{/t}</label>
                    <div class="col-md-8">
                        <p class="form-control-plaintext">{$firstname}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-4">{t}Nachname:{/t}</label>
                    <div class="col-md-8">
                        <p class="form-control-plaintext">{$lastname}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-4">{t}Email:{/t}</label>
                    <div class="col-md-8">
                        <p class="form-control-plaintext">{$email}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-4">{t}Abteilung:{/t}</label>
                    <div class="col-md-8">
                        <p class="form-control-plaintext">{$department}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form form-horizontal">
                <div class="form-group row">
                    <label class="col-form-label col-md-4">{t}Benutzername:{/t}</label>
                    <div class="col-md-8">
                        <p class="form-control-plaintext">{$username}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-4">{t}Gruppe:{/t}</label>
                    <div class="col-md-8">
                        <p class="form-control-plaintext">{$group}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>