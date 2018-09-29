{locale path="nextgen/locale" domain="partdb"}

<div class="card mt-3">
    <div class="card-header">
        <a data-toggle="collapse" class="link-collapse text-default" href="#panel-attachements"><i class="fa fa-file fa-fw" aria-hidden="true"></i>
            {t}Dateianhänge{/t}
        </a>
    </div>
    <div class="card-body card-collapse collapse show" id="panel-attachements">
        {if isset($attachement_types_loop)}
            {foreach $attachement_types_loop as $attach_type}
                <b>{$attach_type.attachement_type}:</b><br>
                {foreach $attach_type.attachements_loop as $attach}
                    {if $attach.is_picture}
                        <a target="_blank" rel="noopener" href="{$attach.filename}" class="link-datasheet">
                            <img src="{$attach.filename}" data-title="{$attach.attachement_name|escape}" class="img-attachement" rel="popover">
                        </a>
                    {else}
                        <a target="_blank" rel="noopener" href="{$attach.filename}" class="link-external">{$attach.attachement_name}</a><br>
                    {/if}
                {/foreach}
                <br><br>
            {/foreach}
        {else}
            <span class="form-text text-muted" style="display: inline;">{t}Dieses Bauteil besitzt keine Dateianhänge.{/t}</span>
            <a class="btn btn-secondary float-right d-print-none" class=" d-print-none-href"
               href="edit_part_info.php?pid={$pid}#attachements"
               {if !$can_orderdetails_create}disabled{/if}>
                {t}Dateianhänge hinzufügen{/t}</a>
        {/if}
    </div>
</div>