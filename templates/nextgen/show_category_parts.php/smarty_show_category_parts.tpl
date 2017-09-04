{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading">
        {t}Sonstiges{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post" class="form-horizontal no-progbar">
            <input type="hidden" name="cid" value="{$cid}">
            <input type="hidden" name="subcat" value="{if $with_subcategories}0{else}1{/if}">
            
            <div class="form-group">
                <div class="col-md-10">
                    <button type="submit" class="btn btn-default {if $with_subcategories}active{/if}" name="subcat_button" >{t}Unterkategorien einblenden{/t}</button>
                </div>
            </div>
        </form>
        <a class="btn btn-primary" href="edit_part_info.php?category_id={$cid}">
            {t}Neues Teil in dieser Kategorie{/t}
        </a>
        
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-tag" aria-hidden="true"></i>&nbsp;
        <b>{$table_rowcount}</b> {t}Teile in der Kategorie{/t} <b>"{$category_name}"</b>
    </div>
    <form method="post" action="" class="no-progbar">
        <input type="hidden" name="cid" value="{$cid}">
        <input type="hidden" name="subcat" value="{if $with_subcategories}1{else}0{/if}">
        <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
           {include file='../smarty_table.tpl'}
    </form>
</div>
