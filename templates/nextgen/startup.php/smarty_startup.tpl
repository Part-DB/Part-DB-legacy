
    <div class="jumbotron">
        <h1>Part-DB</h1>
        {if isset($system_version_full)}
        <h3>Version: {$system_version_full}{if isset($git_branch)}, Git: {$git_branch}{if isset($git_commit)}/{$git_commit}{/if}{/if}</h3>
        {/if}
        <h4><i>"NextGen"</i></h4>
    </div>
    
    {if isset($database_update)}
        {if $database_update}
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h2>Datenbankupdate</h2>
            </div>
            <div class="panel-body">
            <b>Datenbank-Version {$db_version_current} benötigt ein Update auf Version {$db_version_latest}.</b><br><br>
            {if isset($disabled_autoupdate)}
            {if isset($auto_disabled_autoupdate)}
                <p>Automatische Datenbankupdates wurden vorübergehend automatisch deaktiviert,
                da es sich um ein sehr umfangreiches Update handelt.</p>
            {else}
                <p>Automatische Datenbankupdates sind deaktiviert.</p>
            {/if}
            Updates bitte manuell durchführen: <a href="system_database.php">System -> Datenbank</a>
        {else}
            {$database_update_log}
        {/if}
            </div>
        </div>
        {/if}
    {/if}

{if $display_warning}
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h2 class="red">Achtung!</h2>
            </div>
        <div class="panel-body">
            Bitte beachten Sie, dass vor der Verwendung der Datenbank mindestens<br>
            <blockquote>{$missing_category}eine <a href="edit_categories.php" target="content_frame">Kategorie</a> </blockquote>hinzufügt werden muss.<br><br>
            Um das Potential der Suchfunktion zu nutzen, wird empfohlen
            <blockquote>{$missing_storeloc}einen <a href="edit_storelocations.php">Lagerort</a> </blockquote>
            <blockquote>{$missing_footprint}einen <a href="edit_footprints.php">Footprint</a> </blockquote>
            <blockquote>{$missing_supplier}und einen <a href="edit_suppliers.php">Lieferanten</a> </blockquote>
            anzugeben.
        </div>
    </div>
{/if}

{if $broken_filename_footprints}
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h2 class="red">Achtung!</h2>
            </div>
        <div class="panel-body">
        <font color="red">In Ihrer Datenbank gibt es Footprints, die einen fehlerhaften Dateinamen hinterlegt haben.
        Dies kann durch ein Datenbankupdate, ein Update von Part-DB, oder durch nicht mehr existierende Dateien ausgelöst worden sein.
        <br>
        Sie können dies unter <a href="edit_footprints.php">Bearbeiten/Footprints</a> (ganz unten, "Fehlerhafte Dateinamen") korrigieren.
        </font>
    </div>
    </div>
{/if}

{$banner}

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3>Lizenz</h3>
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
        <strong>Projektseite:</strong> Downloads, Bugreports, ToDo-Liste usw. gibts auf der <a target="_blank" href="https://github.com/sandboxgangster/Part-DB">GitHub Projektseite</a><br>
        <strong>Forum:</strong> Für Fragen rund um die Part-DB gibt es einen Thread auf <a target="_blank" href="https://www.mikrocontroller.net/topic/305023">mikrocontroller.net</a><br>
        <strong>Wiki:</strong> Weitere Informationen gibt es im <a target="_blank" href="http://www.mikrocontroller.net/articles/Part-DB_RW_-_Lagerverwaltung">mikrocontroller.net Artikel</a><br>
        <br>
        Initiator: <strong>Christoph Lechner</strong> - <a target="_blank" href="http://www.cl-projects.de/">http://www.cl-projects.de/</a><br>
        Autor seit 2009: <strong>K. Jacobs</strong> - <a target="_blank" href="http://www.grautier.com/">http://grautier.com</a><br>
        <br>
        Weitere Autoren:
        <table class="table">
        {foreach $authors as $author}
            <tr><td><strong>{$author.name}</strong></td><td>{$author.role}</td></tr>
        {/foreach}
        </table>
    </div>
</div>

{if isset($rss_feed_loop)}
<div class="panel panel-info">
    <div class="panel-heading">
        <h3>Updates</h3>
    </div>
    <div class="panel-body">
        {foreach $rss_feed_loop as $rss}
            <b>{$rss.title}</b><br>
            {$rss.datetime}<br>
            <a href="{$rss.link}" target="_blank">{$rss.link}</a>
        {/foreach}
    <br>
    </div>
</div>
{/if}
    
    
</div>