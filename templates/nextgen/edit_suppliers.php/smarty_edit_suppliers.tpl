<div class="panel panel-primary">
    <div class="panel-heading"><h4>{t}Lieferanten{/t}</h4></div>
    <div class="panel-body">
        <form action="" method="post" class="row">
            <div class="col-md-4">
                <select name="selected_id" size="40" class="form-control" onChange="this.form.submit()">
                    <optgroup label="{t}Neu{/t}">
                        <option value="0" {if !isset($id) || $id == 0}selected{/if}>{t}Neuer Lieferant{/t}</option>
                    </optgroup>
                    <optgroup label="{t}Bearbeiten{/t}">
                        {$supplier_list}
                    </optgroup>
                </select> 
            </div>
            
            <div class="col-md-8 form-horizontal">
                <h4>
                    {if !isset($id) || $id == 0}
                        <strong>{t}Neuen Lieferanten hinzufügen:{/t}</strong>
                    {else}
                        {if !empty($name)}
                            <strong>{t}Lieferant bearbeiten:{/t}</strong>
                        {else}
                            <strong>{t}Es ist kein Lieferant angewählt!{/t}</strong>
                        {/if}
                    {/if}
                </h4>
               
               
                <div class="form-group">
                    <label class="col-md-3 control-label">ID:</label>
                    <div class="col-md-9">
                        <p class="form-control-static">{if isset($id)}{$id}{else}-{/if}</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">Name*:</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="name" value="{if isset($name)}{$name}{/if}" placeholder="Name" required>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-md-3 control-label">Übergeordneter Lieferant*:</label>
                    <div class="col-md-9">
                        <select name="parent_id" size="1" class="form-control">
                            {$parent_supplier_list}
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">Adresse:</label>
                    <div class="col-md-9">
                        <textarea name="address" class="form-control" rows="5" >{if isset($address)}{$address|escape}{/if}</textarea>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-md-3 control-label">Telefonnummer:</label>
                    <div class="col-md-9">
                        <input type="tel" name="phone_number" class="form-control" value="{if isset($phone_number)}{$phone_number|escape}{/if}">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">Faxnummer:</label>
                    <div class="col-md-9">
                        <input type="tel" class="form-control" name="fax_number" value="{if isset($fax_number)}{$fax_number}{/if}">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">E-Mail Adresse:</label>
                    <div class="col-md-9">
                        {if isset($email_address)}
                        <a href="mailto:{$email_address}">{$email_address}</a><br>
                        {/if}
                        <input type="email" name="email_address" class="form-control" value="{if isset($email_address)}{$email_address}{/if}">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">Webseite:</label>
                    <div class="col-md-9">
                        {if isset($website)}
                        <a href="{$website}" target="_blank">{$website}</a><br>
                        {/if}
                        <input type="url" class="form-control" name="website" value="{if isset($website)}{$website}{/if}">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">Artikel-Direktlink:</label>
                    <div class="col-md-9">
                        <input type="url" class="form-control" name="auto_product_url" value="{if isset($auto_product_url)}{$auto_product_url}{/if}">
                        <p class="form-control-static">Platzhalter für die Bestellnummer: <i>%PARTNUMBER%</i></p>
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
                        <button class="btn btn-success" type="submit" name="add">Neuen Lieferanten anlegen</button>
                        <div class="checkbox">
                            <input type="checkbox" name="add_more" {if $add_more}checked{/if}>
                            <label>Weitere Lieferanten anlegen</label>
                        </div>
                    {else}
                        <button class="btn btn-success" type="submit" name="apply">Änderungen übernehmen</button>
                        <button class="btn btn-danger" type="submit" name="delete">Lieferant löschen</button>
                    {/if}
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
