<?php
/* Smarty version 3.1.30, created on 2016-11-09 15:26:01
  from "C:\xampp\htdocs\part-db\templates\nextgen\developer_tools.php\smarty_developer_tools.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_582331f9d72ea4_71134021',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'aff5ce7bd1e6d34e8bf301fdff99790cd054e8c2' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\developer_tools.php\\smarty_developer_tools.tpl',
      1 => 1478701559,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_582331f9d72ea4_71134021 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Tabs durch Leerzeichen ersetzen / Backup-Dateien löschen</h4>
    </div>
    <div class="panel-body">
        <form action="" method="post">
            <div class="checkbox">
                <input type="checkbox" class="styled" name="trim_exec_output" checked>
                <label for="trim_exec_output">Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen)</label> 
            </div>
            <button class="btn btn-default" type="submit" name="tab2spaces">Ausführen</button>
            <label><i>Der Vorgang kann mehrere Minuten in Anspruch nehmen!</i></label>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Doxygen-Dokumentation erstellen bzw. updaten</h4>
    </div>
    <div class="panel-body">
        <form action="" method="post">
            <b><span class="text-danger">Doxygen muss auf dem Server installiert sein!</span></b>
            <div class="checkbox">
                <input type="checkbox" class="styled" name="trim_exec_output" checked>
                <label for="trim_exec_output">Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen)</label>
            </div>
            <button type="submit" class="btn btn-default" name="build_doxygen">Dokumentation erstellen/updaten</button>
            <label for="build_doxygen"><i>Der Vorgang kann mehrere Minuten in Anspruch nehmen!</i></label>
        </form>
    </div>
</div>

<div class="panel panel-default">
       <div class="panel-heading">
        <h4>Release-Paket erstellen</h4>
    </div>
    <div class="panel-body">
        <form action="" method="post">
            <b>Version: <?php echo $_smarty_tpl->tpl_vars['current_system_version']->value;?>
</b>
            <?php if (isset($_smarty_tpl->tpl_vars['release_archive_link']->value)) {?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="download" href="<?php echo $_smarty_tpl->tpl_vars['release_archive_link']->value;?>
">Download "<?php echo $_smarty_tpl->tpl_vars['release_archive_basename']->value;?>
"</a>
            <?php }?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="<?php echo $_smarty_tpl->tpl_vars['packing_checklist_link']->value;?>
">Checkliste</a>
            <div class="checkbox">
                <input class="styled" type="checkbox" name="trim_exec_output" checked>
                <label for="trim_exec_output">Ausgabe stutzen (nur die ersten und letzten 20 Einträge anzeigen)</label>
            </div>
            <?php if (isset($_smarty_tpl->tpl_vars['release_archive_link']->value)) {?>
                <button class="btn btn-default" type="submit" name="delete_release_package">Paket löschen</button>
            <?php }?>
            <button class="btn btn-default" type="submit" name="build_release_package">Paket neu erstellen</button>
            <label for="build_release_package"><i>Der Vorgang kann mehrere Minuten in Anspruch nehmen!</i></label>
        </form>
    </div>
</div>

<?php if (isset($_smarty_tpl->tpl_vars['exec_output']->value)) {?>
<div class="panel <?php if (isset($_smarty_tpl->tpl_vars['exec_successful']->value)) {?>panel-successful<?php } else { ?>panel-danger<?php }?>">
    <div class="panel-heading">
        <h4>Ausgabe</h4>
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
