<div class="panel panel-info">
   <div class="panel-heading">
      <h5>Treffer in der Kategorie <b>"{$category_full_path}"</b></h5>
   </div>
        <form method="post" action="">
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

          
           </form>
           
           <!-- For responsive design -->
           <div class="table-responsive">
            <table class="table table-small table-condensed table-hover table-striped">
               {include file='../smarty_table.tpl'}
            </table>
           </div> 
        
</div>

