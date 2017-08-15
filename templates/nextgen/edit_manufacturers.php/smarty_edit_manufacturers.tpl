{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-industry" aria-hidden="true"></i>
        {t}Hersteller{/t}
    </div>
    <div class="panel-body">
        <form action="" method="post" class="row no-progbar">
            <div class="col-md-4">

                <select class="form-control selectpicker"  data-live-search="true" onChange='$("[name=selected_id]").val(this.value); submitForm(this.form);'>
                    <optgroup label="Neu">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neuer Hersteller:{/t}</option>
                    </optgroup>
                    <optgroup label="Bearbeiten">
                        {$manufacturer_list nofilter}
                    </optgroup>
                </select>

                <hr>

                <select name="selected_id" size="40" class="form-control" onChange="submitForm(this.form);">
                    <optgroup label="{t}Neu{/t}">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neuer Hersteller{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$manufacturer_list nofilter}
                    </optgroup>
                </select>
            </div>
            
            <div class="col-md-8 form-horizontal">
            <fieldset>
                <legend>
                    {if !isset($id) || $id == 0}
                        <strong>{t}Neuen Hersteller hinzufügen:{/t}</strong>
                    {else}
                        {if isset($name)}
                            <strong>{t}Hersteller bearbeiten:{/t} <a href="show_manufacturer_parts.php?mid={$id}&subman=0">{$name}</a></strong>
                        {else}
                            <strong>{t}Es ist kein Hersteller angewählt!{/t}</strong>
                        {/if}
                    {/if}
                </legend>
            
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}ID:{/t}</label>
                    <div class="col-md-9">
                        <p class="form-control-static">{if isset($id)}{$id}{else}-{/if}</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Name*:{/t}</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="name" value="{if isset($name)}{$name}{/if}" placeholder="{t}z.B. ACME AG{/t}" required>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Übergeordneter Hersteller*:{/t}</label>
                    <div class="col-md-9">
                        <select name="parent_id" data-live-search="true" size="1" class="form-control selectpicker">
                            {$parent_manufacturer_list nofilter}
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Adresse:{/t}</label>
                    <div class="col-md-9">
                        <textarea name="address" class="form-control" rows="5" placeholder="{t}z.B. Musterstraße 1{/t}" >{if isset($address)}{$address|escape}{/if}</textarea>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Telefonnummer:{/t}</label>
                    <div class="col-md-9">
                        <input type="tel" name="phone_number" class="form-control" placeholder="{t}z.B. (030) 12345 67{/t}" value="{if isset($phone_number)}{$phone_number|escape}{/if}">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Faxnummer:{/t}</label>
                    <div class="col-md-9">
                        <input type="tel" class="form-control" name="fax_number" placeholder="{t}z.B. (030) 12345 67{/t}" value="{if isset($fax_number)}{$fax_number}{/if}">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}E-Mail Adresse:{/t}</label>
                    <div class="col-md-9">
                        {if isset($email_address)}
                        <a href="mailto:{$email_address}">{$email_address}</a><br>
                        {/if}
                        <input type="email" name="email_address" class="form-control" placeholder="{t}z.B. contact@foo.bar{/t}" value="{if isset($email_address)}{$email_address}{/if}">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Webseite:{/t}</label>
                    <div class="col-md-9">
                        {if isset($website)}
                        <a href="{$website}" target="_blank">{$website}</a><br>
                        {/if}
                        <input type="url" class="form-control" name="website" placeholder="{t}z.B. www.foo.bar{/t}" value="{if isset($website)}{$website}{/if}">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">{t}Artikel-Direktlink:{/t}</label>
                    <div class="col-md-9">
                        <input type="url" class="form-control" name="auto_product_url" placeholder="{t}z.B. www.foo.bar/%PARTNUMBER%{/t}" value="{if isset($auto_product_url)}{$auto_product_url}{/if}">
                        <p class="help-block">Platzhalter für die Bestellnummer: <i>%PARTNUMBER%</i></p>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-md-9 col-md-offset-3">
                        <i>{t}* = Pflichtfelder{/t}</i>
                    </label>
                </div>
                
                <div class="form-group">
                   <div class="col-md-12 col-md-offset-3">
                        {if !isset($id) || $id == 0}
                            <button class="btn btn-success" type="submit" name="add">{t}Neuen Hersteller anlegen{/t}</button>
                            <div class="checkbox">
                                <input type="checkbox" name="add_more" {if isset($add_more) && $add_more}checked{/if}>
                                <label>{t}Weitere Hersteller anlegen{/t}</label>
                            </div>
                        {else}
                            <button class="btn btn-success" type="submit" name="apply">{t}Änderungen übernehmen{/t}</button>
                            <button class="btn btn-danger" type="submit" name="delete">{t}Hersteller löschen{/t}</button>
                        {/if}
                   </div>
                    
                </div>
            
                </fieldset>
            </div>
        </form>
    </div>
</div>
