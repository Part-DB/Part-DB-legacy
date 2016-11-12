{locale path="nextgen/locale" domain="partdb"}
{if isset($refresh_navigation_frame)}
<script type="text/javascript">
    parent.frames.navigation_frame.location.reload();
</script>
{/if}

<div class="panel panel-default">
    <div class="panel-heading">
        <h4>{t}Systemeinstellungen{/t}</h4>
    </div>
    <div class="panel-body">
        
        <form class="form-horizontal" action="" method="post">
                
                <p><i>{t}Auf dieser Seite sind nur die wichtigsten Einstellungen vorhanden, weitere
                Einstellungen kann man direkt in der "config.php" vornehmen. Mögliche Parameter
                entnehmen Sie bitte der "config_defaults.php" oder der Dokumentation.{/t}</i></p>
        
                <hr>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="theme">{t}Theme:{/t}</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="theme">
                            {foreach $theme_loop as $theme}
                                <option value="{$theme.value}" {if $theme.selected}selected{/if}>{$theme.text}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="custom_css">{t}CSS-Datei:{/t}</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="custom_css">
                            <option value="">{t}Standard des verwendeten Themes verwenden{/t}</option>
                            {foreach $custom_css_loop as $css}
                                <option value="{$css.value}" {if $css.selected}selected{/if}>{$css.text}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="timezon">{t}Zeitzone:{/t}</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="timezone">
                            {foreach $timezone_loop as $timezone}
                                <option value="{$timezone.value}" {if $timezone.selected}selected{/if}>{$timezone.text}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="language">{t}Sprache:{/t}</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="language">
                            {foreach $language_loop as $lang}
                                <option value="{$lang.value}" {if $lang.selected}selected{/if}>{$lang.text}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label for="checkbox-container" class="control-label col-sm-2">{t}Allgemeine Einstellungen:{/t}</label>
                    
                    <div name="checkbox-container" class="col-sm-10">
                        <div class="checkbox">
                            <input type="checkbox" name="disable_updatelist" {if $disable_updatelist} checked{/if}>
                            <label for="disable_updatelist">{t}Updateliste (RSS-Feed) auf Startseite verstecken (verringert die Ladezeit){/t}</label>
                        </div>
                
                        <div class="checkbox">
                            <input type="checkbox" name="disable_footprints" {if $disable_footprints} checked{/if}>
                            <label for="disable_footprints">{t}Footprints global deaktivieren{/t} *</label>
                        </div>

                        <div class="checkbox">
                            <input type="checkbox" name="disable_manufacturers" {if $disable_manufacturers} checked{/if}>
                            <label for="disable_manufacturers">{t}Hersteller global deaktivieren{/t}</label> 
                        </div>

                        <div class="checkbox">
                            <input type="checkbox" name="disable_devices" {if $disable_devices} checked{/if}>
                            <label for="disable_devices">{t}Baugruppenfunktion global deaktivieren{/t} *</label>
                        </div>
                    
                        <div class="checkbox">
                            <input type="checkbox" name="disable_auto_datasheets" {if $disable_auto_datasheets} checked{/if}>
                            <label for="disable_auto_datasheets">{t}Automatische Links zu Datenblättern global deaktivieren{/t} *</label>
                        </div>
                        
                        <div class="checkbox">
                            <input type="checkbox" name="disable_help" {if $disable_help} checked{/if}>
                            <label for="disable_help">{t}Menüpunkt "Hilfe" deaktivieren{/t}</label>
                        </div>
                        
                        <div class="checkbox">
                            <input type="checkbox" name="disable_config" {if $disable_config} checked{/if} {if $is_online_demo}disabled{/if}>
                            <label>{t}Menüpunkt "System" deaktivieren{/t}</label>
                        </div>
                        
                        <div class="checkbox">
                            <input type="checkbox" name="enable_debug_link" {if $enable_debug_link} checked{/if}>
                            <label>{t}Menüpunkt "System -> Debugging" aktivieren{/t}</label>
                        </div>
                        
                        <div class="checkbox">
                            <input type="checkbox" name="disable_labels" {if $disable_labels} checked{/if}>
                            <label>{t}Menüpunkt "Tools -> Labels" deaktivieren{/t} *</label>
                        </div>
                        
                        <div class="checkbox">
                            <input type="checkbox" name="disable_calculator" {if $disable_calculator} checked{/if}>
                            <label>{t}Menüpunkt "Tools -> Widerstandsrechner" deaktivieren{/t} *</label>
                        </div>
                    
                        <div class="checkbox">
                            <input type="checkbox" name="disable_iclogos" {if $disable_iclogos} checked{/if}>
                            <label>{t}Menüpunkt "Tools -> IC-Logos" deaktivieren{/t} *</label>
                        </div>

                        <div class="checkbox">
                            <input type="checkbox" name="disable_tools_footprints" {if $disable_tools_footprints} checked{/if}>
                            <label>{t}Menüpunkt "Tools -> Footprints" deaktivieren{/t} *</label>
                        </div>
                    
                        <div class="checkbox">
                            <input type="checkbox" name="tools_footprints_autoload" {if $tools_footprints_autoload} checked{/if}>
                            <label>{t}Unter "Tools -> Footprints" beim Aufruf automatisch alle Bilder laden (lange Ladezeit!){/t}</label>
                        </div>

                        {if $developer_mode_available}
                        <div class="checkbox">
                            <input type="checkbox" name="enable_developer_mode" {if $enable_developer_mode} checked{/if}>
                            <label>{t}Entwickler-Werkzeuge aktivieren (für Entwickler und Tester){/t}</label>
                        </div>

                        <div class="checkbox">
                            <input type="checkbox" name="enable_dokuwiki_write_perms" {if $enable_dokuwiki_write_perms} checked{/if} {if $is_online_demo}disabled{/if}>
                            <label>{t}Schreibrechte im DokuWiki aktivieren{/t}</label>
                        </div>
                        {/if}
                        
                        
                        <p></p>
                        <div>
                            * <i>{t}Durch das Aktivieren dieser Checkboxen ist Part-DB auch für Nicht-Elektronische Bauteile hervorragend geeignet.{/t}</i>
                        </div>
                    
                    </div>
         
                </div>
                   
                   
                {*
                <hr>
               
                <div class="form-group">
                    <label for="modal-container" class="control-label col-sm-2">Modale Dialoge:</label>
                        <div class="checkbox col-sm-10">
                            <input type="checkbox" name="use_modal_popup" id="use_modal_popup" {if $use_modal_popup}checked{/if}>
                            <label>Modale Dialoge verwenden</label>
                        </div>
                </div>
                            
                <!-- //Height and size not used because popups not recommended. Maybe implement real modal dialogs later            
                
                <div class="form-group">
                        <label class="control-label col-sm-2">Dialogbreite:</label>
                        <div class="col-sm-10">
                            <input name="popup_width" id="popup_width" class="form-control" size="5" onkeypress="validateNumber(event)" value="{$popup_width}">
                        </div>
                </div>
                    

                <tr>
                    <td>Dialoghöhe:</td>
                    <td>
                        <input name="popup_height" id="popup_height" size="5" onkeypress="validateNumber(event)" value="{$popup_height}">
                    </td>
                </tr> -->

               *}

                <hr>

                <div class="form-group">
                    <label for="page_title" class="control-label col-sm-2">{t}Titel der Seite:{/t}</label>
                    <div class="col-sm-10">
                        <input type="text" name="page_title" class="form-control" placeholder="{t}Part-DB Elektronische Bauteile-Datenbank{/t}" value="{$page_title}" {if $is_online_demo}disabled{/if}>
                    </div>
                </div>

                <div class="form-group">
                    <label for="startup_banner" class="control-label col-sm-2">{t}Eigener Banner für die Startseite (HTML):{/t}</label>
                    <div class="col-sm-10">
                        <textarea name="startup_banner" rows="5" class="form-control"  {if $is_online_demo}disabled{/if}>{$startup_banner}</textarea>
                    </div>
                </div>

                <hr>

                <div class="col-sm-offset-2">
                    <button class="btn btn-success" type="submit" name="apply">{t}Einstellungen übernehmen{/t}</button>
                    <button class="btn btn-danger" type="submit">{t}Änderungen verwerfen{/t}</button>
                </div>
            </table>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4>{t}Administratorpasswort ändern{/t}</h4>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" method="post">
            <div class="form-group">
                <label class="control-label col-sm-2" for="current_admin_password">{t}Aktuelles Passwort:{/t}</label>
                <div class="col-sm-10">
                    <input class="form-control" type="password" name="current_admin_password" {if $is_online_demo}disabled{/if}>
                </div>
            </div>

            <div class="form-group">
                <label for="new_admin_password_1" class="col-sm-2 control-label">{t}Neues Passwort:{/t}</label>
                    <div class="col-sm-10">
                        <input type="password"  class="form-control" name="new_admin_password_1" {if $is_online_demo}disabled{/if}>
                    </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="new_admin_password_2">{t}Neues Passwort (Wiederholung):{/t}</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="new_admin_password_2" {if $is_online_demo}disabled{/if}>
                </div>
            </div>

           <hr>
                    
            <div class="col-sm-offset-2">
                <input type="submit" class="btn btn-success" name="change_admin_password" value="Passwort ändern" {if $is_online_demo}disabled{/if}>
            </div>
            
        </form>
    </div>
</div>

{if !$is_online_demo}
    <div class="panel panel-default">
       <div class="panel-heading">
            <h4>{t}Server{/t}</h4>
        </div>
        <div class="panel-body">
            <table width="100%">
                <tr>
                    <td><b>{t}PHP-Version:{/t}</b></td>
                    <td>{$php_version}</td>
                </tr>
                <tr>
                    <td><b>{t}.htaccess funktioniert:{/t}</b></td>
                    <td>{if $htaccess_works}<font color="green">ja</font>{else}<font color="red">nein</font>{/if}</td>
                </tr>
            </table>
        </div>
    </div>
{/if}
