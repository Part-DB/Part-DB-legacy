<script >
    $.ajax("{$relative_path}update_worker.php", {
       async: true,
       beforeSend: function (){ return; },
       complete: function () { return; }
    });
</script>

<div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-cloud fa-fw" aria-hidden="true"></i>
        {t}Systemupdate{/t}
    </div>
    <div class="panel-body">
        <form method="post" class="form no-progbar">
            <button class="btn btn-primary" type="submit" name="download" value=""
                    {if $is_downloading}disabled{/if}>{t}Downloade neue Version{/t}</button>
        </form>
    </div>
</div>