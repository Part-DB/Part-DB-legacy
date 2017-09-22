<?php
/* Smarty version 3.1.31, created on 2017-09-22 15:22:49
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\edit_groups.php\smarty_edit_groups.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59c50ea903e243_98957004',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dd4dd42f37dfbe0aace2a7d1806ded4b02520b90' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\edit_groups.php\\smarty_edit_groups.tpl',
      1 => 1506085860,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../smarty_permissions.tpl' => 1,
  ),
),false)) {
function content_59c50ea903e243_98957004 (Smarty_Internal_Template $_smarty_tpl) {
echo smarty_function_locale(array('path'=>"nextgen/locale",'domain'=>"partdb"),$_smarty_tpl);?>

<?php if (isset($_smarty_tpl->tpl_vars['refresh_navigation_frame']->value) && $_smarty_tpl->tpl_vars['refresh_navigation_frame']->value) {?>
    <?php echo '<script'; ?>
 type="text/javascript">
        AjaxUI.getInstance().updateTrees();
    <?php echo '</script'; ?>
>
<?php }?>

<?php if (!isset($_smarty_tpl->tpl_vars['id']->value) || $_smarty_tpl->tpl_vars['id']->value == 0) {?>
    <?php $_smarty_tpl->_assignInScope('can_edit', $_smarty_tpl->tpl_vars['can_create']->value);
?>
    <?php $_smarty_tpl->_assignInScope('can_move', $_smarty_tpl->tpl_vars['can_create']->value);
}?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-users" aria-hidden="true"></i>
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Gruppen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

    </div>
    <div class="panel-body">
        <form action="" method="post" class="row no-progbar">
            <div class="col-md-4">

                <select class="form-control selectpicker"  data-live-search="true" onChange='$("[name=selected_id]").val(this.value); submitForm(this.form);'>
                    <optgroup label="Neu">
                        <option value="0" <?php if (!isset($_smarty_tpl->tpl_vars['id']->value) || $_smarty_tpl->tpl_vars['id']->value == 0) {?>selected<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Neue Gruppe<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</option>
                    </optgroup>
                    <optgroup label="Bearbeiten">
                        <?php echo $_smarty_tpl->tpl_vars['group_list']->value;?>

                    </optgroup>
                </select>

                <hr>

                <select name="selected_id" size="30" class="form-control" onChange="submitForm(this.form);">
                    <optgroup label="Neu">
                        <option value="0" <?php if (!isset($_smarty_tpl->tpl_vars['id']->value) || $_smarty_tpl->tpl_vars['id']->value == 0) {?>selected<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Neue Gruppe<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</option>
                    </optgroup>
                    <optgroup label="Bearbeiten">
                        <?php echo $_smarty_tpl->tpl_vars['group_list']->value;?>

                    </optgroup>
                </select>
            </div>

            <div class="col-md-8 form-horizontal">
                <fieldset>
                    <legend>
                        <?php if (!isset($_smarty_tpl->tpl_vars['id']->value) || $_smarty_tpl->tpl_vars['id']->value == -1) {?>
                            <strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Neue Gruppe hinzufügen:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong>
                        <?php } else { ?>
                            <?php if (isset($_smarty_tpl->tpl_vars['name']->value)) {?>
                                <strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Gruppe bearbeiten:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8');?>

                            <?php } else { ?>
                                <strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Es ist keine Gruppe angewählt!<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong>
                            <?php }?>
                        <?php }?>
                    </legend>

                    <ul class="nav nav-tabs">
                        <li class="active"><a class="link-anchor" data-toggle="tab" href="#home"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Allgemein<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a></li>
                        <li><a data-toggle="tab" class="link-anchor" href="#permissions"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Berechtigungen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a></li>
                    </ul>

                    <div class="tab-content">
                        <br>
                        <div id="home" class="tab-pane fade in active">

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
ID:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <p class="form-control-static"><?php if (isset($_smarty_tpl->tpl_vars['id']->value)) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8');
} else { ?>-<?php }?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Gruppenname*:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8');?>
"
                                           placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
z.B. admins<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
" required <?php if (!$_smarty_tpl->tpl_vars['can_edit']->value) {?>disabled<?php }?>>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
übergeordnete Gruppe*:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <select class="form-control selectpicker" data-live-search="true"
                                            name="parent_id" size="1" <?php if (!$_smarty_tpl->tpl_vars['can_move']->value) {?>disabled<?php }?>>
                                        <?php echo $_smarty_tpl->tpl_vars['parent_group_list']->value;?>

                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Kommentar:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" name="comment" rows="4"
                                              placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
z.B. für Administratoren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
" <?php if (!$_smarty_tpl->tpl_vars['can_edit']->value) {?>disabled<?php }?>
                                    ><?php if (isset($_smarty_tpl->tpl_vars['comment']->value)) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['comment']->value, ENT_QUOTES, 'UTF-8');
}?></textarea>
                                </div>
                            </div>


                        </div>

                        <div id="permissions" class="tab-pane fade">
                            <?php $_smarty_tpl->_subTemplateRender('file:../smarty_permissions.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

                        </div>


                        <div class="form-group">
                            <label class="col-md-9 col-md-offset-3">
                                <i>* = <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Pflichtfelder<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</i>
                            </label>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <?php if (!isset($_smarty_tpl->tpl_vars['id']->value) || $_smarty_tpl->tpl_vars['id']->value == 0) {?>
                                    <button class="btn btn-success" type="submit" name="add" <?php if (!$_smarty_tpl->tpl_vars['can_create']->value) {?>disabled<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Neue Gruppe anlegen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                                    <div class="checkbox">
                                        <input type="checkbox" name="add_more" <?php if ($_smarty_tpl->tpl_vars['add_more']->value) {?>checked<?php }?> <?php if (!$_smarty_tpl->tpl_vars['can_create']->value) {?>disabled<?php }?>>
                                        <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Weitere Gruppe anlegen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                    </div>
                                <?php } else { ?>
                                    <button class="btn btn-success" type="submit" name="apply"
                                            <?php if (!$_smarty_tpl->tpl_vars['can_move']->value && !$_smarty_tpl->tpl_vars['can_edit']->value && !$_smarty_tpl->tpl_vars['can_permission']->value) {?>disabled<?php }?>>
                                        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Änderungen übernehmen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                                    <button class="btn btn-danger" type="submit" name="delete" <?php if (!$_smarty_tpl->tpl_vars['can_delete']->value) {?>disabled<?php }?>>
                                        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Benutzer löschen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                                <?php }?>
                            </div>
                        </div>
                </fieldset>
            </div>
        </form>
    </div>
</div>
<?php }
}
