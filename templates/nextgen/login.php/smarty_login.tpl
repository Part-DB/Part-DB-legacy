{if isset($refresh_navigation_frame) && $refresh_navigation_frame}
    <script type="text/javascript">
        location.href = location.href.replace("?logout", "");
        //location.reload();
    </script>
{/if}

{if isset($loggedout)}
    <div class="card border-success">
        <div class="card-header bg-success text-white">{t}Erfolg{/t}</div>
        <div class="card-body">
            <p>{t}Erfolgreich ausgeloggt.{/t}</p>
            {* We need this because $refresh_navigation_frame does not work and I dont know why... *}
            <img src onerror='location.href = location.href.replace("?logout", "");'>
        </div>
    </div>
{/if}

{if isset($pw_valid) && $pw_valid == false}
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>{t}Achtung!{/t}</strong> {t}Der Benutzername oder das Password waren falsch!{/t}
    </div>
{/if}

{if isset($loggedin) && $loggedin}
    <div class="card border-success">
        <div class="card-header bg-success text-white">{t}Erfolg{/t}</div>
        <div class="card-body">
            <p>{t}Erfolgreich eingeloggt.{/t}</p>
            <form action="login.php" method="post" class="no-progbar">
                <button class="btn btn-primary" type="submit" name="logout">{t}Logout{/t}</button>
            </form>
        </div>
    </div>
{else}
        <div class="card border-primary">
            <div class="card-header text-white bg-primary"><h4><i class="fa fa-sign-in-alt" aria-hidden="true"></i>
                    {t}Login{/t}</h4></div>
            <div class="card-body">
                <form class="form-horizontal no-progbar" method="post">

                    <div class="form-group">
                        <label class="control-label col-2">{t}Benutzername:{/t}</label>
                        <div class="col-10">
                            <input type="text" class="form-control" placeholder="{t}Nutzername{/t}" name="username" value="{$username}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-2">{t}Password:{/t}</label>
                        <div class="col-10">
                            <input type="password" class="form-control" placeholder="{t}Password{/t}" name="password">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-10 col-offset-2">
                            <button type="submit" class="btn btn-primary">{t}Login{/t}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
{/if}