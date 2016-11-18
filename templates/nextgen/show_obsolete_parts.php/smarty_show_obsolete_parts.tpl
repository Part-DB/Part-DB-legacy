<div class="panel panel-primary">
    <div class="panel-heading"><h4>{t}Sonstiges{/t}</h4></div>
    <div class="panel-body">
        <form action="" method="post">
            {t}Teile ohne Einkaufsinformationen:{/t}
            <input type="hidden" name="show_no_orderdetails_parts" value="{if $show_no_orderdetails_parts}0{else}1{/if}">
            <button class="btn btn-default" type="submit" name="change_show_no_orderdetails">{if $show_no_orderdetails_parts}ausblenden{else}einblenden{/if}</button>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><h4>Nicht mehr erh&auml;ltliche Teile</h4></div>
        {include file="../smarty_table.tpl"}
</div>