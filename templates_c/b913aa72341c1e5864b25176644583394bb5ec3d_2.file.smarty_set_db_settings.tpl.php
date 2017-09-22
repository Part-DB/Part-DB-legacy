<?php
/* Smarty version 3.1.31, created on 2017-09-22 15:13:07
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\install.php\smarty_set_db_settings.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59c50c6328c8e3_38580126',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b913aa72341c1e5864b25176644583394bb5ec3d' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\install.php\\smarty_set_db_settings.tpl',
      1 => 1504694324,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59c50c6328c8e3_38580126 (Smarty_Internal_Template $_smarty_tpl) {
echo smarty_function_locale(array('path'=>"nextgen/locale",'domain'=>"partdb"),$_smarty_tpl);?>


<!--suppress Annotator -->
<div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-database" aria-hidden="true"></i>&nbsp
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Installation/Update: Datenbank konfigurieren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</div>
    <div class="panel-body">
        <b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Die Datenbank für Part-DB muss bereits existieren, damit Sie Part-DB installieren können.
            Wenn Sie Part-DB bereits benutzt haben, können Sie die vorhandene Datenbank weiter benutzen,
            ansonsten sollte die Datenbank komplett leer sein.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

            <br><br>
            <span style="color:red;">
                <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Achtung:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

                <ul>
                    <li><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Damit Part-DB korrekt funktioniert, müssen Sie dem Benutzer jegliche Rechte an der Datenbank gewähren!<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</li>
                    <li><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Benutzen Sie eine bereits vorhandene Datenbank weiter, sollten Sie jetzt ein Backup davon anlegen!<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</li>
                </ul>
            </span></b>

        <form action="" method="post" class="form-horizontal">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
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
                <div class="col-md-9">
                        <select name="db_type" class="form-control">
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
                    <label class="col-md-3 control-label"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
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
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="db_host" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['db_host']->value, ENT_QUOTES, 'UTF-8');?>
" placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
z.B. localhost<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
" required>
                        <!-- (nicht nötig für SQLite) -->
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Datenbankname:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
<!--/<br>Dateiname--></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="db_name" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['db_name']->value, ENT_QUOTES, 'UTF-8');?>
" placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
z.B. part-db<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
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
                    <div class="col-md-9">
                        <input type="text" name="db_user" class="form-control" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['db_user']->value, ENT_QUOTES, 'UTF-8');?>
" placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
z.B. part-db<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
" required>
                        <!-- (nicht nötig für SQLite) -->
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
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
                    <div class="col-md-9">
                        <input type="password" class="form-control" name="db_password" value="">
                        <!-- (nicht nötig für SQLite) -->
                    </div>
                </div>

                <hr>

                <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Sollte es nicht möglich sein mit der Datenbank zu verbinden, versuchen sie eine der untenstehenden Optionen anzuwählen:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>

                <div class="form-group">
                    
                    <div class="col-md-9 col-md-offset-3">
                        <div class="checkbox">
                            <input type="checkbox" class="form-control" name="space_fix" value="" <?php if ($_smarty_tpl->tpl_vars['space_fix']->value) {?>checked<?php }?>>
                            <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Leerzeichen in PDO-String einfügen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                        <button class="btn btn-primary" type="submit" name="save_db_settings"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
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

</div> <!-- for header-->
</body>
</html><?php }
}
