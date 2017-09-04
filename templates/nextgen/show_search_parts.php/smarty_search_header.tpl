{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading">
        {t}Suchergebnis{/t}
    </div>
    <div class="panel-body">
        {t 1=$keyword 2=$hits_count escape=no}Die Suche nach <b>"%1"</b> ergab <b>%2 Treffer</b>.{/t}

        <div style="float: right; display: inline;">
            <form action="" method="post" style="display: inline;" class="no-progbar no-ajax">
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
                    <label>{t}Exportieren:{/t}</label>
                    <select name="export_format" class="form-control">
                        {foreach $export_formats as $format}
                            <option value="{$format.value}" {if isset($format.selected)}selected{/if}>{$format.text}</option>
                        {/foreach}
                    </select>

                    <button class="btn btn-primary" type="submit" name="export">{t}OK{/t}</button>
                </div>
            </form>
        </div>

        <br><br>
        <div style="float: right; display: inline;">
            <form action="" method="post" style="display: inline;" class="no-progbar">
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

                <div class="form-inline">
                    <label class="">{t}Gruppiere nach:{/t}</label>
                    <select name="groupby" class="form-control">
                        {foreach $group_formats as $format}
                            <option value="{$format.value}" {if isset($format.selected) && $format.selected === true}selected{/if}>{$format.text}</option>
                        {/foreach}
                    </select>

                    <button class="btn btn-primary" type="submit" name="group">{t}OK{/t}</button>
                </div>
            </form>
        </div>


    </div>
</div>