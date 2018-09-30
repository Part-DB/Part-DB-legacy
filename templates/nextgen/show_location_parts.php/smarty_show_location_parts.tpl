{locale path="nextgen/locale" domain="partdb"}

{include "../smarty_breadcrumb.tpl"}

<span id="export-title">{t}Bauteile im Lagerort{/t}: {$location_name}</span>
<span id="export-messageTop">{t}Vollständiger Pfad{/t}: {$location_fullpath}</span>

{if $other_panel_position == "top" || $other_panel_position == "both"}
    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            <a data-toggle="collapse" class="link-collapse text-white" href="#panel-other">
                {t}Sonstiges{/t}
            </a>
        </div>
        <div class="card-body card-collapse collapse {if !$other_panel_collapse}show{/if}" id="panel-other">
            <form action="" method="post" class="form-horizontal no-progbar">
                <input type="hidden" name="lid" value="{$lid}">
                <input type="hidden" name="subloc" value="{if $with_sublocations}0{else}1{/if}">

                <div class="form-group row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-outline-secondary {if $with_sublocations}active{/if}" name="subloc_button" >{t}Unterlagerorte einblenden{/t}</button>
                    </div>
                </div>
            </form>

            {if $can_generate_barcode}
                <form action="show_part_label.php" method="get" class="form-horizontal">
                    <div class="form-group row">
                        <input type="hidden" name="label_generate">
                        <input type="hidden" name="generator" value="location">
                        <input type="hidden" name="id" value="{$lid}">

                        <div class="col-md-12">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-outline-secondary"><i class="fa fa-barcode fa-fw" aria-hidden="true"></i>
                                    {t}Barcode erzeugen{/t}</button>
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-right" id="label-dropdown">
                                    {foreach $barcode_profiles as $profile}
                                        <li><a href="#" class="link-anchor" onclick="submitFormSubmitBtn($(this).closest('form'), $('#profile_btn_{$profile|replace:" ":"_"}'));">{$profile}</a>
                                            <button type="submit" name="profile" id="profile_btn_{$profile|replace:" ":"_"}" value="{$profile}" class="hidden">{$profile}</button></li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            {/if}
        </div>
    </div>
{/if}

<form method="get">
    <input type="hidden" name="lid" value="{$lid}">
    <input type="hidden" name="subloc" value="{$with_sublocations}">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

<div class="card">
    <div class="card-header">
        <i class="fa fa-cube" aria-hidden="true"></i>&nbsp;
        <b>{$table_rowcount}</b> {t}Teile im Lagerort{/t} "<b>{$location_name}</b>"
    </div>
    <form method="post" action="" class="no-progbar">
        <input type="hidden" name="lid" value="{$lid}">
        <input type="hidden" name="subloc" value="{if $with_sublocations}1{else}0{/if}">
        <input type="hidden" name="table_rowcount" value="{$table_rowcount}">
        <input type="hidden" name="limit" value="{$limit}">
        <input type="hidden" name="page" value="{$page}">
        {include file='../smarty_table.tpl' table_selectable=true}
    </form>
</div>

<form method="get">
    <input type="hidden" name="lid" value="{$lid}">
    <input type="hidden" name="subloc" value="{$with_sublocations}">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl"}
</form>

{if $other_panel_position == "bottom" || $other_panel_position == "both"}
    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            <a data-toggle="collapse" class="link-collapse text-white" href="#panel-other">
                {t}Sonstiges{/t}
            </a>
        </div>
        <div class="card-body card-collapse collapse {if !$other_panel_collapse}show{/if}" id="panel-other">
            <form action="" method="post" class="form-horizontal no-progbar">
                <input type="hidden" name="lid" value="{$lid}">
                <input type="hidden" name="subloc" value="{if $with_sublocations}0{else}1{/if}">

                <div class="form-group row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-outline-secondary {if $with_sublocations}active{/if}" name="subloc_button" >{t}Unterlagerorte einblenden{/t}</button>
                    </div>
                </div>
            </form>

            {if $can_generate_barcode}
                <form action="show_part_label.php" method="get" class="form-horizontal">
                    <div class="form-group row">
                        <input type="hidden" name="label_generate">
                        <input type="hidden" name="generator" value="location">
                        <input type="hidden" name="id" value="{$lid}">

                        <div class="col-md-12">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-outline-secondary"><i class="fa fa-barcode fa-fw" aria-hidden="true"></i>
                                    {t}Barcode erzeugen{/t}</button>
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-right" id="label-dropdown">
                                    {foreach $barcode_profiles as $profile}
                                        <li><a href="#" class="link-anchor" onclick="submitFormSubmitBtn($(this).closest('form'), $('#profile_btn_{$profile|replace:" ":"_"}'));">{$profile}</a>
                                            <button type="submit" name="profile" id="profile_btn_{$profile|replace:" ":"_"}" value="{$profile}" class="hidden">{$profile}</button></li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            {/if}
        </div>
    </div>
{/if}