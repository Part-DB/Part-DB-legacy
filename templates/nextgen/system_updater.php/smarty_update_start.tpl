<script >
    $.ajax("{$relative_path}update_worker.php", {
       async: true,
       beforeSend: function (){ return; },
       complete: function () { return; }
    });
</script>


{if $is_downloading}
    <div class="panel panel-primary">
        <div class="panel-heading">{t}Lade Update herunter{/t}</div>
        <div class="panel-body">
            <div class="progress">
                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                     aria-valuemax="100" style="width: 100%;">
                    <span>{t}Lade{/t}</span>
                </div>
            </div>
            <h4>{t}Dies kann einen Moment dauern...{/t}</h4>
        </div>
    </div>
{else}
    <div class="panel panel-primary">
        <div class="panel-heading"><i class="fa fa-cloud fa-fw" aria-hidden="true"></i>
            {t}Systemupdate{/t}
        </div>
        <div class="panel-body">
            <form method="post" class="form no-progbar">
                <button class="btn btn-primary" type="submit" name="download" value=""
                        {if $is_downloading}disabled{/if}>{t}Downloade neue Version{/t}</button>
            </form>

            <br>

            <form method="post" class="form no-progbar">
                <button class="btn btn-primary" type="submit" name="update" value=""
                        {if $is_downloading}disabled{/if}>{t}Führe Aktualisierung durch.{/t}</button>
            </form>
        </div>
    </div>
{/if}

