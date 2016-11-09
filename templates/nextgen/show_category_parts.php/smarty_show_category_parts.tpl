<div class="panel panel-success">
    <div class="panel-heading">
        <h4>Sonstiges</h4>
    </div>
    <div class="panel-body">
        <form action="" method="post">
            Unterkategorien:
            <input type="hidden" name="cid" value="{$cid}">
            <input type="hidden" name="subcat" value="{if $with_subcategories}0{else}1{/if}">
            <button type="submit" class="btn btn-default" name="subcat_button" >{if $with_subcategories}ausblenden{else}einblenden{/if}</button>
        </form>
        <p></p>
        <a class="btn btn-primary" href="edit_part_info.php?category_id={$cid}"
            onclick="openPart('edit_part_info.php?category_id={$cid}';">
            Neues Teil in dieser Kategorie
        </a>
    </div>
</div>

<div class="panel panel-info">
    <div class="panel-heading">
        <h5>Teile in der Kategorie <b>"{$category_name}" </b></h5>
    </div>
    <div class="ipanel-body">
        <form method="post" action="">
            <input type="hidden" name="cid" value="{$cid}">
            <input type="hidden" name="subcat" value="{if $with_subcategories}1{else}0{/if}">
            <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
               {include file='../smarty_table.tpl'}
        </form>
    </div>
</div>
