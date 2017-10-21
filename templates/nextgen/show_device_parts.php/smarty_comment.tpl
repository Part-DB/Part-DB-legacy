{locale path="nextgen/locale" domain="partdb"}

<div class="panel panel-default">
    <div class="panel-heading">
        <a data-toggle="collapse" class="link-collapse text-default" href="#panel-attachements">
            {t}Kommentar{/t}
        </a>
    </div>
    <div class="panel-body panel-collapse collapse in" id="panel-attachements">
        <pre>{$comment nofilter}</pre>
        <a href="{$relative_path}edit_devices.php?selected_id={$device_id}" class="btn btn-primary"><i class="fa fa-pencil" aria-hidden="true"></i>
            {t}Bearbeiten{/t}</a>
    </div>
</div>
