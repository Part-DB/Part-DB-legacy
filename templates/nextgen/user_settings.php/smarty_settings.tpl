{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-cogs" aria-hidden="true"></i>
        {t}Benutzereinstellungen{/t}</div>
    <div class="panel-body">
        <form class="form form-horizontal" method="post" class="no-progbar">
            <div class="form-group">
                <label class="control-label col-md-3">{t}Benutzername:{/t}</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{$username}" name="username"
                           placeholder="{t}z.B. m.muster{/t}" required {if !$can_username}disabled{/if}>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3">{t}Vorname:{/t}</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{$firstname}"  name="firstname"
                           placeholder="{t}z.B. Max{/t}" {if !$can_infos}disabled{/if}>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3">{t}Nachname:{/t}</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{$lastname}" name="lastname"
                           placeholder="{t}z.B. Muster{/t}" {if !$can_infos}disabled{/if}>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3">{t}Email:{/t}</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{$email}"  name="email"
                           placeholder="{t}z.B. m.muster@ecorp.com{/t}" {if !$can_infos}disabled{/if}>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3">{t}Abteilung:{/t}</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{$department}"  name="department"
                           placeholder="{t}z.B. Entwicklung{/t}" {if !$can_infos}disabled{/if}>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                    <button class="btn btn-primary" type="submit" name="apply_settings" {if !$can_infos && !$can_username}disabled{/if}>
                        {t}Änderungen übernehmen{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>