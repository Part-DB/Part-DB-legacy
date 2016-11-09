<div class="panel {if $is_new_part}panel-success{else}panel-warning{/if}">
    <div class="panel-heading">
        <h4>
            {if !$is_new_part}
                Ändere Detailinfos von "<b><a href="{$relative_path}show_part_info.php?pid={$pid}">{$name}</b></a>"
                
                <div style="float: right; display: inline;">
                    ID: {$pid}
                </div>
            {else}
                Neues Bauteil erstellen
            {/if}
        </h4>
    
    </div>    
        
    <div class="panel-body">
        <form action="edit_part_info.php" method="post">
            <!--<table class="table">-->
                <div class="row">
                    <div class="col-md-2">
                        <b>Name:</b>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="name" class="form-control" palceholder="Name" size="35" value="{$name}">
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-md-2">
                        <b>Beschreibung:</b>
                    </div>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="description" size="35" value="{$description}">
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-md-2">
                        <b>Vorhanden:</b>
                    </div>
                    <div class="col-md-10">
                        <input type="number" name="instock" class="form-control" min="0" onkeypress="validateNumber(event)" value="{$instock}">
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-md-2">
                        <b>Min. Bestand:</b>
                    </div>
                    <div class="col-md-10">
                        <input type="number" name="mininstock" class="form-control" min="0" onkeypress="validateNumber(event)" value="{$mininstock}">
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-md-2">
                        <b>Kategorie:</b>
                    </div>
                    <div class="col-md-7">
                        <select class="form-control" name="category_id" onChange="document.getElementById('search_category_name').value='__ID__='+this.value; document.getElementById('search_category').click();">
                            {$category_list}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search_category_name" id="search_category_name" placeholder="Suchen / Hinzufügen" class="cleardefault" onkeydown="if (event.keyCode == 13) { document.getElementById('search_category').click();} ">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-default" name="search_category" id="search_category">OK!</button>
                            </span>
                        </div>
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-md-2">
                        <b>Lagerort:</b>
                    </div>
                    <div class="col-md-7">
                        <select class="form-control" name="storelocation_id">
                            <option value="0"></option>
                            {$storelocation_list}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search_storelocation_name" class="form-control" placeholder="Suchen / Hinzufügen" class="cleardefault" onkeydown="if (event.keyCode == 13) { document.getElementById('search_storelocation').click();} ">
                            <span class="input-group-btn">
                                <button type="submit" name="search_storelocation" class="btn btn-default" id="search_storelocation">OK!</button>
                            </span>
                        </div>
                    </div>
                </div>
                <p></p>
                {if !$disable_manufacturers}
                    <div class="row">
                        <div class="col-md-2">
                            <b>Hersteller:</b>
                        </div>
                        <div class="col-md-7">
                            <select class="form-control" name="manufacturer_id">
                                <option value="0"></option>
                                {$manufacturer_list}
                            </select>
                        </div>
                        <div class="col-md-3">
                           <div class="input-group">
                                <input type="text" class="form-control" name="search_manufacturer_name" placeholder="Suchen / Hinzufügen" onkeydown="if (event.keyCode == 13) { document.getElementById('search_manufacturer').click();} ">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default" name="search_manufacturer" id="search_manufacturer">OK!</button>
                                </span>
                            
                            </div>
                        </div>
                    </div>
                <p></p>
                {/if}
                {if !$disable_footprints}
                    <div class="row">
                        <div class="col-md-2">
                            <b>Footprint:</b>
                        </div>
                        <div class="col-md-7">
                            <select class="form-control" name="footprint_id">
                                <option value="0"></option>
                                {$footprint_list}
                            </select>
                        </div>
                        <div class="col-md-3">
                           <div class="input-group">
                                <input type="text" name="search_footprint_name" placeholder="Suchen / Hinzufügen" class="form-control" onkeydown="if (event.keyCode == 13) { document.getElementById('search_footprint').click();} ">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default" name="search_footprint" id="search_footprint">OK!</button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <p></p>
                {/if}
                <div class="row">
                    <div class="col-md-2">
                        <b>Kommentar:</b>
                    </div>
                    <div class="col-md-10">
                        <textarea  class="form-control" name="comment" rows="4" cols="40">{$comment}</textarea>
                    </div>
                </div>
                <p></p>
                
                <div class="row">
                    <div div class="col-md-12">
                        {if $is_new_part}
                            <button type="submit" class="btn btn-success" name="create_new_part">Bauteil erstellen</button>
                        {else}
                            <input type="hidden" name="pid" value="{$pid}">
                            <button type="submit" name="apply_attributes" class="btn btn-success">Änderungen übernehmen</button>
                            <button type="submit" class="btn btn-danger">Änderungen verwerfen</button>
                        {/if}
                    </div>
                </div>
            <!--</table>-->
        </form>
    </div>
</div>
