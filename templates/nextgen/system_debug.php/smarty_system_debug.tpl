{locale path="nextgen/locale" domain="partdb"}

<div class="panel panel-primary">
    <div class="panel-heading">Debug-Konsole</div>
    <div class="panel-body">
        <form action="" method="post" class="form-horizontal no-progbar">
            {if $debug_enable}
                <div class="form-group"><label class="control-label text-success col-md-2">Debugging ist aktiviert:</label>
                    <div class="col-md-2">
                        <button class="btn btn-primary" type="submit" name="disable">Deaktivieren</button>

                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-default" type="submit" name="disable_and_delete">Deaktivieren und Log-Datei löschen</button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">Testeintrag erzeugen:</label>
                    <div class="col-md-4">
                        <select name="new_type" class="form-control">
                            <option value="success">Success</option>
                            <option value="warning" selected>Warning</option>
                            <option value="error">Error</option>
                            <option value="temp">Temp</option>
                        </select>
                    </div>
                    <div class="col-md-4"><input type="text" class="form-control" name="new_text" class="form-control" value="Testeintrag" placeholder="Wert"></div>
                    <div class="col-md-2"><button class="btn btn-primary" type="submit" name="add">Hinzuf&uuml;gen</button></div>
                </div>
                <div class="form-group">
                    <div class="col-md-2 col-md-offset-2">
                        <button type="submit" class="btn btn-default" name="clear">Log leeren</button>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="download" class="btn btn-default">Log als XML-Datei herunterladen</button>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <div class="col-md-offset-2 col-md-2">
                            <button type="button" class="btn btn-default" onclick="reloadPage();">Aktualisieren</button>
                    </div>
                </div>
            {else}
                <h4><span class="text-danger">Debugging ist deaktiviert</span></h4>
                <br>
                <div class="form-group"><label class="control-label col-md-2">Administratorpasswort zum aktivieren:</label>
                    <div class="col-md-10">
                    <div class="input-group">
                        <input class="form-control" type="password" name="admin_password" value="">
                        <div class="input-group-btn">
                            <button class="btn btn-primary" type="submit" name="enable">Aktivieren</button>
                        </div>
                    </div>
                    </div>
                </div>
            {/if}
        </form>
    </div>
</div>

{if count($errors) > 0}
    <div class="alert alert-danger">
        <strong>
            {foreach $errors as $error}
                {$error} <br>
            {/foreach}
        </strong>
    </div>
{/if}

{if $debug_enable}
    <div class="panel panel-default">
        <div class="panel-heading">Debug-Log</div>
        <div class="panel-body">
            Folgende Log-Typen werden hervorgehoben:
            "<span class="text-success">success</span>",
            "<span class="text-warning">warning</span>",
            "<strong><span class="text-danger">error</span></strong>",
            "<strong><span class="text-info">temp</span></strong>"<br>
            Zus&auml;tzliche Informationen k&ouml;nnen angezeigt werden,
            indem man mit der Maus &uuml;ber den entsprechenden Eintrag f&auml;hrt.
            <select name="debug_log" size="25" class="form-control">
                {foreach $logs as $log}
                    {if strtolower($log['type']) == "success"}
                        {assign "style" "text-success"}
                    {elseif strtolower($log['type']) == "warning"}
                        {assign "style" "text-warning"}
                    {elseif strtolower($log['type']) == "error"}
                        {assign "style" "text-danger font-weight-bold"}
                    {elseif strtolower($log['type']) == "temp"}
                        {assign "style" "text-info font-weight-bold"}
                    {else}
                        {assign "style" ""}
                    {/if}

                    <option class="{$style}" title="in file {$log['file']} on line {$log['line']} {if !empty($log['function'])} in function {$log['function']}{/if})">
                        [{$log['datetime']}] {$log['type']}: {$log['message']}</option>
                {/foreach}

            </select>
        </div>
    </div>
{/if}