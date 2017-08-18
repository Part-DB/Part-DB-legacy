{locale path="nextgen/locale" domain="partdb"}

<div class="panel panel-primary">
    <div class="panel-heading">Debug-Konsole</div>
    <div class="panel-body">
        <form action="" method="post">
            {if $debug_enable}
                <div class="form-group"><label><span style="color: #008000; ">Debugging ist aktiviert</span></label>
                    <div class="col-md-12"><button class="btn btn-primary" type="submit" name="disable">Deaktivieren</button>
                        <button class="btn btn-default" type="submit" name="disable_and_delete">Deaktivieren und Log-Datei löschen</button>
                    </div></div><br>
                <div class="form-group"><label class="control-label">Testeintrag erzeugen:</label>
                    <div class="col-md-5"><input type="text" class="form-control" name="new_type" value="warning"></div>
                    <div class="col-md-5"><input type="text" class="form-control" name="new_text" class="form-control" value="Testeintrag"></div>
                    <div class="col-md-2"><button class="btn btn-primary" type="submit" name="add">Hinzuf&uuml;gen</button></div></div>
                <hr><button type="submit" class="btn btn-default" name="clear">Log leeren</button>
                <button type="submit" name="download" class="btn btn-default">Log als XML-Datei herunterladen</button>
                {if $autorefresh}
                    <hr><button type="submit" name="stop_autorefresh" class="btn btn-default" >Autorefresh deaktivieren</button>
                {else}
                    <input type="hidden" name="autorefresh_disabled">
                    <hr><button type="submit" class="btn btn-default" name="start_autorefresh">Autorefresh aktivieren</button>
                {/if}
            {else}
                <strong><span style="color: #ff0000; ">Debugging ist deaktiviert</span></strong><br>
                <div class="form-group"><label>Administratorpasswort zum aktivieren:</label>
                    <div class="input-group"><input class="form-control" type="password" name="admin_password" value="">
                        <div class="input-group-btn"><button class="btn btn-primary" type="submit" name="enable">Aktivieren</button></div></div></div>
            {/if}
            {if count($errors) > 0}
                <div class="alert alert-danger"><strong><span style="color: #ff0000;">
                        {foreach $errors as $error}
                            {$error} <br>
                        {/foreach}
                            </font></strong></div>
            {/if}
        </form>
    </div>
</div>

{if $debug_enable}
    <div class="panel panel-default">
        <div class="panel-heading">Debug-Log</div>
        <div class="panel-body">
            Folgende Log-Typen werden hervorgehoben:
            "<span style="color: darkgreen; ">success</span>",
            "<span style="color: darkorange; ">warning</span>",
            "<strong><span style="color: red; ">error</span></strong>",
            "<strong><span style="color: blue; ">temp</span></strong><br>"
            Zus&auml;tzliche Informationen k&ouml;nnen angezeigt werden,
            indem man mit der Maus &uuml;ber den entsprechenden Eintrag f&auml;hrt.
            <select name="debug_log" size="25" class="form-control">
                {foreach $logs as $log}

                {if strtolower($log['type']) == "success"}
                    {assign "style" "color:darkgreen"}
                {elseif strtolower($log['type']) == "warning"}
                    {assign "style" "color:darkorange"}
                {elseif strtolower($log['type']) == "error"}
                    {assign "style" "color:red;font-weight:bold"}
                {elseif strtolower($log['type']) == "temp"}
                    {assign "style" "color:blue;font-weight:bold"}
                {else}
                    {assign "style" ""}
                {/if}

                <option style="{$style}" title="in file {$log['file']} on line {$log['line']} {if !empty($log['function'])} in function {$log['function']}{/if})">
                    [{$log['datetime']}] {$log['type']}: {$log['message']}</option>
                {/foreach}

            </select>
        </div>
    </div>
{/if}