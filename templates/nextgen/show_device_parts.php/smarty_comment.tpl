{locale path="nextgen/locale" domain="partdb"}

<div class="panel panel-default">
    <div class="panel-heading">
        <a data-toggle="collapse" class="link-collapse text-default" href="#panel-comment">
           {t}Kommentar{/t}
        </a>
    </div>
    <div class="panel-body panel-collapse collapse {if !empty($comment)}in{/if}" id="panel-comment">
        {if !empty($comment)}<pre>{$comment nofilter}</pre>{/if}
        {if !empty($comment)}
            <a href="{$relative_path}edit_devices.php?selected_id={$device_id}" class="btn btn-primary {if !$can_attachement_edit}disabled{/if}"><i class="fa fa-pencil fa-fw" aria-hidden="true"></i>
                {t}Bearbeiten{/t}</a>
        {else}
            <p class="help-block">{t}Bisher existiert noch kein Kommentar zu dieser Baugruppe.{/t}</p>
            <a href="{$relative_path}edit_devices.php?selected_id={$device_id}" class="btn btn-success {if !$can_attachement_edit}disabled{/if}"><i class="fa fa-plus-square fa-fw" aria-hidden="true"></i>
                {t}Hinzufügen{/t}</a>
        {/if}
    </div>
</div>
