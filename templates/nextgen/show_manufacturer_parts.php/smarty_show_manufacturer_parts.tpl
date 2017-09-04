{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading">
        {t}Sonstiges{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post" class="form-horizontal no-progbar">
            <input type="hidden" name="mid" value="{$mid}">
            <input type="hidden" name="subman" value="{if $with_submanufacturers}0{else}1{/if}">
            
            <div class="form-group">
                <label class="control-label col-md-2">{t}Unterkategorien:{/t}</label>
                <div class="col-md-10">
                    <button type="submit" class="btn btn-default" name="subloc_button" >{if $with_submanufacturers}{t}ausblenden{/t}{else}{t}einblenden{/t}{/if}</button>
                </div>
            </div>
        </form>
        {*
        <a class="btn btn-primary" href="edit_part_info.php?category_id={$cid}">
            {t}Neues Teil in dieser Kategorie{/t}
        </a> *}
        
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-industry" aria-hidden="true"></i>&nbsp;
        <b>{$table_rowcount}</b> {t}Teile mit Hersteller{/t} "<b>{$manufacturer_name}</b>"
    </div>
    <form method="post" action="" class="no-progbar">
        <input type="hidden" name="lid" value="{$mid}">
        <input type="hidden" name="subloc" value="{if $with_submanufacturers}1{else}0{/if}">
        <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
           {include file='../smarty_table.tpl'}
    </form>
</div>
