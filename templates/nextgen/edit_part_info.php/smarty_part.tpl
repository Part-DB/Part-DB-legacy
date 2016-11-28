{locale path="nextgen/locale" domain="partdb"}
   
<div class="panel {if $is_new_part}panel-success{else}panel-default{/if}">
    <div class="panel-heading">
            <i class="fa fa-info-circle" aria-hidden="true"></i> 
            {if !$is_new_part}
                {t}Ändere Detailinfos von{/t} <b><a href="{$relative_path}show_part_info.php?pid={$pid}">{$name}</b></a>
                
                <div style="float: right; display: inline;">
                    {t}ID:{/t} {$pid}
                </div>
            {else}
                {t}Neues Bauteil erstellen{/t}
            {/if}
    </div>    
        
    <div class="panel-body">
        <form action="{$relative_path}edit_part_info.php" class="form-horizontal" method="post">
            <!--<table class="table">-->
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        {t}Name:{/t}
                    </label>
                    <div class="col-md-10">
                        <input type="text" name="name" class="form-control" palceholder="Name" size="35" value="{$name}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        {t}Beschreibung:{/t}
                    </label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="description" size="35" value="{$description}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        {t}Vorhanden:{/t}
                    </label>
                    <div class="col-md-10">
                        <input type="number" name="instock" class="form-control" min="0" onkeypress="validateNumber(event)" value="{$instock}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        {t}Min. Bestand:{/t}
                    </label>
                    <div class="col-md-10">
                        <input type="number" name="mininstock" class="form-control" min="0" onkeypress="validateNumber(event)" value="{$mininstock}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        {t}Kategorie:{/t}
                    </label>
                    <div class="col-md-7">
                        <select class="form-control" name="category_id" onChange="document.getElementById('search_category_name').value='__ID__='+this.value; document.getElementById('search_category').click();">
                            {$category_list nofilter}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search_category_name" id="search_category_name" placeholder="Suchen / Hinzufügen" class="cleardefault" onkeydown="if (event.keyCode == 13) { document.getElementById('search_category').click();} ">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-default" name="search_category" id="search_category">{t}OK!{/t}</button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        {t}Lagerort:{/t}
                    </label>
                    <div class="col-md-7">
                        <select class="form-control" name="storelocation_id">
                            <option value="0"></option>
                            {$storelocation_list nofilter}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search_storelocation_name" class="form-control" placeholder="Suchen / Hinzufügen" class="cleardefault" onkeydown="if (event.keyCode == 13) { document.getElementById('search_storelocation').click();} ">
                            <span class="input-group-btn">
                                <button type="submit" name="search_storelocation" class="btn btn-default" id="search_storelocation">{t}OK!{/t}</button>
                            </span>
                        </div>
                    </div>
                </div>
                {if !$disable_manufacturers}
                    <div class="form-group">
                        <label class="col-md-2 control-label">
                            {t}Hersteller:{/t}
                        </label>
                        <div class="col-md-7">
                            <select class="form-control" name="manufacturer_id">
                                <option value="0"></option>
                                {$manufacturer_list nofilter}
                            </select>
                        </div>
                        <div class="col-md-3">
                           <div class="input-group">
                                <input type="text" class="form-control" name="search_manufacturer_name" placeholder="Suchen / Hinzufügen" onkeydown="if (event.keyCode == 13) { document.getElementById('search_manufacturer').click();} ">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default" name="search_manufacturer" id="search_manufacturer">{t}OK{/t}</button>
                                </span>
                            
                            </div>
                        </div>
                    </div>
                {/if}
                {if !$disable_footprints}
                    <div class="form-group">
                        <label class="col-md-2 control-label">
                            {t}Footprint:{/t}
                        </label>
                        <div class="col-md-7">
                            <select class="form-control" name="footprint_id">
                                <option value="0"></option>
                                {$footprint_list nofilter}
                            </select>
                        </div>
                        <div class="col-md-3">
                           <div class="input-group">
                                <input type="text" name="search_footprint_name" placeholder="Suchen / Hinzufügen" class="form-control" onkeydown="if (event.keyCode == 13) { document.getElementById('search_footprint').click();} ">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default" name="search_footprint" id="search_footprint">{t}OK!{/t}</button>
                                </span>
                            </div>
                        </div>
                    </div>
                {/if}
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        {t}Kommentar:{/t}
                    </label>
                    <div class="col-md-10">
                        <textarea  class="form-control" name="comment" rows="4" cols="40">{$comment}</textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-md-10 col-md-offset-2">
                        {if $is_new_part}
                            <button type="submit" class="btn btn-success" name="create_new_part">{t}Bauteil erstellen{/t}</button>
                        {else}
                            <input type="hidden" name="pid" value="{$pid}">
                            <button type="submit" name="apply_attributes" class="btn btn-success">{t}Änderungen übernehmen{/t}</button>
                            <button type="submit" class="btn btn-danger">{t}Änderungen verwerfen{/t}</button>
                        {/if}
                    </div>
                </div>
            <!--</table>-->
        </form>
    </div>
</div>
