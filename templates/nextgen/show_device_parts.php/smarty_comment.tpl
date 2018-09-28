{locale path="nextgen/locale" domain="partdb"}

<div class="card mt-3">
    <div class="card-header">
        <a data-toggle="collapse" class="link-collapse text-default" href="#panel-comment">
            <i class="fas fa-comment fa-fw"></i> {t}Kommentar{/t}
        </a>
    </div>
    <div class="card-body card-collapse collapse {if !empty($comment)}in{/if}" id="panel-comment">
        {if !empty($comment)}<pre>{$comment nofilter}</pre>{/if}
        {if !empty($comment)}
            <a href="{$relative_path}edit_devices.php?selected_id={$device_id}" class="btn btn-primary {if !$can_attachement_edit}disabled{/if}"><i class="fas fa-pencil-alt fa-fw" aria-hidden="true"></i>
                {t}Bearbeiten{/t}</a>
        {else}
            <p class="help-block">{t}Bisher existiert noch kein Kommentar zu dieser Baugruppe.{/t}</p>
            <a href="{$relative_path}edit_devices.php?selected_id={$device_id}" class="btn btn-success {if !$can_attachement_edit}disabled{/if}"><i class="fas fa-plus-square fa-fw" aria-hidden="true"></i>
                {t}Hinzufügen{/t}</a>
        {/if}
    </div>
</div>
