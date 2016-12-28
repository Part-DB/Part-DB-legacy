{locale path="nextgen/locale" domain="partdb"}
    <div class="jumbotron">
        <h1>Part-DB</h1>
        {if isset($system_version_full)}
        <h3>{t}Version:{/t} {$system_version_full}{if !empty($git_branch)}, Git: {$git_branch}{if isset($git_commit)}/{$git_commit}{/if}{/if}</h3>
        {/if}
        <h4><i>"NextGen"</i></h4>
        <!--
        <a href="zxing://scan/?ret={if isset($smarty.server.HTTPS)}https{else}http{/if}%3A%2F%2F{$smarty.server.HTTP_HOST|escape:'url'}{$relative_path|escape:'url'}show_part_info.php%3Fbarcode%3D%7BCODE%7D&SCAN_FORMATS=EAN_8" class="link-anchor">Barcode Scan</a>
        -->
    </div>
    
    {if isset($database_update) && $database_update}
        {if $database_update}
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h4>{t}Datenbankupdate{/t}</h4>
            </div>
            <div class="panel-body">
            <b>{t 1=$keyword 2=$hits_count escape=no}Datenbank-Version %1 benötigt ein Update auf Version %2.{/t}</b><br><br>
            {if isset($disabled_autoupdate)}
            {if isset($auto_disabled_autoupdate)}
                <p>{t}Automatische Datenbankupdates wurden vorübergehend automatisch deaktiviert,
                da es sich um ein sehr umfangreiches Update handelt.{/t}</p>
            {else}
                <p>{t}Automatische Datenbankupdates sind deaktiviert.{/t}</p>
            {/if}
            {t}Updates bitte manuell durchführen:{/t} <a href="system_database.php">{t}System -> Datenbank{/t}</a>
        {else}
            {$database_update_log}
        {/if}
            </div>
        </div>
        {/if}
    {/if}

{if isset($display_warning) && $display_warning}
        <div class="panel panel-danger">
            <div class="panel-heading">
                {t}Achtung!{/t}
            </div>
        <div class="panel-body">
            Bitte beachten Sie, dass vor der Verwendung der Datenbank mindestens<br>
            <blockquote>{$missing_category}eine <a href="edit_categories.php" target="content_frame">{t}Kategorie{/t}</a> </blockquote>hinzufügt werden muss.<br><br>
            Um das Potential der Suchfunktion zu nutzen, wird empfohlen
            <blockquote>{$missing_storeloc}einen <a href="edit_storelocations.php">{t}Lagerort{/t}</a> </blockquote>
            <blockquote>{$missing_footprint}einen <a href="edit_footprints.php">{t}Footprint{/t}</a> </blockquote>
            <blockquote>{$missing_supplier}und einen <a href="edit_suppliers.php">{t}Lieferanten{/t}</a> </blockquote>
            anzugeben.
        </div>
    </div>
{/if}

{if isset($broken_filename_footprints) && $broken_filename_footprints}
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h2 class="red">{t}Achtung!{/t}</h2>
            </div>
        <div class="panel-body">
        <span style="color: red; ">{t}In Ihrer Datenbank gibt es Footprints, die einen fehlerhaften Dateinamen hinterlegt haben.
        Dies kann durch ein Datenbankupdate, ein Update von Part-DB, oder durch nicht mehr existierende Dateien ausgelöst worden sein.{/t}
        <br>
        {t escape=none}Sie können dies unter <a href="edit_footprints.php">Bearbeiten/Footprints</a> (ganz unten, "Fehlerhafte Dateinamen") korrigieren.{/t}
        </span>
    </div>
    </div>
{/if}

{$banner}

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3><i class="fa fa-book" aria-hidden="true"></i>&nbsp{t}Lizenz{/t}</h3>
    </div>
    <div class="panel-body">
       <!-- Doesnt work! Paypal has changed API?
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="GE4ABWP3JUHLL">
            <input type="image" src="https://www.paypalobjects.com/de_DE/CH/i/btn/btn_donateCC_LG.gif" border="0" name="submit" align="right" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
            <img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
        </form>
         -->

        Part-DB, Copyright &copy; 2005 of <strong>Christoph Lechner</strong>. Part-DB is published under the <strong>GPL</strong>, so it comes with <strong>ABSOLUTELY NO WARRANTY</strong>, click <a href="{$relative_path}readme/gpl.txt">here</a> for details. This is free software, and you are welcome to redistribute it under certain conditions. Click <a href="{$relative_path}readme/gpl.txt">here</a> for details.<br>
        <br>
        <strong>{t}Projektseite:{/t}</strong> Downloads, Bugreports, ToDo-Liste usw. gibts auf der <a class="link-external" target="_blank" href="https://github.com/do9jhb/Part-DB/">GitHub Projektseite</a><br>
        <strong>Forum:</strong> Für Fragen rund um die Part-DB gibt es einen Thread auf <a class="link-external" target="_blank" href="https://www.mikrocontroller.net/topic/305023">mikrocontroller.net</a><br>
        <strong>Wiki:</strong> Weitere Informationen gibt es im <a class="link-external" target="_blank" href="http://www.mikrocontroller.net/articles/Part-DB_RW_-_Lagerverwaltung">mikrocontroller.net Artikel</a><br>
        <br>
        {t}Initiator:{/t} <strong>Christoph Lechner</strong> - <a class="link-external" target="_blank" href="http://www.cl-projects.de/">http://www.cl-projects.de/</a><br>
        {t}Autor seit 2009:{/t} <strong>K. Jacobs</strong> - <a class="link-external" target="_blank" href="http://www.grautier.com/">http://grautier.com</a><br>
        {t}Neues Design 2016 durch:{/t}  <strong>Jan Böhmer</strong><br>
        <br>
        {t}Weitere Autoren:{/t}
        <table class="table">
            <tbody>
            {foreach $authors as $author}
                <tr><td><strong>{$author.name}</strong></td><td>{$author.role}</td></tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>

{if !empty($rss_feed_loop)}
<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-rss" aria-hidden="true"></i>&nbsp{t}Updates{/t}</h4>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>{t}Version{/t}</th>
                    <th>Veröffentlichungsdatum</th>
                    <th>Link</th>
                </tr>
            </thead>
            <tbody>
            {foreach $rss_feed_loop as $rss}
                <tr>
                    <td>{$rss.title}</td>
                    <td>{$rss.datetime}</td>
                    <td><a href="{$rss.link}" class="link-external" target="_blank">{$rss.link}</a></td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
{/if}
