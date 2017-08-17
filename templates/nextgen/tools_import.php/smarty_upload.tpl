{locale path="nextgen/locale" domain="partdb"}
{if isset($refresh_navigation_frame) && $refresh_navigation_frame}
    <script type="text/javascript">
        location.reload();
    </script>
{/if}

<div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;
        {t}Datei auswählen{/t}</div>
    <div class="panel-body">
        <form enctype="multipart/form-data" action="" method="post" class="form-horizontal no-progbar">
            <div class="form-group">
                <label class="col-md-2 control-label">{t}Dateityp:{/t}</label>
                <div class="col-md-7 ">
                    <select name="file_format" class="form-control">
                        <option value="CSV" {if $file_format == "CSV"}selected{/if}>CSV</option>
                        <option value="XML" {if $file_format == "XML"}selected{/if}>XML</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="pull-right">
                        <input data-show-caption="false" data-show-upload="false" data-show-preview="false" type="file" class="file"
                               name="uploaded_file" accept=".csv,.xml">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-2">{t}Trennzeichen für CSV:{/t}</label> 
                <div class="col-md-2">
                    <input type="text" class="form-control" name="separator" size="5" maxlength="10" value="{$separator}" required>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2 col-md-offset-2">
                    <button class="btn btn-success" type="submit" name="upload_file">{t}Datei hochladen{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>
