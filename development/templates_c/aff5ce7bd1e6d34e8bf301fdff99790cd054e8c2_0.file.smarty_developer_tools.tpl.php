<?php
/* Smarty version 3.1.30, created on 2016-11-12 14:38:13
  from "C:\xampp\htdocs\part-db\templates\nextgen\developer_tools.php\smarty_developer_tools.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_58271b455515a7_10240983',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'aff5ce7bd1e6d34e8bf301fdff99790cd054e8c2' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\developer_tools.php\\smarty_developer_tools.tpl',
      1 => 1478957891,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_58271b455515a7_10240983 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_function_locale')) require_once 'C:\\xampp\\htdocs\\part-db\\lib\\smarty\\plugins\\function.locale.php';
if (!is_callable('smarty_block_t')) require_once 'C:\\xampp\\htdocs\\part-db\\lib\\smarty\\plugins\\block.t.php';
echo smarty_function_locale(array('path'=>"nextgen/locale",'domain'=>"partdb"),$_smarty_tpl);?>

  
   <div class="panel panel-default">
    <div class="panel-heading">
        <h4><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Tabs durch Leerzeichen ersetzen / Backup-Dateien löschen<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</h4>
    </div>
    <div class="panel-body">
        <form action="" method="post">
            <div class="checkbox">
                <input type="checkbox" class="styled" name="trim_exec_output" checked>
                <label for="trim_exec_output">Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen)</label> 
            </div>
            <button class="btn btn-default" type="submit" name="tab2spaces">Ausführen</button>
            <label><i><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Der Vorgang kann mehrere Minuten in Anspruch nehmen!<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</i></label>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Doxygen-Dokumentation erstellen bzw. updaten<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</h4>
    </div>
    <div class="panel-body">
        <form action="" method="post">
            <b><span class="text-danger"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Doxygen muss auf dem Server installiert sein!<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span></b>
            <div class="checkbox">
                <input type="checkbox" class="styled" name="trim_exec_output" checked>
                <label for="trim_exec_output"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen)<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
            </div>
            <button type="submit" class="btn btn-default" name="build_doxygen"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Dokumentation erstellen/updaten<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
            <label for="build_doxygen"><i><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Der Vorgang kann mehrere Minuten in Anspruch nehmen!<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</i></label>
        </form>
    </div>
</div>

<div class="panel panel-default">
       <div class="panel-heading">
        <h4><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Release-Paket erstellen<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</h4>
    </div>
    <div class="panel-body">
        <form action="" method="post">
            <b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Version:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 <?php echo $_smarty_tpl->tpl_vars['current_system_version']->value;?>
</b>
            <?php if (isset($_smarty_tpl->tpl_vars['release_archive_link']->value)) {?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="download" href="<?php echo $_smarty_tpl->tpl_vars['release_archive_link']->value;?>
"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Download<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 "<?php echo $_smarty_tpl->tpl_vars['release_archive_basename']->value;?>
"</a>
            <?php }?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="<?php echo $_smarty_tpl->tpl_vars['packing_checklist_link']->value;?>
"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Checkliste<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a>
            <div class="checkbox">
                <input class="styled" type="checkbox" name="trim_exec_output" checked>
                <label for="trim_exec_output"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen)<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
            </div>
            <?php if (isset($_smarty_tpl->tpl_vars['release_archive_link']->value)) {?>
                <button class="btn btn-default" type="submit" name="delete_release_package"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Paket löschen<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
            <?php }?>
            <button class="btn btn-default" type="submit" name="build_release_package"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Paket neu erstellen<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
            <label for="build_release_package"><i><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Der Vorgang kann mehrere Minuten in Anspruch nehmen!<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</i></label>
        </form>
    </div>
</div>

<?php if (isset($_smarty_tpl->tpl_vars['exec_output']->value)) {?>
<div class="panel <?php if (isset($_smarty_tpl->tpl_vars['exec_successful']->value)) {?>panel-successful<?php } else { ?>panel-danger<?php }?>">
    <div class="panel-heading">
        <h4><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Ausgabe<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</h4>
    </div>
    <div class="panel-body">
            <pre>
                <code>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['exec_output']->value, 'line');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['line']->value) {
?>
                    <?php echo $_smarty_tpl->tpl_vars['line']->value['text'];?>

                <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

                </code>
            </pre>
    </div>
</div>
<?php }
}
}
