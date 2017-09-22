<?php
/* Smarty version 3.1.31, created on 2017-09-06 12:40:02
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\edit_categories.php\smarty_edit_categories.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59afd0820971b7_17921257',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a9dbeeee7e6ca241e63abf2082c0f04c966711e7' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\edit_categories.php\\smarty_edit_categories.tpl',
      1 => 1504694324,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59afd0820971b7_17921257 (Smarty_Internal_Template $_smarty_tpl) {
echo smarty_function_locale(array('path'=>"nextgen/locale",'domain'=>"partdb"),$_smarty_tpl);?>


<?php if (isset($_smarty_tpl->tpl_vars['refresh_navigation_frame']->value) && $_smarty_tpl->tpl_vars['refresh_navigation_frame']->value) {?>
    <?php echo '<script'; ?>
 type="text/javascript">
        AjaxUI.getInstance().updateTrees();
    <?php echo '</script'; ?>
>
<?php }?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-tags" aria-hidden="true"></i>
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Kategorien<?php $_block_repeat=false;
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
Neue Kategorie<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</option>
                    </optgroup>
                    <optgroup label="Bearbeiten">
                        <?php echo $_smarty_tpl->tpl_vars['category_list']->value;?>

                    </optgroup>
                </select>

                <hr>

                <select class="form-control"  size="30" name="selected_id" onChange='submitForm(this.form)'>
                    <optgroup label="Neu">
                        <option value="0" <?php if (!isset($_smarty_tpl->tpl_vars['id']->value) || $_smarty_tpl->tpl_vars['id']->value == 0) {?>selected<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Neue Kategorie<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</option>
                    </optgroup>
                    <optgroup label="Bearbeiten">
                        <?php echo $_smarty_tpl->tpl_vars['category_list']->value;?>

                    </optgroup>
                </select>
            </div>

            <div class="col-md-8 form-horizontal">
                <fieldset>
                    <legend>
                        <?php if (!isset($_smarty_tpl->tpl_vars['id']->value) || $_smarty_tpl->tpl_vars['id']->value == 0) {?>
                            <strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Neue Kategorie hinzufügen:<?php $_block_repeat=false;
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
Kategorie bearbeiten:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
 <a href="show_category_parts.php?cid=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8');?>
&subcat=0"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8');?>
</a></strong>
                            <?php } else { ?>
                                <strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Es ist keine Kategorie angewählt!<?php $_block_repeat=false;
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
Standard<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a></li>
                        <li><a data-toggle="tab" class="link-anchor" href="#menu1"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Optionen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a></li>
                        <li><a data-toggle="tab" class="link-anchor" href="#menu2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Erweitert<?php $_block_repeat=false;
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
Name*:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8');?>
" placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
z.B. Kondensatoren<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Übergeordnete Kategorie*:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <select class="form-control selectpicker" data-live-search="true" name="parent_id" size="1">
                                        <?php echo $_smarty_tpl->tpl_vars['parent_category_list']->value;?>

                                    </select>
                                </div>
                            </div>

                        </div>

                        <div id="menu2" class="tab-pane fade">

                            <br>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Filter für Bauteilenamen (RegEx):<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="partname_regex" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['partname_regex']->value, ENT_QUOTES, 'UTF-8');?>
"
                                           placeholder="<?php if (!empty($_smarty_tpl->tpl_vars['partname_regex_parent']->value)) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['partname_regex_parent']->value, ENT_QUOTES, 'UTF-8');
} else {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
z.B. /([^\/]+)/(^\/]+)/@f$Kapazität$Spannung<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}?>"
                                            pattern="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['partname_input_pattern']->value, ENT_QUOTES, 'UTF-8');?>
">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Hinweis für Bauteilenamen:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="partname_hint" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['partname_hint']->value, ENT_QUOTES, 'UTF-8');?>
"
                                           placeholder="<?php if (!empty($_smarty_tpl->tpl_vars['partname_hint_parent']->value)) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['partname_hint_parent']->value, ENT_QUOTES, 'UTF-8');
} else {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
z.B. Kapazität/Spannung<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}?>">
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Standard Beschreibung:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="default_description" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['default_description']->value, ENT_QUOTES, 'UTF-8');?>
"
                                           placeholder="<?php if (!empty($_smarty_tpl->tpl_vars['default_description_parent']->value)) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['default_description_parent']->value, ENT_QUOTES, 'UTF-8');
} else {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
z.B. Durchmesser: ,Höhe:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Standard Kommentar:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="default_comment" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['default_comment']->value, ENT_QUOTES, 'UTF-8');?>
"
                                           placeholder="<?php if (!empty($_smarty_tpl->tpl_vars['default_comment_parent']->value)) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['default_comment_parent']->value, ENT_QUOTES, 'UTF-8');
} else {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
z.B. RM:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}?>">
                                </div>
                            </div>

                        </div>


                        <div id="menu1" class="tab-pane fade">

                            <br>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Footprints deaktivieren:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <div class="checkbox">
                                        <input type="checkbox" name="disable_footprints" <?php if ($_smarty_tpl->tpl_vars['disable_footprints']->value) {?>checked<?php }?> <?php if (isset($_smarty_tpl->tpl_vars['parent_disable_footprints']->value) && $_smarty_tpl->tpl_vars['parent_disable_footprints']->value) {?>disabled<?php }?>>
                                        <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Teile in dieser Kategorie (inkl. allen Unterkategorien) können keine Footprints haben<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Hersteller deaktivieren:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <div class="checkbox">
                                        <input type="checkbox" name="disable_manufacturers" <?php if ($_smarty_tpl->tpl_vars['disable_manufacturers']->value) {?>checked<?php }?> <?php if (isset($_smarty_tpl->tpl_vars['parent_disable_manufacturers']->value) && $_smarty_tpl->tpl_vars['parent_disable_manufacturers']->value) {?>disabled<?php }?>>
                                        <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Teile in dieser Kategorie (inkl. allen Unterkategorien) können keine Hersteller haben<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                    </div>
                                </div>
                            </div>



                            <div class="form-group">
                                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Automatische Links zu Datenblättern deaktivieren:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <div class="checkbox">
                                        <input type="checkbox" name="disable_autodatasheets" <?php if ($_smarty_tpl->tpl_vars['disable_autodatasheets']->value) {?>checked<?php }?> <?php if (isset($_smarty_tpl->tpl_vars['parent_disable_autodatasheets']->value) && $_smarty_tpl->tpl_vars['parent_disable_autodatasheets']->value) {?>disabled<?php }?>>
                                        <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Teile in dieser Kategorie (inkl. allen Unterkategorien) haben keine automatisch erzeugten Links zu Datenblättern<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Automatische erzeugte Bauteileeigenschaften deaktivieren:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                <div class="col-md-9">
                                    <div class="checkbox">
                                        <input type="checkbox" name="disable_properties" <?php if ($_smarty_tpl->tpl_vars['disable_properties']->value) {?>checked<?php }?> <?php if (isset($_smarty_tpl->tpl_vars['parent_disable_properties']->value) && $_smarty_tpl->tpl_vars['parent_disable_properties']->value) {?>disabled<?php }?>>
                                        <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Teile in dieser Kategorie (inkl. allen Unterkategorien) haben keine automatisch erzeugten Bauteileigenschaften<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-md-9 col-md-offset-3">
                            <i><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
* = Pflichtfelder<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</i>
                        </label>
                    </div>

                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <?php if (!isset($_smarty_tpl->tpl_vars['id']->value) || $_smarty_tpl->tpl_vars['id']->value == 0) {?>
                                <button class="btn btn-success" type="submit" name="add"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Neue Kategorie anlegen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</button>
                                <div class="checkbox">
                                    <input type="checkbox" name="add_more" <?php if ($_smarty_tpl->tpl_vars['add_more']->value) {?>checked<?php }?>>
                                    <label><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Weitere Kategorien anlegen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</label>
                                </div>
                            <?php } else { ?>
                                <button class="btn btn-success" type="submit" name="apply"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
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
                                <button class="btn btn-danger" type="submit" name="delete"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
?>
Kategorie löschen<?php $_block_repeat=false;
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
