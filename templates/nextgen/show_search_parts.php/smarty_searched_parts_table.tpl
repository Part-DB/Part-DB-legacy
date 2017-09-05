{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-default">
   <div class="panel-heading"><i class="fa fa-tag" aria-hidden="true"></i>&nbsp;
       <b>{$table_rowcount - 1}</b> {t}Treffer in der Kategorie{/t} <b>"{$category_full_path}"</b>
   </div>
        <form method="post" action="" class="no-progbar">
            <input type="hidden" name="table_rowcount" value="{$table_rowcount}">

            <input type='hidden' name='keyword' value='{$keyword}'>
           
            {if isset($search_name)}                <input type='hidden' name='search_name'>{/if}
            {if isset($search_category)}            <input type='hidden' name='search_category'>{/if}
            {if isset($search_description)}         <input type='hidden' name='search_description'>{/if}
            {if isset($search_comment)}             <input type='hidden' name='search_comment'>{/if}
            {if isset($search_supplier)}            <input type='hidden' name='search_supplier'>{/if}
            {if isset($search_supplierpartnr)}      <input type='hidden' name='search_supplierpartnr'>{/if}
            {if isset($search_storelocation)}       <input type='hidden' name='search_storelocation'>{/if}
            {if isset($search_footprint)}           <input type='hidden' name='search_footprint'>{/if}
            {if isset($search_manufacturer)}        <input type='hidden' name='search_manufacturer'>{/if}

          

           
           {include file='../smarty_table.tpl'}

        </form>
</div>

