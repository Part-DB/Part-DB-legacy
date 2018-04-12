<?php
/* Smarty version 3.1.31, created on 2017-09-29 12:37:31
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\system_config.php\smarty_system_config.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59ce226b1ad956_04779964',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '57e6a2d6f861fc6e1dd122adaecbee27222cd3ed' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\system_config.php\\smarty_system_config.tpl',
      1 => 1506681393,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59ce226b1ad956_04779964 (Smarty_Internal_Template $_smarty_tpl) {
echo smarty_function_locale(array('path'=>"nextgen/locale",'domain'=>"partdb"),$_smarty_tpl);?>

<?php if (isset($_smarty_tpl->tpl_vars['refresh_navigation_frame']->value)) {?>
    <?php echo '<script'; ?>
 type="text/javascript">
        location.reload();
    <?php echo '</script'; ?>
>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['can_read']->value) {?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-cog" aria-hidden="true"></i> <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Systemeinstellungen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

        </div>
        <div class="panel-body">
            <form class="form-horizontal" action="" method="post" class="no-progbar">
                <p><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Auf dieser Seite sind nur die wichtigsten Einstellungen vorhanden, weitere Einstellungen kann man direkt in der "config.php" vornehmen. Mögliche Parameter entnehmen Sie bitte der "config_defaults.php" oder der Dokumentation.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</p>

                <hr>

                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#appearance" class="link-anchor">
                        <span class="fa-stack">
                            <i class="fa fa-square-o fa-stack-2x"></i>
                            <i class="fa fa-magic fa-stack-1x"></i>
                        </span>
                            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Aussehen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a></li>
                    <li><a data-toggle="tab" href="#features" class="link-anchor">
                         <span class="fa-stack">
                            <i class="fa fa-square-o fa-stack-2x"></i>
                            <i class="fa fa-sliders fa-stack-1x"></i>
                        </span>
                            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Funktionen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a></li>
                    <li><a data-toggle="tab" href="#misc" class="link-anchor">
                        <span class="fa-stack">
                            <i class="fa fa-square-o fa-stack-2x"></i>
                            <i class="fa fa-gears fa-stack-1x"></i>
                        </span>
                            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Sonstiges<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a></li>
                    <?php if ($_smarty_tpl->tpl_vars['developer_mode_available']->value) {?><li><a data-toggle="tab" href="#dev" class="link-anchor">
                        <span class="fa-stack">
                            <i class="fa fa-square-o fa-stack-2x"></i>
                            <i class="fa fa-code fa-stack-1x"></i>
                        </span>
                            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Entwickler<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

                        </a></li><?php }?>
                </ul>

                <div class="tab-content">
                    <div id="appearance" class="tab-pane fade in active">
                        <br>

                        

                        <div class="form-group">
                            <label class="control-label col-sm-2" for="custom_css"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Theme:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="custom_css">
                                    <option value=""><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Standardmäßiges Theme<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</option>
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['custom_css_loop']->value, 'css');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['css']->value) {
?>
                                        <option value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['css']->value['value'], ENT_QUOTES, 'UTF-8');?>
" <?php if ($_smarty_tpl->tpl_vars['css']->value['selected']) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['css']->value['text'], ENT_QUOTES, 'UTF-8');?>
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
                            <label for="checkbox-container" class="control-label col-sm-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Aussehen:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                            <div class="checkbox-container col-sm-10">
                                <div class="checkbox">
                                    <input type="checkbox" name="use_old_datasheet_icons" <?php if ($_smarty_tpl->tpl_vars['use_old_datasheet_icons']->value) {?> checked<?php }?>>
                                    <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Alte (farbige) Icons für automatisch erzeugte Datenblattlinks benutzen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="short_description" <?php if ($_smarty_tpl->tpl_vars['short_description']->value) {?> checked<?php }?>>
                                    <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Länge der Bauteilebeschreibungen in den Übersichtstabellen begrenzen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="checkbox-container" class="control-label col-sm-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Detailinfos:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                            <div class="checkbox-container col-sm-10">
                                <div class="checkbox">
                                    <input type="checkbox" name="info_hide_actions" <?php if ($_smarty_tpl->tpl_vars['info_hide_actions']->value) {?> checked<?php }?>>
                                    <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Verstecke "Aktionen" Dialog in den Detailinfos<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="info_hide_empty_orderdetails" <?php if ($_smarty_tpl->tpl_vars['info_hide_empty_orderdetails']->value) {?> checked<?php }?>>
                                    <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Verstecke "Einkaufsinformationen" Panel, wenn keine Einkaufsinformationen vorhanden sind.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="info_hide_empty_attachements" <?php if ($_smarty_tpl->tpl_vars['info_hide_empty_attachements']->value) {?> checked<?php }?>>
                                    <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Verstecke "Dateianhänge" Panel, wenn keine Dateianhänge vorhanden sind.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="features" class="tab-pane fade">
                        <br>
                        <div class="form-group">
                            <label for="checkbox-container" class="control-label col-sm-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Allgemein:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>

                            <div id="checkbox-container" class="col-sm-10">
                                <div class="checkbox">
                                    <input type="checkbox" name="disable_updatelist" <?php if ($_smarty_tpl->tpl_vars['disable_updatelist']->value) {?> checked<?php }?>>
                                    <label for="disable_updatelist"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Updateliste (RSS-Feed) auf Startseite verstecken (verringert die Ladezeit)<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>

                                <div class="checkbox">
                                    <input type="checkbox" name="disable_footprints" <?php if ($_smarty_tpl->tpl_vars['disable_footprints']->value) {?> checked<?php }?>>
                                    <label for="disable_footprints"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Footprints global deaktivieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 *</label>
                                </div>

                                <div class="checkbox">
                                    <input type="checkbox" name="disable_manufacturers" <?php if ($_smarty_tpl->tpl_vars['disable_manufacturers']->value) {?> checked<?php }?>>
                                    <label for="disable_manufacturers"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Hersteller global deaktivieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>

                                <div class="checkbox">
                                    <input type="checkbox" name="disable_suppliers" <?php if ($_smarty_tpl->tpl_vars['disable_suppliers']->value) {?> checked<?php }?>>
                                    <label for="disable_suppliers"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Lieferanten und Einkaufsinformationen global deaktivieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>


                                <div class="checkbox">
                                    <input type="checkbox" name="disable_devices" <?php if ($_smarty_tpl->tpl_vars['disable_devices']->value) {?> checked<?php }?>>
                                    <label for="disable_devices"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Baugruppenfunktion global deaktivieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 *</label>
                                </div>

                                <div class="checkbox">
                                    <input type="checkbox" name="disable_auto_datasheets" <?php if ($_smarty_tpl->tpl_vars['disable_auto_datasheets']->value) {?> checked<?php }?>>
                                    <label for="disable_auto_datasheets"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Automatische Links zu Datenblättern global deaktivieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 *</label>
                                </div>

                                <div class="checkbox">
                                    <input type="checkbox" name="disable_help" <?php if ($_smarty_tpl->tpl_vars['disable_help']->value) {?> checked<?php }?>>
                                    <label for="disable_help"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Menüpunkt "Hilfe" deaktivieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>

                                <div class="checkbox">
                                    <input type="checkbox" name="disable_config" <?php if ($_smarty_tpl->tpl_vars['disable_config']->value) {?> checked<?php }?> <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?>>
                                    <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Menüpunkt "System" deaktivieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>

                                <div class="checkbox">
                                    <input type="checkbox" name="disable_labels" <?php if ($_smarty_tpl->tpl_vars['disable_labels']->value) {?> checked<?php }?>>
                                    <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Menüpunkt "Tools -> Labels" deaktivieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 *</label>
                                </div>

                                <div class="checkbox">
                                    <input type="checkbox" name="disable_calculator" <?php if ($_smarty_tpl->tpl_vars['disable_calculator']->value) {?> checked<?php }?>>
                                    <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Menüpunkt "Tools -> Widerstandsrechner" deaktivieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 *</label>
                                </div>

                                <div class="checkbox">
                                    <input type="checkbox" name="disable_iclogos" <?php if ($_smarty_tpl->tpl_vars['disable_iclogos']->value) {?> checked<?php }?>>
                                    <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Menüpunkt "Tools -> IC-Logos" deaktivieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 *</label>
                                </div>

                                <div class="checkbox">
                                    <input type="checkbox" name="disable_tools_footprints" <?php if ($_smarty_tpl->tpl_vars['disable_tools_footprints']->value) {?> checked<?php }?>>
                                    <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Menüpunkt "Tools -> Footprints" deaktivieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 *</label>
                                </div>

                                <div class="checkbox">
                                    <input type="checkbox" name="tools_footprints_autoload" <?php if ($_smarty_tpl->tpl_vars['tools_footprints_autoload']->value) {?> checked<?php }?>>
                                    <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Unter "Tools -> Footprints" beim Aufruf automatisch alle Bilder laden (lange Ladezeit!)<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>

                                <br>
                                <div>
                                    * <i><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Durch das Aktivieren dieser Checkboxen ist Part-DB auch für Nicht-Elektronische Bauteile hervorragend geeignet.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</i>
                                </div>

                            </div>

                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="modal-container" class="control-label col-sm-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
3D-Footprints:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <input type="checkbox" name="foot3d_active" <?php if ($_smarty_tpl->tpl_vars['foot3d_active']->value) {?> checked<?php }?>>
                                    <label for="foot3d_active"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
3D-Footprints aktiviert<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="modal-container" class="control-label col-sm-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Bauteilebearbeitung:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <input type="checkbox" name="created_redirect" <?php if ($_smarty_tpl->tpl_vars['created_redirect']->value) {?> checked<?php }?>>
                                    <label for="properties_active"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Springe zu Bauteileübersicht, nachdem ein neues Teil angelegt wurde.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="saved_redirect" <?php if ($_smarty_tpl->tpl_vars['saved_redirect']->value) {?> checked<?php }?>>
                                    <label for="properties_active"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Springe zu Bauteileübersicht, nachdem ein neues Teil bearbeitet und gespeichert wurde.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                                <p class="help-block"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Tipp: Wird der Dialog zur Erzeugung bzw. Bearbeitung von Bauteilen mit einem Rechtsklick bestätigt, so werden obige Einstellungen, für diese Aktion umgekehrt.<?php $_block_repeat=false;
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
So wird bei einem Rechtsklick auf "Bauteil anlegen", auch ohne oben gesetzen Haken, auf die Übersichtsseite des neuen Bauteils umgeleitet, und umgekehrt.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="modal-container" class="control-label col-sm-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Bauteileeigenschaften:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <input type="checkbox" name="properties_active" <?php if ($_smarty_tpl->tpl_vars['properties_active']->value) {?> checked<?php }?>>
                                    <label for="properties_active"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Bauteileigenschaften global aktiv.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="modal-container" class="control-label col-sm-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Bauteilesuche:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <input type="checkbox" name="livesearch_active" <?php if ($_smarty_tpl->tpl_vars['livesearch_active']->value) {?> checked<?php }?>>
                                    <label for="properties_active"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Suche bereits während der Eingabe in das Suchfeld (Livesuche).<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="search_highlighting" <?php if ($_smarty_tpl->tpl_vars['search_highlighting']->value) {?> checked<?php }?>>
                                    <label for="properties_active"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Hebe den Suchbegriff in den Ergebnissen hervor (Highlighting).<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="modal-container" class="control-label col-sm-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Bauteiletabellen:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <input type="checkbox" name="table_autosort" <?php if ($_smarty_tpl->tpl_vars['table_autosort']->value) {?> checked<?php }?>>
                                    <label for="table_autosort"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Aktiviere initiale Sortierung.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="default_subcat" <?php if ($_smarty_tpl->tpl_vars['default_subcat']->value) {?> checked<?php }?>>
                                    <label for="default_subcat"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Zeige die beim Auflisten aller Teile einer Kategorie, die Unterkategorien standarmäßig.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="control-label col-sm-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Dateianhänge:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <input type="checkbox" name="attachements_structure" <?php if ($_smarty_tpl->tpl_vars['attachements_structure']->value) {?> checked<?php }?>>
                                    <label for="table_autosort"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Speichere Anhänge in Ordnerstruktur, ähnlich der Kategorienhierachie.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="attachements_download" <?php if ($_smarty_tpl->tpl_vars['attachements_download']->value) {?> checked<?php }?>>
                                    <label for="table_autosort"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Lade Medien von externen Quellen standardmäßig herunter.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="attachements_show_name" <?php if ($_smarty_tpl->tpl_vars['attachements_show_name']->value) {?> checked<?php }?>>
                                    <label for="table_autosort"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Zeige die Namen der Anhängen in der Übersichtstabelle (statt Icons).<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <?php if ($_smarty_tpl->tpl_vars['developer_mode_available']->value) {?>
                        <br>
                        <div id="dev" class="tab-pane fade">
                            <div class="form-group">
                                <label for="checkbox-container" class="control-label col-sm-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Entwickleroptionen:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div id="checkbox-container" class="col-sm-10">
                                    <div class="checkbox">
                                        <input type="checkbox" name="enable_developer_mode" <?php if ($_smarty_tpl->tpl_vars['enable_developer_mode']->value) {?> checked<?php }?>>
                                        <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Entwickler-Werkzeuge aktivieren (für Entwickler und Tester)<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                    </div>

                                    <div class="checkbox">
                                        <input type="checkbox" name="enable_debug_link" <?php if ($_smarty_tpl->tpl_vars['enable_debug_link']->value) {?> checked<?php }?>>
                                        <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Menüpunkt "System -> Debugging" aktivieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }?>

                    <div id="misc" class="tab-pane fade">
                        <br>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="timezon"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Zeitzone:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                            <div class="col-sm-10">
                                <select class="form-control selectpicker" data-live-search="true" name="timezone">
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['timezone_loop']->value, 'timezone');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['timezone']->value) {
?>
                                        <option value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['timezone']->value['value'], ENT_QUOTES, 'UTF-8');?>
" <?php if ($_smarty_tpl->tpl_vars['timezone']->value['selected']) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['timezone']->value['text'], ENT_QUOTES, 'UTF-8');?>
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
                            <label class="control-label col-sm-2" for="language"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Sprache:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="language">
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['language_loop']->value, 'lang');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['lang']->value) {
?>
                                        <option value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['lang']->value['value'], ENT_QUOTES, 'UTF-8');?>
" <?php if ($_smarty_tpl->tpl_vars['lang']->value['selected']) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['lang']->value['text'], ENT_QUOTES, 'UTF-8');?>
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
                            <label for="page_title" class="control-label col-sm-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Titel der Seite:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                            <div class="col-sm-10">
                                <input type="text" name="page_title" class="form-control" placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Part-DB Elektronische Bauteile-Datenbank<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['page_title']->value, ENT_QUOTES, 'UTF-8');?>
" <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?>>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="startup_banner" class="control-label col-sm-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Eigener Banner für die Startseite (BB-Code):<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                            <div class="col-sm-10">
                                <textarea name="startup_banner" rows="5" class="form-control"  <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['startup_banner']->value, ENT_QUOTES, 'UTF-8');?>
</textarea>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <div class="col-sm-10 col-sm-offset-2">
                                <div class="checkbox">
                                    <input type="checkbox" name="downloads_enable" <?php if ($_smarty_tpl->tpl_vars['downloads_enable']->value) {?> checked<?php }?>>
                                    <label for="downloads_enable"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Erlaube Nutzern Dateien (z.B. Anhänge) über den Server herunterzuladen.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                                <p class="help-block"><b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Achtung:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</b> <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Wenn diese Option aktiviert ist, können Benutzer potentiell, Dateien von jedem Server herunterladen, auf den dieser Server Zugriff hat.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

                                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Dies könnte einen Angreifer in die Lage versetzen, auf Dateien von internen Servern zuzugreifen.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-10 col-sm-offset-2">
                                <div class="checkbox">
                                    <input type="checkbox" name="gravatar_enable" <?php if ($_smarty_tpl->tpl_vars['gravatar_enable']->value) {?> checked<?php }?>>
                                    <label for="downloads_enable"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Benutze Gravatar für Benutzeravatare.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                                <p class="help-block"><b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Achtung:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</b> <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Wenn diese Option aktiv ist, werden die Email Addressen der Benutzer in MD5 gehashter Form an die Server von Gravatar gesendet.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</p>
                            </div>
                        </div>

                        <br>

                    </div>

                    <div class="col-sm-offset-2">
                        <button class="btn btn-success" type="submit" name="apply" <?php if (!$_smarty_tpl->tpl_vars['can_edit']->value) {?>disabled<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
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
                        <button class="btn btn-danger" type="submit" <?php if (!$_smarty_tpl->tpl_vars['can_edit']->value) {?>disabled<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Änderungen verwerfen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                    </div>
            </form>
        </div>
    </div>
    </div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['can_password']->value) {?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-lock" aria-hidden="true"></i>
            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Administratorpasswort ändern<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

        </div>
        <div class="panel-body">
            <form class="form-horizontal no-progbar" method="post">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="current_admin_password"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Aktuelles Passwort:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="password" name="current_admin_password" <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?> required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_admin_password_1" class="col-sm-2 control-label"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Neues Passwort:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                    <div class="col-sm-10">
                        <input type="password"  class="form-control" name="new_admin_password_1" <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?> required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="new_admin_password_2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Neues Passwort (Wiederholung):<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" name="new_admin_password_2" <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?> required>
                    </div>
                </div>

                <hr>

                <div class="col-sm-offset-2">
                    <button type="submit" class="btn btn-success" name="change_admin_password" <?php if ($_smarty_tpl->tpl_vars['is_online_demo']->value) {?>disabled<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Passwort ändern<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                </div>

            </form>
        </div>
    </div>
<?php }?>

<?php if (!$_smarty_tpl->tpl_vars['is_online_demo']->value && $_smarty_tpl->tpl_vars['can_infos']->value) {?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-server" aria-hidden="true"></i>
            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Server<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

        </div>
        <div class="panel-body">
            <table width="" class="table table-condensed">
                <thead>
                <tr>
                    <th><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Eigenschaft<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</th>
                    <th><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Wert<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
PHP-Version:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</b></td>
                    <td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['php_version']->value, ENT_QUOTES, 'UTF-8');?>
</td>
                </tr>
                <tr>
                    <td><b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
.htaccess funktioniert:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</b></td>
                    <td><?php if ($_smarty_tpl->tpl_vars['htaccess_works']->value) {?><span class="text-success"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
ja<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span><?php } else { ?>
                            <span class="text-danger font-weight-bold"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
nein<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span><?php }?></td>
                </tr>
                <tr>
                    <td><b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Verbindung benutzt HTTPS:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</b></td>
                    <td><?php if ($_smarty_tpl->tpl_vars['using_https']->value) {?><span class="text-success"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
ja<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span><?php } else { ?>
                            <span class="text-danger font-weight-bold"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
nein<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span><?php }?></td>
                </tr>
                <tr>
                    <td><b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Max. Input Vars:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</b></td>
                    <td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['max_input_vars']->value, ENT_QUOTES, 'UTF-8');?>
</td>
                </tr>
                <tr>
                    <td><b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Maximale Dateigröße beim Upload:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</b></td>
                    <td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['max_upload_filesize']->value, ENT_QUOTES, 'UTF-8');?>
B</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php }
}
}
