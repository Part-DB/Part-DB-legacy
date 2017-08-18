{locale path="nextgen/locale" domain="partdb"}

<script type="text/javascript">
    function switch_series()
    {
        if(document.edit.series.checked)
        {
            document.edit.series_from.disabled=false;
            document.edit.series_to.disabled=false;
        }
        else
        {
            document.edit.series_from.disabled=true;
            document.edit.series_to.disabled=true;
        }
    }
</script>

<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-cube" aria-hidden="true"></i>&nbsp;
        {t}Lagerorte{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post" name="edit" class="row no-progbar">
            <div class="col-md-4">

                <select class="form-control selectpicker"  data-live-search="true" onChange='$("[name=selected_id]").val(this.value); submitForm(this.form);'>
                    <optgroup label="Neu">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neuer Lagerort{/t}</option>
                    </optgroup>
                    <optgroup label="Bearbeiten">
                        {$storelocation_list nofilter}
                    </optgroup>
                </select>

                <hr>

                <select name="selected_id" size="30" class="form-control" onChange="submitForm(this.form);">
                    <optgroup label="{t}Neu{/t}">
                        <option value="0" {if isset($id) && $id == 0}selected{/if}>{t}Neuer Lagerort{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$storelocation_list nofilter}
                    </optgroup>
                </select>
            </div>
         
            <div class="col-md-8 form-horizontal">   
            <fieldset>
                <legend>
                    {if !isset($id) || $id == 0}
                        <strong>{t}Neuen Lagerort hinzufügen:{/t}</strong>
                    {else}
                        {if isset($name)}
                            <strong>{t}Lagerort bearbeiten:{/t} <a href="show_location_parts.php?lid={$id}&subloc=0">{$name}</a></strong>
                        {else}
                            <strong>{t}Es ist kein Lagerort angewählt!{/t}</strong>
                        {/if}
                    {/if}
                </legend>

                <div class="form-group">
                    <label class="control-label col-md-3">{t}ID:{/t}</label>
                    <div class="col-md-9">
                        <p class="form-control-static">{if isset($id)}{$id}{else}-{/if}</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-md-3">{t}Name*:{/t}</label>
                    <div class="col-md-9">
                        <input class="form-control" placeholder="{t}z.B. Aktive Bauteile I{/t}" type="text" name="name" value="{$name}" required>
                    </div>
                </div>
                
                {if !isset($id) || $id == 0}
                <div class="form-group">
                    <label class="control-label col-md-3">{t}Serie:{/t}</label>
                    <div class="col-md-9">
                       <div class="checkbox">
                            <input type="checkbox" name="series" {if isset($series)}checked{/if} onclick="switch_series()">
                            <label>{t}Serie erzeugen{/t}</label>
                        </div>
                    </div>
                    
                </div>
                
                <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                        <label class="col-md-1 control-label">{t}von{/t}</label>
                        <div class="col-md-5">
                            <input type="number" class="form-control" min="0"  name="series_from" value="{if isset($series_from)}{$series_from}{else}1{/if}" disabled>
                        </div>
                        <label class="col-md-1 control-label">{t}bis{/t}</label> 
                        <div class="col-md-5">
                            <input type="number" class="form-control" min="1" name="series_to" value="{if isset($series_to)}{$series_to}{else}3{/if}" disabled>
                        </div>
                    </div>
                    
                </div>
                
                {/if}
            
                <div class="form-group">
                    <label class="control-label col-md-3">{t}Übergeordneter Lagerort*:{/t}</label>
                    <div class="col-md-9">
                        <select name="parent_id" class="form-control selectpicker" data-live-search="true" size="1">
                            {$parent_storelocation_list nofilter}
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-md-3">{t}Voll:{/t}</label>
                    <div class="col-md-9">
                        <div class="checkbox"> 
                            <input type="checkbox" name="is_full" {if $is_full}checked{/if}>
                            <label>{t}Diesen Lagerort als "voll" markieren{/t}</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-9 col-md-offset-3">
                        <i>{t}* = Pflichtfelder{/t}</i>
                    </label>
                </div>
                
                <div class="form-group"> 
                    <div class="col-md-9 col-md-offset-3">
                        {if !isset($id) || $id == 0}
                            <button type="submit" class="btn btn-success" name="add">{t}Neuen Lagerort anlegen{/t}</button>
                            <div class="checkbox">
                                <input type="checkbox" name="add_more" {if $add_more}checked{/if}>
                                <label>{t}Weitere Lagerorte anlegen{/t}</label>
                            </div>
                        {else}
                            <button type="submit" class="btn btn-success" name="apply">{t}Änderungen übernehmen{/t}</button>
                            <button type="submit" class="btn btn-danger" name="delete">{t}Lagerort löschen{/t}</button>
                        {/if}
                    </div>
                </div>
            </fieldset>
            </div>
        </form>
    </div>
</div>
