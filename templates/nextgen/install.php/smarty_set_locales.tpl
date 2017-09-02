{locale path="nextgen/locale" domain="partdb"}

<!--suppress Annotator -->
<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;
        {t}Willkommen bei Part-DB!{/t}</div>
    <div class="panel-body">
        Part-DB, Copyright &copy; 2005 of <strong>Christoph Lechner</strong>. Part-DB is published under the <strong>GPL</strong>, so it comes with <strong>ABSOLUTELY NO WARRANTY</strong>, click <a href="{$relative_path}readme/gpl.txt">here</a> for details. This is free software, and you are welcome to redistribute it under certain conditions. Click <a href="{$relative_path}readme/gpl.txt">here</a> for details.<br>
        <br>
        <strong>{t}Projektseite:{/t}</strong> {t escape=off}Downloads, Bugreports, ToDo-Liste usw. gibts auf der <a target="_blank" href="https://github.com/jbtronics/Part-DB">GitHub Projektseite</a>{/t}<br>
        <strong>{t}Forum:{/t}</strong> {t escape=off}Für Fragen rund um die Part-DB gibt es einen Thread auf <a target="_blank" href="https://www.mikrocontroller.net/topic/305023">mikrocontroller.net</a>{/t}<br>
        <strong>{t}Wiki:{/t}</strong> {t escape=off}Weitere Informationen gibt es im <a target="_blank" href="http://www.mikrocontroller.net/articles/Part-DB_RW_-_Lagerverwaltung">mikrocontroller.net Artikel{/t}</a>
    </div>
    <br>
    <div style="background-color:#FFFC86;">
        <table><tr>
                <td><img src="{$relative_path}img/partdb/help.png"></td>
                <td>
                    <strong>{t escape=off}In der <a href="https://github.com/jbtronics/Part-DB/wiki" target="_blank">Dokumentation</a> gibt es eine Installationsanleitung, FAQ und weitere Informationen.</strong><br>{/t}
                </td>
            </tr></table>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-globe" aria-hidden="true"></i>&nbsp;
        {t}Installation/Update: Zeitzone und Sprache{/t}</div>
    <div class="panel-body">

        <p>{t}Stellen Sie hier bitte Ihre Zeitzone und Ihre Sprache ein.
                Anhand der gewählten Sprache wird dann auch die Währung gesetzt.{/t}</p>

        <form action="" method="post" class="form-horizontal">
            <div class="form-group">
                <div class="col-md-9 col-md-offset-3">

                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">{t}Zeitzone:{/t}</label>
                <div class="col-md-9">
                    <select name="timezone" class="form-control">
                        {foreach $timezone_loop as $timezone}
                            <option value="{$timezone.value}" {if $timezone.selected}selected{/if}>{$timezone.text}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3">{t}Sprache:{/t}</label>
                <div class="col-md-9">
                    <select name="language" class="form-control">
                        {foreach $language_loop as $lang}
                            <option value="{$lang.value}" {if $lang.selected}selected{/if}>{$lang.text}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                    <button type="submit" class="btn btn-primary" name="save_locales">{t}Weiter{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>