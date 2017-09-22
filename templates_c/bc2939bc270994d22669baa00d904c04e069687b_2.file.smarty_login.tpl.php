<?php
/* Smarty version 3.1.31, created on 2017-09-22 15:21:34
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\login.php\smarty_login.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59c50e5e00ae81_12967673',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bc2939bc270994d22669baa00d904c04e069687b' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\login.php\\smarty_login.tpl',
      1 => 1506085860,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59c50e5e00ae81_12967673 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['refresh_navigation_frame']->value) && $_smarty_tpl->tpl_vars['refresh_navigation_frame']->value) {?>
    <?php echo '<script'; ?>
 type="text/javascript">
        //location.href = location.href.replace("?logout", "");
        location.reload();
    <?php echo '</script'; ?>
>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['loggedout']->value)) {?>
    <div class="panel panel-success">
        <div class="panel-heading">Erfolg</div>
        <div class="panel-body">
            <p>Erfolgreich ausgeloggt.</p>
            
            <img src onerror='location.href = location.href.replace("?logout", "");'>
        </div>
    </div>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['pw_valid']->value) && $_smarty_tpl->tpl_vars['pw_valid']->value == false) {?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Achtung!</strong> Der Benutzername oder das Password waren falsch!
    </div>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['loggedin']->value) && $_smarty_tpl->tpl_vars['loggedin']->value) {?>
    <div class="panel panel-success">
        <div class="panel-heading">Erfolg</div>
        <div class="panel-body">
            <p>Erfolgreich eingeloggt.</p>
            <form action="login.php" method="post" class="no-progbar">
                <button class="btn btn-primary" type="submit" name="logout">Logout</button>
            </form>
        </div>
    </div>
<?php } else { ?>
        <div class="panel panel-primary">
            <div class="panel-heading"><h4><i class="fa fa-sign-in" aria-hidden="true"></i>
                    Login</h4></div>
            <div class="panel-body">
                <form action="login.php" class="form-horizontal no-progbar" method="post">

                    <div class="form-group">
                        <label class="control-label col-md-2">Benutzername:</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="Nutzername" name="username" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['username']->value, ENT_QUOTES, 'UTF-8');?>
">
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
<?php }
}
}
