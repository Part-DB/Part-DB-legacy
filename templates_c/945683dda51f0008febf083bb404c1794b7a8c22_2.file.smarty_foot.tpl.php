<?php
/* Smarty version 3.1.31, created on 2017-09-22 15:13:18
  from "E:\xampp\htdocs\Part-DB2\templates\nextgen\smarty_foot.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59c50c6e1bf475_41722109',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '945683dda51f0008febf083bb404c1794b7a8c22' => 
    array (
      0 => 'E:\\xampp\\htdocs\\Part-DB2\\templates\\nextgen\\smarty_foot.tpl',
      1 => 1506085860,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59c50c6e1bf475_41722109 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['messages']->value)) {?>
    <!--suppress ALL -->
    <div class="panel panel-default">
        <form action="" method="post" class="panel-body">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['messages']->value, 'msg');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['msg']->value) {
?>
                <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['text'])) {?>
                    <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['strong'])) {?><strong><?php }?>
                <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['color'])) {?><span style="color: <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['msg']->value['color'], ENT_QUOTES, 'UTF-8');?>
; "><?php }?>
                    <?php echo $_smarty_tpl->tpl_vars['msg']->value['text'];?>

                <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['color'])) {?></span><?php }?>
                    <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['strong'])) {?></strong><?php }?>
                <?php }?>

                <?php if (isset($_smarty_tpl->tpl_vars['msg']->value['html'])) {?>
                    <?php echo $_smarty_tpl->tpl_vars['msg']->value['html'];?>

                <?php }?>

                <?php if (!isset($_smarty_tpl->tpl_vars['msg']->value['no_linebreak'])) {?><br><?php }?>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

        </form>
    </div>
<?php }?>

<input type="hidden" id="basepath" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
">
<input type="hidden" id="autorefresh" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['autorefresh']->value, ENT_QUOTES, 'UTF-8');?>
">
<input type="hidden" id="redirect_url" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['redirect_url']->value, ENT_QUOTES, 'UTF-8');?>
">
<input type="hidden" id="auto_sort" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['auto_sort']->value, ENT_QUOTES, 'UTF-8');?>
">


</div> <!-- content-data -->
</div> <!-- .container-float -->
</div> <!-- page-content-wrapper -->

</main>

</div>   <!-- Wrapper -->

<!-- PHP Debugbar -->
<?php if (isset($_smarty_tpl->tpl_vars['debugbar_body']->value)) {
echo $_smarty_tpl->tpl_vars['debugbar_body']->value;
}?>


<?php if (!isset($_smarty_tpl->tpl_vars['ajax_request']->value) || !'ajax_request') {?>
    <!-- Back to top button -->
    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button"
       title="Zum Seitenbeginn" data-toggle="tooltip" data-placement="left">
        <span class="glyphicon glyphicon-chevron-up"></span>
    </a>

    <!-- Datatables -->
    <link rel="stylesheet" type="text/css" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
datatables/datatables.min.css"/>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
datatables/datatables.min.js"><?php echo '</script'; ?>
>
    <!-- Datatables plugin for natural sorting -->
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
datatables/natural.js"><?php echo '</script'; ?>
>



    <!-- Treeview -->
    <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
js/bootstrap-treeview.js" async><?php echo '</script'; ?>
>

    <!-- FileInput -->
    <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
js/fileinput.min.js" async><?php echo '</script'; ?>
>

    <!-- Functions -->
    <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
templates/nextgen/js/functions.js" async><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
templates/nextgen/js/ajax_ui.js" async><?php echo '</script'; ?>
>

    <!-- Calculator scripts -->
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
javascript/calculator.js"><?php echo '</script'; ?>
>

    <!-- jQuery Form lib -->
    <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
js/jquery.form.min.js"><?php echo '</script'; ?>
>

    <!-- Bootstrap-select -->
    <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
js/bootstrap-select.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['relative_path']->value, ENT_QUOTES, 'UTF-8');?>
js/i18n/defaults-de_DE.js"><?php echo '</script'; ?>
>

    <?php if (!empty($_smarty_tpl->tpl_vars['tracking_code']->value)) {
echo $_smarty_tpl->tpl_vars['tracking_code']->value;
}?>

    </body>

    </html>
<?php }
}
}
