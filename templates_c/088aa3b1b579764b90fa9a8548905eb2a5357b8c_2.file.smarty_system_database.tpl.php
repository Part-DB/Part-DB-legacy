<?php
/* Smarty version 3.1.31, created on 2017-09-22 15:20:52
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\system_database.php\smarty_system_database.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59c50e34bdb311_96168593',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '088aa3b1b579764b90fa9a8548905eb2a5357b8c' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\system_database.php\\smarty_system_database.tpl',
      1 => 1506085860,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59c50e34bdb311_96168593 (Smarty_Internal_Template $_smarty_tpl) {
echo smarty_function_locale(array('path'=>"nextgen/locale",'domain'=>"partdb"),$_smarty_tpl);?>


<?php if ($_smarty_tpl->tpl_vars['refresh_navigation_frame']->value) {?>
    <?php echo '<script'; ?>
 type="text/javascript">
        AjaxUI.getInstance().updateTrees();
    <?php echo '</script'; ?>
>
<?php }?>

<?php if (!$_smarty_tpl->tpl_vars['hide_status']->value && $_smarty_tpl->tpl_vars['can_status']->value) {?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-info-circle" aria-hidden="true"></i>
            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Datenbank Status / Update<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

        </div>
        <div class="panel-body">
            <form action="" method="post" class="form-horizontal no-progbar">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Eigenschaft</th>
                        <th>Wert</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Aktuelle Version:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

                        </td>
                        <td>
                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['current_version']->value, ENT_QUOTES, 'UTF-8');?>

                        </td>
                    </tr>

                    <tr>
                        <td>
                            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Benötigte Version:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

                        </td>
                        <td>
                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['latest_version']->value, ENT_QUOTES, 'UTF-8');?>

                        </td>
                    </tr>
                    </tbody>
                </table>

                <?php if (isset($_smarty_tpl->tpl_vars['update_required']->value) && $_smarty_tpl->tpl_vars['update_required']->value) {?>
                    <strong><span style="color: red; "><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Die Datenbank benötigt ein Update!<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span></strong><br>
                    <?php if ($_smarty_tpl->tpl_vars['last_update_failed']->value) {?>
                        <br>
                        <strong><span style="color: red; ">
                                        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
ACHTUNG:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
<br>
                                <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Das letzte Update ist fehlgeschlagen. Sie können beliebig oft versuchen,
                                    das Update an der Stelle des letzten Abbruchs fortzusetzen.
                                    Falls Sie zwischenzeitlich aber eine neue Datenbank geladen haben
                                    (z.B. ein Backup eingespielt), muss das Update jedoch wieder von Vorne gestartet werden.<br>
                                    Sie haben deshalb die folgenden zwei Möglichkeiten:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

                                    </span></strong>
                        <br>
                        <button type="submit" class="btn btn-default" name="make_update" <?php if (!$_smarty_tpl->tpl_vars['can_update']->value) {?>disabled<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Letztes, fehlgeschlagenes Update fortsetzen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                        <button type="submit" class="btn btn-default" name="make_new_update" <?php if (!$_smarty_tpl->tpl_vars['can_update']->value) {?>disabled<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Neuer Update-Versuch beginnen (von Vorne)<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                    <?php } else { ?>
                        <br>
                        <button class="btn btn-success" type="submit" name="make_update" <?php if (!$_smarty_tpl->tpl_vars['can_update']->value) {?>disabled<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Jetzt Datenbank updaten<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                    <?php }?>
                <?php } else { ?>
                    <span style="color: darkgreen; "><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Die Datenbank ist auf dem neusten Stand.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span>
                <?php }?>

            </form>
        </div>
    </div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['can_read_db_settings']->value) {?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <i class="fa fa-database" aria-hidden="true"></i>
            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Datenbank-Einstellungen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

        </div>
        <div class="panel-body">
            <form action="" class="form-horizontal" method="post">
                <div class="form-group">
                    <label class="control-label col-sm-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Datenbanktyp:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                    <div class="col-sm-9">
                        <select name="db_type" class="form-control" <?php if (!$_smarty_tpl->tpl_vars['can_edit_db_settings']->value) {?>disabled<?php }?>>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['db_type_loop']->value, 'db');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['db']->value) {
?>
                                <option value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['db']->value['value'], ENT_QUOTES, 'UTF-8');?>
" <?php if ($_smarty_tpl->tpl_vars['db']->value['selected']) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['db']->value['text'], ENT_QUOTES, 'UTF-8');?>
</option>
                            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label class="control-label col-sm-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Host:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="db_host"
                               value="<?php if (!$_smarty_tpl->tpl_vars['is_online_demo']->value) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['db_host']->value, ENT_QUOTES, 'UTF-8');
}?>" <?php if (!$_smarty_tpl->tpl_vars['can_edit_db_settings']->value) {?>disabled<?php }?>>
                        <!-- (nicht nötig für SQLite) -->
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Datenbankname:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="db_name"
                               value="<?php if (!$_smarty_tpl->tpl_vars['is_online_demo']->value) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['db_name']->value, ENT_QUOTES, 'UTF-8');
}?>" <?php if (!$_smarty_tpl->tpl_vars['can_edit_db_settings']->value) {?>disabled<?php }?>>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Benutzer:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="db_user"
                               value="<?php if (!$_smarty_tpl->tpl_vars['is_online_demo']->value) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['db_user']->value, ENT_QUOTES, 'UTF-8');
}?>" <?php if (!$_smarty_tpl->tpl_vars['can_edit_db_settings']->value) {?>disabled<?php }?>>
                        <!-- (nicht nötig für SQLite) -->
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Datenbankpasswort:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" name="db_password"
                               value="" <?php if (!$_smarty_tpl->tpl_vars['can_edit_db_settings']->value) {?>disabled<?php }?>>
                        <!-- (nicht nötig für SQLite) -->
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Administratorpasswort:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" name="admin_password"
                               value="" <?php if (!$_smarty_tpl->tpl_vars['can_edit_db_settings']->value) {?>disabled<?php }?>>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <button type="submit" class="btn btn-primary" name="apply_connection_settings"
                                <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?> <?php if (!$_smarty_tpl->tpl_vars['can_edit_db_settings']->value) {?>disabled<?php }?>>
                            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Einstellungen übernehmen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <div class="checkbox">
                            <input type="checkbox" name="automatic_updates_enabled"
                                    <?php if ($_smarty_tpl->tpl_vars['automatic_updates_enabled']->value) {?> checked<?php }?> <?php if (!$_smarty_tpl->tpl_vars['can_edit_db_settings']->value) {?>disabled<?php }?>>
                            <label for="automatic_updated_enabled"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Automatische Updates aktivieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <button class="btn btn-success" type="submit" name="apply_auto_updates" <?php if (!$_smarty_tpl->tpl_vars['can_edit_db_settings']->value) {?>disabled<?php }?>>
                            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Übernehmen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
<?php }
}
}
