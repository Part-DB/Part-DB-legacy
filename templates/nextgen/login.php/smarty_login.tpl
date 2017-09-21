{if isset($refresh_navigation_frame) && $refresh_navigation_frame}
    <script type="text/javascript">
        //location.href = location.href.replace("?logout", "");
        location.reload();
    </script>
{/if}

{if isset($loggedout)}
    <div class="panel panel-success">
        <div class="panel-heading">Erfolg</div>
        <div class="panel-body">
            <p>Erfolgreich ausgeloggt.</p>
            {* We need this because $refresh_navigation_frame does not work and I dont know why... *}
            <img src onerror='location.href = location.href.replace("?logout", "");'>
        </div>
    </div>
{/if}

{if isset($pw_valid) && $pw_valid == false}
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Achtung!</strong> Der Benutzername oder das Password waren falsch!
    </div>
{/if}

{if isset($loggedin) && $loggedin}
    <div class="panel panel-success">
        <div class="panel-heading">Erfolg</div>
        <div class="panel-body">
            <p>Erfolgreich eingeloggt.</p>
            <form action="login.php" method="post" class="no-progbar">
                <button class="btn btn-primary" type="submit" name="logout">Logout</button>
            </form>
        </div>
    </div>
{else}
        <div class="panel panel-primary">
            <div class="panel-heading"><h4><i class="fa fa-sign-in" aria-hidden="true"></i>
                    Login</h4></div>
            <div class="panel-body">
                <form action="login.php" class="form-horizontal no-progbar" method="post">

                    <div class="form-group">
                        <label class="control-label col-md-2">Benutzername:</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="Nutzername" name="username" value="{$username}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">Password:</label>
                        <div class="col-md-10">
                            <input type="password" class="form-control" placeholder="Password" name="password">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-10 col-md-offset-2">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
{/if}