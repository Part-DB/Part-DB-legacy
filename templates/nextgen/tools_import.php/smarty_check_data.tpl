<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-magic" aria-hidden="true"></i>&nbsp;
        {t}Daten prüfen{/t}</div>
    <form action="" method="post" class="no-progbar">
        <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
        <input type="hidden" name="file_content" value="{$file_content}">
            {include "../smarty_table.tpl"}
        <div class="panel-body">
            <button class="btn btn-primary" type="submit" name="check_data">{t}Daten übernehmen und prüfen{/t}</button>
            {if isset($data_is_valid) && $data_is_valid}
                <button type="submit" class="btn btn-success" name="import_data">{t}Daten importieren!{/t}</button>
            {/if}
        </div>
    </form>
</div>
