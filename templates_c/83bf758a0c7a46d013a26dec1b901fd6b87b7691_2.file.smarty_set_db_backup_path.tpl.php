<?php
/* Smarty version 3.1.31, created on 2017-09-06 12:37:38
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\install.php\smarty_set_db_backup_path.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59afcff210ff88_14700765',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '83bf758a0c7a46d013a26dec1b901fd6b87b7691' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\install.php\\smarty_set_db_backup_path.tpl',
      1 => 1503859154,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59afcff210ff88_14700765 (Smarty_Internal_Template $_smarty_tpl) {
echo smarty_function_locale(array('path'=>"nextgen/locale",'domain'=>"partdb"),$_smarty_tpl);?>


<div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-life-ring" aria-hidden="true"></i>&nbsp;
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Installation/Update: Datenbank Backupsystem<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</div>
    <div class="panel-body">
        <span style="color: red; "><p><b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Es wird dringend empfohlen, regelmässig Sicherungskopien der Datenbank zu erstellen!<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</b></p>
            <p><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Auch sollte vor jedem Datenbankupdate ein Backup durchgeführt werden. Die Entwickler von Part-DB übernehmen keinerlei Haftung für Schäden jeglicher Art, die durch fehlende Backups oder durch Fehler in Part-DB verursacht werden.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</p></span>
        <p><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('escape'=>'off'));
$_block_repeat=true;
echo smarty_block_t(array('escape'=>'off'), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Sie können dafür ein externes System benutzen, das sich mit einem Link ins Menü von Part-DB integrieren lässt.
            Ein solches Backup-System ist z.B. <a target="_blank" href="http://www.mysqldumper.net/">MySQLDumper</a>.<?php $_block_repeat=false;
echo smarty_block_t(array('escape'=>'off'), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</p>
        <p><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Lassen Sie beide Felder leer, wenn Sie keine Verknüpfung zu einem Backup-System haben möchten.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</p>

        <form action="" method="post" class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Name des Backup-Systems:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="db_backup_name" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['db_backup_name']->value, ENT_QUOTES, 'UTF-8');?>
" placeholder='<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
z.B. "MySQLDumper"<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
'>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Link zum Backup-System:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="db_backup_path" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['db_backup_path']->value, ENT_QUOTES, 'UTF-8');?>
" placeholder='<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
z.B. "../mysqldumper/"<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
'>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                    <button class="btn btn-primary" type="submit" name="save_db_backup_path"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Weiter<?php $_block_repeat=false;
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
