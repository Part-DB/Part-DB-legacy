{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-default">
    <div class="panel-heading">{t}Bauteile importieren{/t}</div>
    <div class="panel-body">
        <form method="post" action="" class="form-horizontal">
          <div class="form-group">
            <div class="col-md-6">
                <label>{t}CSV-Beispiel:{/t}</label>
                <pre>
#{t}Bauteile-ID;Anzahl;Bestückungsdaten{/t}
100;1;
10;4;R5,R6,R7,R8
                </pre>
                <b>oder:</b>
                <pre>
#{t}Bauteil-Name;Anzahl;Bestückungsdaten{/t}
Resistor_SMD_10M_5;4;R5,R6,R7,R8
ATMEGA328_SMD;1;
                </pre>
            </div>

            <div class="col-md-6">
                <label>{t}XML-Beispiel:{/t}</label>
                <pre>
&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot; standalone=&quot;yes&quot;?&gt;
&lt;deviceparts&gt;
  &lt;devicepart&gt;
    &lt;part_id&gt;123&lt;/part_id&gt;
    &lt;mount_quantity&gt;3&lt;/mount_quantity&gt;
    &lt;mount_names&gt;R1, R2, R3&lt;/mount_names&gt;
  &lt;/devicepart&gt;
  &lt;devicepart&gt;
    &lt;part_name&gt;ATMEGA328_SMD&lt;/part_name&gt;
    &lt;mount_quantity&gt;3&lt;/mount_quantity&gt;
    &lt;mount_names&gt;U1&lt;/mount_names&gt;
  &lt;/devicepart&gt;
&lt;/deviceparts&gt;
                </pre>
            </div>
        </div>
                    
        <div class="form-group">
            <label class="col-md-12">{t}Import Text:{/t}</label>
            <div class="col-md-12">
                <textarea class="form-control"  name="import_file_content" rows="10" cols="80" {if !$can_part_create}disabled{/if}>{$import_file_content}</textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3">{t}Export-Format:{/t}</label>
            <div class="col-md-9">
                <input type="hidden" name="device_id" value="{$device_id}">
                <select class="form-control" name="import_format" {if !$can_part_create}disabled{/if}>
                    <option value="CSV" {if $import_format == "CSV"}selected{/if}>{t}CSV{/t}</option>
                    <option value="XML" {if $import_format == "XML"}selected{/if}>{t}XML{/t}</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label col-md-3">{t}Trennzeichen für CSV:{/t}</label>
            <div class="col-md-9">      
                <input class="form-control" type="text" name="import_separator" value="{$import_separator}" size="5" {if !$can_part_create}disabled{/if}>
            </div> 
        </div>
        
        <div class="form-group">
            <div class="col-md-9 col-md-offset-3">
                <button type="submit" class="btn btn-success" name="import_readtext" {if !$can_part_create}disabled{/if}>{t}Daten zum Überprüfen hochladen{/t}</button>
            </div>
        </div>
        
    </form>
        
        {if isset($table) && !empty(!table)}
            <hr>
            <form action="" method="post" id="table" class="form-horizontal">
                <input type="hidden" name="import_rowcount" value="{$import_rowcount}">
                <input type="hidden" name="import_file_content" value="{$import_file_content}">
                <div class="form-group col-md-12">
                    {include "../smarty_table.tpl"}
                </div>
                <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                        <button type="submit" class="btn btn-default" name="check_import_data">{t}Daten übernehmen und prüfen{/t}</button>
                        <button type="submit" class="btn btn-default" name="import_data">{t}Daten übernehmen und importieren!{/t}</button>
                    </div>
                </div>
            </form>
        {/if}
    </div>
</div>
