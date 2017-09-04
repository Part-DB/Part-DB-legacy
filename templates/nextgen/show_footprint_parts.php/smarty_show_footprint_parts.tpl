{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading">
        {t}Sonstiges{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post" class="form-horizontal no-progbar">
            <input type="hidden" name="fid" value="{$fid}">
            <input type="hidden" name="subfoot" value="{if $with_subfoot}0{else}1{/if}">
            
            <div class="form-group">
                <label class="control-label col-md-2">{t}Unterkategorien:{/t}</label>
                <div class="col-md-10">
                    <button type="submit" class="btn btn-default" name="subfoot_button" >{if $with_subfoot}{t}ausblenden{/t}{else}{t}einblenden{/t}{/if}</button>
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
        <i class="fa fa-cube" aria-hidden="true"></i>&nbsp;
        <b>{$table_rowcount}</b> {t}Teile mit Footprint{/t} "<b>{$footprint_name}</b>"
    </div>
    <form method="post" action="" class="no-progbar">
        <input type="hidden" name="lid" value="{$fid}">
        <input type="hidden" name="subloc" value="{if $with_subfoot}1{else}0{/if}">
        <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
           {include file='../smarty_table.tpl'}
    </form>
</div>
