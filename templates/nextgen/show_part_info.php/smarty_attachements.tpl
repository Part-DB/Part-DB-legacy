{locale path="nextgen/locale" domain="partdb"}

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-file" aria-hidden="true"></i>&nbsp;
        {t}Dateianhänge{/t}
    </div>
    <div class="panel-body">
        {if isset($attachement_types_loop)}
            {foreach $attachement_types_loop as $attach_type}
                <b>{$attach_type.attachement_type}:</b><br>
                {foreach $attach_type.attachements_loop as $attach}
                    {if $attach.is_picture}
                        <a target="_blank" href="{$attach.filename}" class="link-datasheet"">
                        <img src="{$attach.filename}" data-title="{$attach.attachement_name|escape}" class="img-attachement" rel="popover"></a>
                    {else}
                        <a target="_blank" href="{$attach.filename}" class="link-external">{$attach.attachement_name}</a><br>
                    {/if}
                {/foreach}
                <br><br>
            {/foreach}
        {else}
            {t}Dieses Bauteil besitzt keine Dateianhänge.{/t}
            <a class="btn btn-default pull-right hidden-print" class=" hidden-print-href" href="edit_part_info.php?pid={$pid}#attachements">{t}Dateianhänge hinzufügen{/t}</a>
        {/if}
    </div>
</div>