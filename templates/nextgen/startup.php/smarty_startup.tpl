{locale path="nextgen/locale" domain="partdb"}

{if isset($database_update) && $database_update}
    {if $database_update}
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3>
                    <i class="fa fa-database" aria-hidden="true"></i>
                    {t}Datenbankupdate{/t}
                </h3>
            </div>
            <div class="panel-body">
                <b>{t 1=$db_version_current 2=$db_version_latest}Datenbank-Version %1 benötigt ein Update auf Version %2.{/t}</b><br><br>
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


<div class="jumbotron">
    <h1>{if !empty($partdb_title)}{$partdb_title}{else}Part-DB{/if}</h1>
    {if isset($system_version_full)}
        <h3>{t}Version:{/t} {$system_version_full}{if !empty($git_branch)}, Git: {$git_branch}{if isset($git_commit)}/{$git_commit}{/if}{/if}</h3>
    {/if}
    <h4><i>"NextGen"</i></h4>

    {if !empty($banner)}
        <hr>
        <div>
            <h4>{$banner nofilter}</h4>
        </div>
    {/if}
</div>



{if isset($display_warning) && $display_warning}
    <div class="panel panel-danger">
        <div class="panel-heading">
            {t}Achtung!{/t}
        </div>
        <div class="panel-body">
            Bitte beachten Sie, dass vor der Verwendung der Datenbank mindestens<br>
            <blockquote>{$missing_category nofilter}eine <a href="edit_categories.php">{t}Kategorie{/t}</a> </blockquote>hinzufügt werden muss.<br><br>
            Um das Potential der Suchfunktion zu nutzen, wird empfohlen
            <blockquote>{$missing_storeloc nofilter}einen <a href="edit_storelocations.php">{t}Lagerort{/t}</a> </blockquote>
            <blockquote>{$missing_footprint nofilter}einen <a href="edit_footprints.php">{t}Footprint{/t}</a> </blockquote>
            <blockquote>{$missing_supplier nofilter}und einen <a href="edit_suppliers.php">{t}Lieferanten{/t}</a> </blockquote>
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



<div class="panel panel-primary">
    <div class="panel-heading">
        <h3><i class="fa fa-book" aria-hidden="true"></i>&nbsp{t}Lizenz{/t}</h3>
    </div>
    <div class="panel-body">
        <p>Part-DB, Copyright &copy; 2005 of <strong>Christoph Lechner</strong>. <br> Part-DB is published under the <strong>GPL</strong>, so it comes with <strong>ABSOLUTELY NO WARRANTY</strong>,
            click <a href="{$relative_path}readme/gpl.txt" class="link-external" target="_blank">here</a> for details.
            This is free software, and you are welcome to redistribute it under certain conditions.
            Click <a href="{$relative_path}readme/gpl.txt" class="link-external" target="_blank">here</a> for details.<br>
        </p>
        <strong>{t}Projektseite:{/t}</strong> Downloads, Bugreports, ToDo-Liste usw. gibts auf der <a class="link-external" target="_blank" href="https://github.com/do9jhb/Part-DB/">GitHub Projektseite</a><br>
        <strong>{t}Hilfe{/t}</strong> Hilfe und Tipps finden sie im <a class="link-external" href="https://github.com/jbtronics/Part-DB/wiki" target="_blank">Wiki</a> der GitHub Seite. <br>
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
