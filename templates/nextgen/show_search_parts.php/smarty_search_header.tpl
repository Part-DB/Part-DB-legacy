<div class="panel panel-success">
    <div class="panel-heading">
        <h4>Suchergebnis</h4>
    </div>
    <div class="panel-body">
        Die Suche nach <b>"{$keyword}"</b> ergab <b>{$hits_count} Treffer</b>.

        <div style="float: right; display: inline;">
            <form action="" method="post" style="display: inline;">
                <input type='hidden' name='keyword'     value='{$keyword}'>
                {if isset($search_name)}                <input type='hidden' name='search_name'>{/if}
                {if isset($search_category)}            <input type='hidden' name='search_category'>{/if}
                {if isset($search_description)}         <input type='hidden' name='search_description'>{/if}
                {if isset($search_comment)}             <input type='hidden' name='search_comment'>{/if}
                {if isset($search_supplier)}            <input type='hidden' name='search_supplier'>{/if}
                {if isset($search_supplierpartnr)}      <input type='hidden' name='search_supplierpartnr'>{/if}
                {if isset($search_storelocation)}       <input type='hidden' name='search_storelocation'>{/if}
                {if isset($search_footprint)}           <input type='hidden' name='search_footprint'>{/if}
                {if isset($search_manufacturer)}        <input type='hidden' name='search_manufacturer'>{/if}

               <div class="form-inline">
                <select name="export_format" class="form-control">
                    {foreach $export_formats as $format}
                        <option value="{$format.value}" {if isset($format.selected)}selected{/if}>{$format.text}</option>
                    {/foreach}
                </select>

                <input class="btn btn-primary" type="submit" name="export" value="Export">
               </div>
               
            </form>
        </div>
        <div class="clear"></div>
    </div>
</div>