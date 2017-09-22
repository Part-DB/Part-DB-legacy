<?php
/* Smarty version 3.1.31, created on 2017-09-22 15:13:21
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\startup.php\smarty_startup.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59c50c71435965_04575191',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '336b1f6a5623ecf3cfc3c3033c84cfb46ab54f6a' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\startup.php\\smarty_startup.tpl',
      1 => 1506085860,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59c50c71435965_04575191 (Smarty_Internal_Template $_smarty_tpl) {
echo smarty_function_locale(array('path'=>"nextgen/locale",'domain'=>"partdb"),$_smarty_tpl);?>


<?php if (isset($_smarty_tpl->tpl_vars['must_change_pw']->value) && $_smarty_tpl->tpl_vars['must_change_pw']->value) {?>
    <div class="alert alert-danger">
        <h4><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Password Änderung erforderlich!<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</h4>
        <strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Aus Sicherheitsgründen müssen sie ihr Password ändern.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong>
        <p><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('escape'=>false));
$_block_repeat=true;
echo smarty_block_t(array('escape'=>false), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Besuchen sie hierzu in die <a href="user_settings.php">Benutzeinstellungen</a>.<?php $_block_repeat=false;
echo smarty_block_t(array('escape'=>false), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</p>
    </div>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['database_update']->value) && $_smarty_tpl->tpl_vars['database_update']->value) {?>
    <?php if ($_smarty_tpl->tpl_vars['database_update']->value) {?>
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3>
                    <i class="fa fa-database" aria-hidden="true"></i>
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Datenbankupdate<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

                </h3>
            </div>
            <div class="panel-body">
                <b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array(1=>$_smarty_tpl->tpl_vars['db_version_current']->value,2=>$_smarty_tpl->tpl_vars['db_version_latest']->value));
$_block_repeat=true;
echo smarty_block_t(array(1=>$_smarty_tpl->tpl_vars['db_version_current']->value,2=>$_smarty_tpl->tpl_vars['db_version_latest']->value), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Datenbank-Version %1 benötigt ein Update auf Version %2.<?php $_block_repeat=false;
echo smarty_block_t(array(1=>$_smarty_tpl->tpl_vars['db_version_current']->value,2=>$_smarty_tpl->tpl_vars['db_version_latest']->value), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</b><br><br>
                <?php if (isset($_smarty_tpl->tpl_vars['disabled_autoupdate']->value)) {?>
                    <?php if (isset($_smarty_tpl->tpl_vars['auto_disabled_autoupdate']->value)) {?>
                        <p><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Automatische Datenbankupdates wurden vorübergehend automatisch deaktiviert,
                                da es sich um ein sehr umfangreiches Update handelt.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</p>
                    <?php } else { ?>
                        <p><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Automatische Datenbankupdates sind deaktiviert.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</p>
                    <?php }?>
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Updates bitte manuell durchführen:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 <a href="system_database.php"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
System -> Datenbank<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a>
                <?php } else { ?>
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['database_update_log']->value, ENT_QUOTES, 'UTF-8');?>

                <?php }?>
            </div>
        </div>
    <?php }
}?>


<div class="jumbotron">
    <h1><?php if (!empty($_smarty_tpl->tpl_vars['partdb_title']->value)) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['partdb_title']->value, ENT_QUOTES, 'UTF-8');
} else { ?>Part-DB<?php }?></h1>
    <?php if (isset($_smarty_tpl->tpl_vars['system_version_full']->value)) {?>
        <h3><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Version:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['system_version_full']->value, ENT_QUOTES, 'UTF-8');
if (!empty($_smarty_tpl->tpl_vars['git_branch']->value)) {?>, Git: <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['git_branch']->value, ENT_QUOTES, 'UTF-8');
if (isset($_smarty_tpl->tpl_vars['git_commit']->value)) {?>/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['git_commit']->value, ENT_QUOTES, 'UTF-8');
}
}?></h3>
    <?php }?>
    <h4><i>"NextGen"</i></h4>

    <?php if (!empty($_smarty_tpl->tpl_vars['banner']->value)) {?>
        <hr>
        <div>
            <h4><?php echo $_smarty_tpl->tpl_vars['banner']->value;?>
</h4>
        </div>
    <?php }?>
</div>



<?php if (isset($_smarty_tpl->tpl_vars['display_warning']->value) && $_smarty_tpl->tpl_vars['display_warning']->value) {?>
    <div class="panel panel-danger">
        <div class="panel-heading">
            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Achtung!<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

        </div>
        <div class="panel-body">
            Bitte beachten Sie, dass vor der Verwendung der Datenbank mindestens<br>
            <blockquote><?php echo $_smarty_tpl->tpl_vars['missing_category']->value;?>
eine <a href="edit_categories.php"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Kategorie<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a> </blockquote>hinzufügt werden muss.<br><br>
            Um das Potential der Suchfunktion zu nutzen, wird empfohlen
            <blockquote><?php echo $_smarty_tpl->tpl_vars['missing_storeloc']->value;?>
einen <a href="edit_storelocations.php"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Lagerort<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a> </blockquote>
            <blockquote><?php echo $_smarty_tpl->tpl_vars['missing_footprint']->value;?>
einen <a href="edit_footprints.php"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Footprint<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a> </blockquote>
            <blockquote><?php echo $_smarty_tpl->tpl_vars['missing_supplier']->value;?>
und einen <a href="edit_suppliers.php"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Lieferanten<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a> </blockquote>
            anzugeben.
        </div>
    </div>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['broken_filename_footprints']->value) && $_smarty_tpl->tpl_vars['broken_filename_footprints']->value) {?>
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h2 class="red"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Achtung!<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</h2>
        </div>
        <div class="panel-body">
        <span style="color: red; "><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
In Ihrer Datenbank gibt es Footprints, die einen fehlerhaften Dateinamen hinterlegt haben.
                Dies kann durch ein Datenbankupdate, ein Update von Part-DB, oder durch nicht mehr existierende Dateien ausgelöst worden sein.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

            <br>
            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('escape'=>'none'));
$_block_repeat=true;
echo smarty_block_t(array('escape'=>'none'), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Sie können dies unter <a href="edit_footprints.php">Bearbeiten/Footprints</a> (ganz unten, "Fehlerhafte Dateinamen") korrigieren.<?php $_block_repeat=false;
echo smarty_block_t(array('escape'=>'none'), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

        </span>
        </div>
    </div>
<?php }?>



<div class="panel panel-primary">
    <div class="panel-heading">
        <h3><i class="fa fa-book" aria-hidden="true"></i>&nbsp<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Lizenz<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</h3>
    </div>
    <div class="panel-body">
        <p>Part-DB, Copyright &copy; 2005 of <strong>Christoph Lechner</strong>. <br> Part-DB is published under the <strong>GPL</strong>, so it comes with <strong>ABSOLUTELY NO WARRANTY</strong>,
            click <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
readme/gpl.txt" class="link-external" target="_blank">here</a> for details.
            This is free software, and you are welcome to redistribute it under certain conditions.
            Click <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
readme/gpl.txt" class="link-external" target="_blank">here</a> for details.<br>
        </p>
        <strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Projektseite:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong> Downloads, Bugreports, ToDo-Liste usw. gibts auf der <a class="link-external" target="_blank" href="https://github.com/do9jhb/Part-DB/">GitHub Projektseite</a><br>
        <strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Hilfe<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong> Hilfe und Tipps finden sie im <a class="link-external" href="https://github.com/jbtronics/Part-DB/wiki" target="_blank">Wiki</a> der GitHub Seite. <br>
        <strong>Forum:</strong> Für Fragen rund um die Part-DB gibt es einen Thread auf <a class="link-external" target="_blank" href="https://www.mikrocontroller.net/topic/305023">mikrocontroller.net</a><br>
        <strong>Wiki:</strong> Weitere Informationen gibt es im <a class="link-external" target="_blank" href="http://www.mikrocontroller.net/articles/Part-DB_RW_-_Lagerverwaltung">mikrocontroller.net Artikel</a><br>
        <br>
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Initiator:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 <strong>Christoph Lechner</strong> - <a class="link-external" target="_blank" href="http://www.cl-projects.de/">http://www.cl-projects.de/</a><br>
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Autor seit 2009:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 <strong>K. Jacobs</strong> - <a class="link-external" target="_blank" href="http://www.grautier.com/">http://grautier.com</a><br>
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Neues Design 2016 durch:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
  <strong>Jan Böhmer</strong><br>
        <br>
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Weitere Autoren:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

        <table class="table">
            <tbody>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['authors']->value, 'author');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['author']->value) {
?>
                <tr><td><strong><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['author']->value['name'], ENT_QUOTES, 'UTF-8');?>
</strong></td><td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['author']->value['role'], ENT_QUOTES, 'UTF-8');?>
</td></tr>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($_smarty_tpl->tpl_vars['rss_feed_loop']->value)) {?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4><i class="fa fa-rss" aria-hidden="true"></i>&nbsp<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Updates<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Version<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</th>
                    <th>Veröffentlichungsdatum</th>
                    <th>Link</th>
                </tr>
                </thead>
                <tbody>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['rss_feed_loop']->value, 'rss');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['rss']->value) {
?>
                    <tr>
                        <td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['rss']->value['title'], ENT_QUOTES, 'UTF-8');?>
</td>
                        <td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['rss']->value['datetime'], ENT_QUOTES, 'UTF-8');?>
</td>
                        <td><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['rss']->value['link'], ENT_QUOTES, 'UTF-8');?>
" class="link-external" target="_blank"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['rss']->value['link'], ENT_QUOTES, 'UTF-8');?>
</a></td>
                    </tr>
                <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                </tbody>
            </table>
        </div>
    </div>
<?php }
}
}
