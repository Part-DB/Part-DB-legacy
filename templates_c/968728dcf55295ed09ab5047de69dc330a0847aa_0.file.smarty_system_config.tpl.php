<?php
/* Smarty version 3.1.30, created on 2016-11-09 19:10:41
  from "C:\xampp\htdocs\part-db\templates\nextgen\system_config.php\smarty_system_config.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_582366a181fb82_46357975',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '968728dcf55295ed09ab5047de69dc330a0847aa' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\system_config.php\\smarty_system_config.tpl',
      1 => 1478715039,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_582366a181fb82_46357975 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['refresh_navigation_frame']->value)) {
echo '<script'; ?>
 type="text/javascript">
    parent.frames.navigation_frame.location.reload();
<?php echo '</script'; ?>
>
<?php }?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Systemeinstellungen</h4>
    </div>
    <div class="panel-body">
        
        <form class="form-horizontal" action="" method="post">
                
                <p><i>Auf dieser Seite sind nur die wichtigsten Einstellungen vorhanden, weitere
                Einstellungen kann man direkt in der "config.php" vornehmen. Mögliche Parameter
                entnehmen Sie bitte der "config_defaults.php" oder der Dokumentation.</i></p>
        
                <hr>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="theme">Theme:</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="theme">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['theme_loop']->value, 'theme');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['theme']->value) {
?>
                                <option value="<?php echo $_smarty_tpl->tpl_vars['theme']->value['value'];?>
" <?php if ($_smarty_tpl->tpl_vars['theme']->value['selected']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['theme']->value['text'];?>
</option>
                            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="custom_css">CSS-Datei:</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="custom_css">
                            <option value="">Standard des verwendeten Themes verwenden</option>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['custom_css_loop']->value, 'css');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['css']->value) {
?>
                                <option value="<?php echo $_smarty_tpl->tpl_vars['css']->value['value'];?>
" <?php if ($_smarty_tpl->tpl_vars['css']->value['selected']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['css']->value['text'];?>
</option>
                            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                        </select>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="timezon">Zeitzone:</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="timezone">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['timezone_loop']->value, 'timezone');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['timezone']->value) {
?>
                                <option value="<?php echo $_smarty_tpl->tpl_vars['timezone']->value['value'];?>
" <?php if ($_smarty_tpl->tpl_vars['timezone']->value['selected']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['timezone']->value['text'];?>
</option>
                            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="language">Sprache:</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="language">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['language_loop']->value, 'lang');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['lang']->value) {
?>
                                <option value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['value'];?>
" <?php if ($_smarty_tpl->tpl_vars['lang']->value['selected']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['lang']->value['text'];?>
</option>
                            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                        </select>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label for="checkbox-container" class="control-label col-sm-2">Allgemeine Einstellungen:</label>
                    
                    <div name="checkbox-container" class="col-sm-10">
                        <div class="checkbox">
                            <input type="checkbox" name="disable_updatelist" <?php if ($_smarty_tpl->tpl_vars['disable_updatelist']->value) {?> checked<?php }?>>
                            <label for="disable_updatelist">Updateliste (RSS-Feed) auf Startseite verstecken (verringert die Ladezeit)</label>
                        </div>
                
                        <div class="checkbox">
                            <input type="checkbox" name="disable_footprints" <?php if ($_smarty_tpl->tpl_vars['disable_footprints']->value) {?> checked<?php }?>>
                            <label for="disable_footprints">Footprints global deaktivieren *</label>
                        </div>

                        <div class="checkbox">
                            <input type="checkbox" name="disable_manufacturers" <?php if ($_smarty_tpl->tpl_vars['disable_manufacturers']->value) {?> checked<?php }?>>
                            <label for="disable_manufacturers">Hersteller global deaktivieren</label> 
                        </div>

                        <div class="checkbox">
                            <input type="checkbox" name="disable_devices" <?php if ($_smarty_tpl->tpl_vars['disable_devices']->value) {?> checked<?php }?>>
                            <label for="disable_devices">Baugruppenfunktion global deaktivieren *</label>
                        </div>
                    
                        <div class="checkbox">
                            <input type="checkbox" name="disable_auto_datasheets" <?php if ($_smarty_tpl->tpl_vars['disable_auto_datasheets']->value) {?> checked<?php }?>>
                            <label for="disable_auto_datasheets">Automatische Links zu Datenblättern global deaktivieren *</label>
                        </div>
                        
                        <div class="checkbox">
                            <input type="checkbox" name="disable_help" <?php if ($_smarty_tpl->tpl_vars['disable_help']->value) {?> checked<?php }?>>
                            <label for="disable_help">Menüpunkt "Hilfe" deaktivieren</label>
                        </div>
                        
                        <div class="checkbox">
                            <input type="checkbox" name="disable_config" <?php if ($_smarty_tpl->tpl_vars['disable_config']->value) {?> checked<?php }?> <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?>>
                            <label>Menüpunkt "System" deaktivieren</label>
                        </div>
                        
                        <div class="checkbox">
                            <input type="checkbox" name="enable_debug_link" <?php if ($_smarty_tpl->tpl_vars['enable_debug_link']->value) {?> checked<?php }?>>
                            <label>Menüpunkt "System -> Debugging" aktivieren</label>
                        </div>
                        
                        <div class="checkbox">
                            <input type="checkbox" name="disable_labels" <?php if ($_smarty_tpl->tpl_vars['disable_labels']->value) {?> checked<?php }?>>
                            <label>Menüpunkt "Tools -> Labels" deaktivieren *</label>
                        </div>
                        
                        <div class="checkbox">
                            <input type="checkbox" name="disable_calculator" <?php if ($_smarty_tpl->tpl_vars['disable_calculator']->value) {?> checked<?php }?>>
                            <label>Menüpunkt "Tools -> Widerstandsrechner" deaktivieren *</label>
                        </div>
                    
                        <div class="checkbox">
                            <input type="checkbox" name="disable_iclogos" <?php if ($_smarty_tpl->tpl_vars['disable_iclogos']->value) {?> checked<?php }?>>
                            <label>Menüpunkt "Tools -> IC-Logos" deaktivieren *</label>
                        </div>

                        <div class="checkbox">
                            <input type="checkbox" name="disable_tools_footprints" <?php if ($_smarty_tpl->tpl_vars['disable_tools_footprints']->value) {?> checked<?php }?>>
                            <label>Menüpunkt "Tools -> Footprints" deaktivieren *</label>
                        </div>
                    
                        <div class="checkbox">
                            <input type="checkbox" name="tools_footprints_autoload" <?php if ($_smarty_tpl->tpl_vars['tools_footprints_autoload']->value) {?> checked<?php }?>>
                            <label>Unter "Tools -> Footprints" beim Aufruf automatisch alle Bilder laden (lange Ladezeit!)</label>
                        </div>

                        <?php if ($_smarty_tpl->tpl_vars['developer_mode_available']->value) {?>
                        <div class="checkbox">
                            <input type="checkbox" name="enable_developer_mode" <?php if ($_smarty_tpl->tpl_vars['enable_developer_mode']->value) {?> checked<?php }?>>
                            <label>Entwickler-Werkzeuge aktivieren (für Entwickler und Tester)</label>
                        </div>

                        <div class="checkbox">
                            <input type="checkbox" name="enable_dokuwiki_write_perms" <?php if ($_smarty_tpl->tpl_vars['enable_dokuwiki_write_perms']->value) {?> checked<?php }?> <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?>>
                            <label>Schreibrechte im DokuWiki aktivieren</label>
                        </div>
                        <?php }?>
                        
                        
                        <p></p>
                        <div>
                            * <i>Durch das Aktivieren dieser Checkboxen ist Part-DB auch für Nicht-Elektronische Bauteile hervorragend geeignet.</i>
                        </div>
                    
                    </div>
         
                </div>
                   
                   
                

                <hr>

                <div class="form-group">
                    <label for="page_title" class="control-label col-sm-2">Titel der Seite:</label>
                    <div class="col-sm-10">
                        <input type="text" name="page_title" class="form-control" placeholder="Part-DB Elektronische Bauteile-Datenbank" value="<?php echo $_smarty_tpl->tpl_vars['page_title']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?>>
                    </div>
                </div>

                <div class="form-group">
                    <label for="startup_banner" class="control-label col-sm-2">Eigener Banner für die Startseite (HTML):</label>
                    <div class="col-sm-10">
                        <textarea name="startup_banner" rows="5" class="form-control"  <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?>><?php echo $_smarty_tpl->tpl_vars['startup_banner']->value;?>
</textarea>
                    </div>
                </div>

                <hr>

                <div class="col-sm-offset-2">
                    <button class="btn btn-success" type="submit" name="apply">Einstellungen übernehmen</button>
                    <button class="btn btn-danger" type="submit">Änderungen verwerfen</button>
                </div>
            </table>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Administratorpasswort ändern</h4>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" method="post">
            <div class="form-group">
                <label class="control-label col-sm-2" for="current_admin_password">Aktuelles Passwort:</label>
                <div class="col-sm-10">
                    <input class="form-control" type="password" name="current_admin_password" <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?>>
                </div>
            </div>

            <div class="form-group">
                <label for="new_admin_password_1" class="col-sm-2 control-label">Neues Passwort:</label>
                    <div class="col-sm-10">
                        <input type="password"  class="form-control" name="new_admin_password_1" <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?>>
                    </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="new_admin_password_2">Neues Passwort (Wiederholung):</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="new_admin_password_2" <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?>>
                </div>
            </div>

           <hr>
                    
            <div class="col-sm-offset-2">
                <input type="submit" class="btn btn-success" name="change_admin_password" value="Passwort ändern" <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?>>
            </div>
            
        </form>
    </div>
</div>

<?php if (!$_smarty_tpl->tpl_vars['is_online_demo']->value) {?>
    <div class="panel panel-default">
       <div class="panel-heading">
            <h2>Server</h2>
        </div>
        <div class="panel-body">
            <table width="100%">
                <tr>
                    <td><b>PHP-Version:</b></td>
                    <td><?php echo $_smarty_tpl->tpl_vars['php_version']->value;?>
</td>
                </tr>
                <tr>
                    <td><b>.htaccess funktioniert:</b></td>
                    <td><?php if ($_smarty_tpl->tpl_vars['htaccess_works']->value) {?><font color="green">ja</font><?php } else { ?><font color="red">nein</font><?php }?></td>
                </tr>
            </table>
        </div>
    </div>
<?php }
}
}
