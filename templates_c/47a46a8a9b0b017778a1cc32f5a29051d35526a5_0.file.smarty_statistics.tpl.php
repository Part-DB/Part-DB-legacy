<?php
/* Smarty version 3.1.30, created on 2016-11-07 23:08:22
  from "C:\xampp\htdocs\part-db\templates\nextgen\statistics.php\smarty_statistics.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5820fb56b76d28_71255723',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '47a46a8a9b0b017778a1cc32f5a29051d35526a5' => 
    array (
      0 => 'C:\\xampp\\htdocs\\part-db\\templates\\nextgen\\statistics.php\\smarty_statistics.tpl',
      1 => 1478556499,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5820fb56b76d28_71255723 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4>Statistik</h4>
    </div>
    <div class="panel-body table-responsive">
        <table>
            <tr>
                <td width="300"><strong>Mit Preis erfasste Bauteile:</strong></td>
                <td><?php echo $_smarty_tpl->tpl_vars['parts_count_with_prices']->value;?>
</td>
            </tr>
            <tr>
                <td width="300"><strong>Wert aller mit Preis erfassten Bauteile:</strong></td>
                <td><?php echo $_smarty_tpl->tpl_vars['parts_count_sum_value']->value;?>
</td>
            </tr>
        </table>
        <br>
        <table>
            <tr><td width="300"><strong>Anzahl der verschiedenen Bauteile:</strong></td><td><?php echo $_smarty_tpl->tpl_vars['parts_count']->value;?>
</td></tr>
            <tr><td width="300"><strong>Anzahl der vorhandenen Bauteile:</strong></td><td><?php echo $_smarty_tpl->tpl_vars['parts_count_sum_instock']->value;?>
</td></tr>
        </table>
        <br>
        <table>
            <tr><td width="300"><strong>Anzahl der Kategorien:</strong></td><td><?php echo $_smarty_tpl->tpl_vars['categories_count']->value;?>
</td></tr>
            <tr><td width="300"><strong>Anzahl der Footprints:</strong></td><td><?php echo $_smarty_tpl->tpl_vars['footprint_count']->value;?>
</td></tr>
            <tr><td width="300"><strong>Anzahl der Lagerorte:</strong></td><td><?php echo $_smarty_tpl->tpl_vars['location_count']->value;?>
</td></tr>
            <tr><td width="300"><strong>Anzahl der Lieferanten:</strong></td><td><?php echo $_smarty_tpl->tpl_vars['suppliers_count']->value;?>
</td></tr>
            <tr><td width="300"><strong>Anzahl der Hersteller:</strong></td><td><?php echo $_smarty_tpl->tpl_vars['manufacturers_count']->value;?>
</td></tr>
            <tr><td width="300"><strong>Anzahl der Baugruppen:</strong></td><td><?php echo $_smarty_tpl->tpl_vars['devices_count']->value;?>
</td></tr>
            <tr><td width="300"><strong>Anzahl der Dateianhänge:</strong></td><td><?php echo $_smarty_tpl->tpl_vars['attachements_count']->value;?>
</td></tr>
        </table>
        <br>
        <table>
            <tr><td width="300"><strong>Anzahl der Footprint Bilder:</strong></td><td><?php echo $_smarty_tpl->tpl_vars['footprint_picture_count']->value;?>
</td></tr>
            <tr><td width="300"><strong>Anzahl der Hersteller Logos:</strong></td><td><?php echo $_smarty_tpl->tpl_vars['iclogos_picture_count']->value;?>
</td></tr>
        </table>
    </div>
</div>
<?php }
}
