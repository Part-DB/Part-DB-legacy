{if isset($messages)}
    <div class="panel panel-default">
        <form action="" method="post" class="panel-body">
            {foreach $messages as $msg}
                {if isset($msg.text)}
                    {if isset($msg.strong)}<strong>{/if}
                {if isset($msg.color)}<span style="color: {$msg.color}; ">{/if}
                    {$msg.text nofilter}
                {if isset($msg.color)}</span>{/if}
                    {if isset($msg.strong)}</strong>{/if}
                {/if}

                {if isset($msg.html)}
                    {$msg.html nofilter}
                {/if}

                {if !isset($msg.no_linebreak)}<br>{/if}
            {/foreach}
        </form>
    </div>
{/if}

</div> <!-- content-data -->
</div> <!-- .container-float -->
</div> <!-- page-content-wrapper -->

</main>

</div>   <!-- Wrapper -->

{if !isset($ajax_request) || !ajax_request}

    <!-- Back to top button -->
    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button"
       title="Zum Seitenbeginn" data-toggle="tooltip" data-placement="left">
        <span class="glyphicon glyphicon-chevron-up"></span>
    </a>


    <link rel="stylesheet" type="text/css" href="{$relative_path}datatables/datatables.min.css"/>
    <script type="text/javascript" src="{$relative_path}datatables/datatables.min.js" async></script>

    <!-- Treeview -->
    <script src="{$relative_path}js/bootstrap-treeview.js" async></script>

    <!-- FileInput -->
    <script src="{$relative_path}js/fileinput.min.js" async></script>

    <!-- Functions -->
    <script src="{$relative_path}templates/nextgen/js/functions.js" async></script>
    <script src="{$relative_path}templates/nextgen/js/ajax_ui.js" async></script>

    <!-- Calculator scripts -->
    <script type="text/javascript" src="{$relative_path}javascript/calculator.js"></script>

    <!-- jQuery Form lib -->
    <script src="{$relative_path}js/jquery.form.min.js"></script>

    <!-- Bootstrap-select -->
    <script src="{$relative_path}js/bootstrap-select.min.js"></script>
    <script src="{$relative_path}js/i18n/defaults-de_DE.js"></script>

    {if !empty($tracking_code)}{$tracking_code nofilter}{/if}

    </body>

    </html>
{/if}