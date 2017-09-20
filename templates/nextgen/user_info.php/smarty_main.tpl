<div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-info-circle" aria-hidden="true"></i>
        {t}Benutzerinformationen{/t}</div>
    <div class="panel-body">
        <div class="col-md-2">
            {*
            <i class="fa fa-user fa-5x fa-border fa-pull-left" aria-hidden="true"></i>
             *}
            <div class="thumbnail">
                <img class="img-responsive img-rounded" src="{$avatar_url}">
            </div>
        </div>
        <div class="col-md-5">
            <div class="form form-horizontal">
                <div class="form-group">
                    <label class="control-label col-md-4">{t}Vorname:{/t}</label>
                    <div class="col-md-8">
                        <p class="form-control-static">{$firstname}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4">{t}Nachname:{/t}</label>
                    <div class="col-md-8">
                        <p class="form-control-static">{$lastname}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4">{t}Email:{/t}</label>
                    <div class="col-md-8">
                        <p class="form-control-static">{$email}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4">{t}Abteilung:{/t}</label>
                    <div class="col-md-8">
                        <p class="form-control-static">{$department}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form form-horizontal">
                <div class="form-group">
                    <label class="control-label col-md-4">{t}Benutzername:{/t}</label>
                    <div class="col-md-8">
                        <p class="form-control-static">{$username}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4">{t}Gruppe:{/t}</label>
                    <div class="col-md-8">
                        <p class="form-control-static">{$group}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>