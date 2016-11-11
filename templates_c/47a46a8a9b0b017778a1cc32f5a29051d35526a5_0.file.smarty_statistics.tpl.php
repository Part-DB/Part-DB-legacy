<?php
/* Smarty version 3.1.30, created on 2016-11-10 19:42:21
  from "C:\xampp\htdocs\part-db\templates\nextgen\statistics.php\smarty_statistics.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5824bf8d0c3b60_67367434',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '47a46a8a9b0b017778a1cc32f5a29051d35526a5' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\statistics.php\\smarty_statistics.tpl',
      1 => 1478803336,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5824bf8d0c3b60_67367434 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_function_locale')) require_once 'C:\\xampp\\htdocs\\part-db\\lib\\smarty\\plugins\\function.locale.php';
if (!is_callable('smarty_block_t')) require_once 'C:\\xampp\\htdocs\\part-db\\lib\\smarty\\plugins\\block.t.php';
echo smarty_function_locale(array('path'=>"locale",'domain'=>"template"),$_smarty_tpl);?>


<div class="panel panel-primary">
    <div class="panel-heading">
        <h4><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('domain'=>"template"));
$_block_repeat1=true;
echo smarty_block_t(array('domain'=>"template"), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Statistik<?php $_block_repeat1=false;
echo smarty_block_t(array('domain'=>"template"), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</h4>
    </div>
    <div class="panel-body table-responsive">
        <table>
            <tr>
                <td width="300"><strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Mit Preis erfasste Bauteile:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong></td>
                <td><?php echo $_smarty_tpl->tpl_vars['parts_count_with_prices']->value;?>
</td>
            </tr>
            <tr>
                <td width="300"><strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Wert aller mit Preis erfassten Bauteile:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong></td>
                <td><?php echo $_smarty_tpl->tpl_vars['parts_count_sum_value']->value;?>
</td>
            </tr>
        </table>
        <br>
        <table>
            <tr><td width="300"><strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Anzahl der verschiedenen Bauteile:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong></td><td><?php echo $_smarty_tpl->tpl_vars['parts_count']->value;?>
</td></tr>
            <tr><td width="300"><strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Anzahl der vorhandenen Bauteile:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong></td><td><?php echo $_smarty_tpl->tpl_vars['parts_count_sum_instock']->value;?>
</td></tr>
        </table>
        <br>
        <table>
            <tr><td width="300"><strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Anzahl der Kategorien:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong></td><td><?php echo $_smarty_tpl->tpl_vars['categories_count']->value;?>
</td></tr>
            <tr><td width="300"><strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Anzahl der Footprints:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong></td><td><?php echo $_smarty_tpl->tpl_vars['footprint_count']->value;?>
</td></tr>
            <tr><td width="300"><strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Anzahl der Lagerorte:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong></td><td><?php echo $_smarty_tpl->tpl_vars['location_count']->value;?>
</td></tr>
            <tr><td width="300"><strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Anzahl der Lieferanten:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong></td><td><?php echo $_smarty_tpl->tpl_vars['suppliers_count']->value;?>
</td></tr>
            <tr><td width="300"><strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Anzahl der Hersteller:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong></td><td><?php echo $_smarty_tpl->tpl_vars['manufacturers_count']->value;?>
</td></tr>
            <tr><td width="300"><strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Anzahl der Baugruppen:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong></td><td><?php echo $_smarty_tpl->tpl_vars['devices_count']->value;?>
</td></tr>
            <tr><td width="300"><strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Anzahl der Dateianhänge:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong></td><td><?php echo $_smarty_tpl->tpl_vars['attachements_count']->value;?>
</td></tr>
        </table>
        <br>
        <table>
            <tr><td width="300"><strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Anzahl der Footprint Bilder:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong></td><td><?php echo $_smarty_tpl->tpl_vars['footprint_picture_count']->value;?>
</td></tr>
            <tr><td width="300"><strong><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Anzahl der Hersteller Logos:<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</strong></td><td><?php echo $_smarty_tpl->tpl_vars['iclogos_picture_count']->value;?>
</td></tr>
        </table>
    </div>
</div>
<?php }
}
