{locale path="nextgen/locale" domain="partdb"}

<script>
    function checkInstockUnknown() {
        var element = $("#instock_unknown");
        var value = element.prop("checked");
        $("#instock").prop("disabled",value);
        if(value == false) {
            $("#instock").val("0");
        }
    }
</script>


<div class="panel {if $is_new_part}panel-success{else}panel-default{/if}">
    <div class="panel-heading">
            {if !$is_new_part}
                <i class="far fa-edit fa-fw" aria-hidden="true"></i>
                {t}Ändere Detailinfos von{/t} <b><a href="{$relative_path}show_part_info.php?pid={$pid}">{$name}</a></b>
                
                <div class="pull-right-md pull-right-lg pull-right-sm">
                    <span>{t}ID:{/t} {$pid}</span>
                </div>
            {else}
                <i class="fa fa-plus-square" aria-hidden="true"></i>
                {t}Neues Bauteil erstellen{/t}
            {/if}
    </div>

    {if $is_new_part}
        {assign "can_edit" $can_create}
        {assign "can_move" $can_create}
    {/if}

    <div class="panel-body">
        <form action="{$relative_path}edit_part_info.php" class="form-horizontal no-progbar" method="post">
            <!--<table class="table">-->
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        {t}Name:{/t}
                    </label>
                    <div class="col-md-10">
                        <input type="text" name="name" id="name" class="form-control" placeholder="{if empty($format_hint)}{t}z.B. BC547{/t}{else}{$format_hint}{/if}"
                               value="{$name}" onkeydown="if (event.keyCode == 13) { document.getElementById('btn_enter').click();}"
                               required {if !$can_name}disabled{/if}>
                        {if !empty($format_hint)}<p class="help-block">{t}Hinweis zum Format:{/t} {$format_hint}</p>{/if}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        {t}Beschreibung:{/t}
                    </label>
                    {if isset($auto_desc) && $auto_desc}
                    <div class="col-md-8">
                        <input type="text" id="description" class="form-control" name="description"
                               placeholder="{t}z.B. NPN 45V 0,1A 0,5W{/t}" value="{$description nofilter}"
                               {if !$can_description}disabled{/if}>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-default" onClick="octoPart();">Auto</button>
                    </div>
                    {else}
                    <div class="col-md-10">
                        <input type="text" id="description" class="form-control" name="description" placeholder="{t}z.B. NPN 45V 0,1A 0,5W{/t}"
                               value="{$description nofilter}" onkeydown="if (event.keyCode == 13) { document.getElementById('btn_enter').click();}"
                               {if !$can_description}disabled{/if}>
                        <p class="help-block">{t}Hinweis: Hier kann BBCode verwendet werden um den Text besonders auszuzeichnen (z.B. [b]Fett[/b]).{/t}</p>
                    </div>
                    {/if}
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        {t}Vorhanden:{/t}
                    </label>
                    <div class="col-md-8">
                        <input type="number" name="instock" id="instock" class="form-control" min="0"  placeholder="{t}z.B. 100{/t}"
                               value="{if !$instock_unknown}{$instock}{/if}" onkeydown="if (event.keyCode == 13) { document.getElementById('btn_enter').click();}"
                               {if !$can_instock || $instock_unknown}disabled{/if}>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" name="instock_unknown" id="instock_unknown" onchange="checkInstockUnknown();"
                                   {if $instock_unknown}checked{/if} {if !$can_instock}disabled{/if}>
                            <label>{t}Unbekannt{/t}</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        {t}Min. Bestand:{/t}
                    </label>
                    <div class="col-md-10">
                        <input type="number" name="mininstock" class="form-control" min="0" placeholder="{t}z.B. 20{/t}"
                               value="{$mininstock}" onkeydown="if (event.keyCode == 13) { document.getElementById('btn_enter').click();}"
                                {if !$can_mininstock}disabled{/if}>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        {t}Kategorie:{/t}
                    </label>
                    <div class="col-md-7">
                        <select class="form-control selectpicker" data-live-search="true" name="category_id"
                                onChange="document.getElementById('search_category_name').value='__ID__='+this.value; document.getElementById('search_category').click();"
                                {if !$can_move}disabled{/if}>
                             {$category_list nofilter}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search_category_name" id="search_category_name"
                                   placeholder="{t}Suchen / Hinzufügen{/t}" class="cleardefault"
                                   onkeydown="if (event.keyCode == 13) { document.getElementById('search_category').click();} "
                                   {if !$can_move}disabled{/if}>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default submit" name="search_category" id="search_category"
                                        {if !$can_move}disabled{/if}>{t}OK!{/t}</button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        {t}Lagerort:{/t}
                    </label>
                    <div class="col-md-7">
                        <select class="form-control selectpicker" data-live-search="true" name="storelocation_id" {if !$can_storelocation}disabled{/if}>
                            <option value="0">&nbsp;</option>
                            {$storelocation_list nofilter}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search_storelocation_name" class="form-control" placeholder="{t}Suchen / Hinzufügen{/t}"
                                   class="cleardefault" onkeydown="if (event.keyCode == 13) { document.getElementById('search_storelocation').click();} "
                                   {if !$can_storelocation}disabled{/if}>
                            <span class="input-group-btn">
                                <button type="button" name="search_storelocation" class="btn btn-default submit"
                                        id="search_storelocation"{if !$can_storelocation}disabled{/if}>{t}OK!{/t}</button>
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
                            <select class="form-control selectpicker" data-live-search="true" name="manufacturer_id" {if !$can_storelocation}disabled{/if}>
                                <option value="0">&nbsp;</option>
                                {$manufacturer_list nofilter}
                            </select>
                        </div>
                        <div class="col-md-3">
                           <div class="input-group">
                                <input type="text" class="form-control selectpicker" data-live-search="true"
                                       name="search_manufacturer_name" placeholder="{t}Suchen / Hinzufügen{/t}"
                                       onkeydown="if (event.keyCode == 13) { document.getElementById('search_manufacturer').click();} "
                                       {if !$can_storelocation}disabled{/if}>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default submit" name="search_manufacturer"
                                            id="search_manufacturer" {if !$can_storelocation}disabled{/if}>{t}OK!{/t}</button>
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
                            <select class="form-control selectpicker" data-live-search="true" name="footprint_id" {if !$can_footprint}disabled{/if}>
                                <option value="0">&nbsp;</option>
                                {$footprint_list nofilter}
                            </select>
                        </div>
                        <div class="col-md-3">
                           <div class="input-group">
                                <input type="text" name="search_footprint_name" placeholder="{t}Suchen / Hinzufügen{/t}" class="form-control"
                                       onkeydown="if (event.keyCode == 13) { document.getElementById('search_footprint').click();} "
                                       {if !$can_footprint}disabled{/if}>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default submit" name="search_footprint" id="search_footprint" {if !$can_footprint}disabled{/if}>{t}OK!{/t}</button>
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
                        {* Closing bracket has to be directly in front of the $comment, or spaces gets inserted in textarea *}
                        <textarea  class="form-control scedit" name="comment" id="edit_comment" rows="4" cols="40" {if !$can_comment}disabled{/if}
                        >{$comment nofilter}</textarea>
                        <p class="help-block">{t}Hinweis: Hier kann BBCode verwendet werden um den Text besonders auszuzeichnen (z.B. [b]Fett[/b]).{/t}</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-md-10 col-md-offset-2">
                        {if $is_new_part}
                            <button type="button" class="btn btn-success submit rightclick" name="create_new_part" id="btn_enter"
                            {if !$can_create}disabled{/if}>{t}Bauteil erstellen{/t}</button>
                        {else}
                            <input type="hidden" name="pid" value="{$pid}">
                            <button type="button" name="apply_attributes" class="btn btn-success submit rightclick"
                                    id="btn_enter" {if !$can_edit}disabled{/if}>{t}Änderungen übernehmen{/t}</button>
                            <button type="button" class="btn btn-danger submit" {if !$can_edit}disabled{/if}>{t}Änderungen verwerfen{/t}</button>
                        {/if}
                    </div>
                </div>
            <!--</table>-->
        </form>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="description_select" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
