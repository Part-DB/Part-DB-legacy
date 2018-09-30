{locale path="nextgen/locale" domain="partdb"}
<div class="card border-primary">
    <div class="card-header bg-primary text-white">
        <a data-toggle="collapse" class="link-collapse text-white" href="#panel-header">
           <i class="fas fa-search fa-fw"></i> {t}Suchergebnis{/t}
        </a>
    </div>
    <div class="card-body  card-collapse collapse show" id="panel-header">
        {if $highlighting}<input type="hidden" value="{$keyword}" id="highlight">{/if}

        <div class="row">
            <div class="col-md-6">
                {t 1=$keyword|escape 2=$hits_count|escape escape=false}Die Suche nach "<b>%1</b>" ergab <b>%2 Treffer</b>.{/t}
            </div>

            <div class="col-md-6">
                <div class="float-lg-right float-md-right">
                    <form action="" method="post" class="no-progbar no-ajax form-inline">
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
                            <label class="mr-2">{t}Exportieren:{/t}</label>
                            <select name="export_format" class="form-control mr-2">
                                {foreach $export_formats as $format}
                                    <option value="{$format.value}" {if isset($format.selected)}selected{/if}>{$format.text}</option>
                                {/foreach}
                            </select>
                            <button class="btn btn-primary mt-2 mt-md-0" type="submit" name="export">{t}OK{/t}</button>
                        </div>


                    </form>
                </div>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-12">
                <div class="pull-right-lg pull-right-md pull-right-sm">
                    <form action="" method="post" style="display: inline;" class="no-progbar form-inline">
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
                            <label class="mr-2">{t}Gruppiere nach:{/t}</label>
                            <select name="groupby" class="form-control mr-2">
                                {foreach $group_formats as $format}
                                    <option value="{$format.value}" {if isset($format.selected) && $format.selected === true}selected{/if}>{$format.text}</option>
                                {/foreach}
                            </select>

                            <button class="btn btn-primary mt-0 mt-md-2" type="submit" name="group">{t}OK{/t}</button>
                        </div>


                    </form>
                </div>

            </div>
        </div>


    </div>
</div>