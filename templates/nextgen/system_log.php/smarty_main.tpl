{locale path="nextgen/locale" domain="partdb"}

<div class="card">
    <div class="card-header">
        <a data-toggle="collapse" class="link-collapse text-default" href="#panel-filter"><i class="fa fa-filter fa-fw" aria-hidden="true"></i>
            {t}Filter{/t}
        </a>
    </div>
    <div class="card-collapse collapse card-body" id="panel-filter">
        <form class="form-horizontal no-progbar" method="get">
            <div class="form-group row">
                <label class="col-md-2 col-form-label">{t}Minimales Loglevel:{/t}</label>
                <div class="col-md-10">
                    <select name="min_level" class="form-control selectpicker" data-live-search="true">
                        <option value="0" {if $min_level == 0}selected{/if}>Emergency</option>
                        <option value="1" {if $min_level == 1}selected{/if}>Alert</option>
                        <option value="2" {if $min_level == 2}selected{/if}>Critical</option>
                        <option value="3" {if $min_level == 3}selected{/if}>Error</option>
                        <option value="4" {if $min_level == 4}selected{/if}>Warning</option>
                        <option value="5" {if $min_level == 5}selected{/if}>Notice</option>
                        <option value="6" {if $min_level == 6}selected{/if}>Info</option>
                        <option value="7" {if $min_level == 7}selected{/if}>Debug</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">{t}Benutzer:{/t}</label>
                <div class="col-md-10">
                    <select name="filter_user" class="form-control selectpicker" data-live-search="true"
                            {if !$can_show_user || !$can_change_user}disabled{/if}>
                        <option value="-1">{t}Kein Filter{/t}</option>
                        <optgroup label="{t}Benutzer{/t}">
                            {$user_list nofilter}
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">{t}Ereignis:{/t}</label>
                <div class="col-md-10">
                    <select name="filter_type" class="form-control selectpicker" data-live-search="true">
                        <option value="-1">{t}Kein Filter{/t}</option>
                        <optgroup label="{t}Typ{/t}">
                            {foreach $types_loop as $type}
                                <option value="{$type.id}" {if $filter_type == {$type.id}}selected{/if}>{$type.text}</option>
                            {/foreach}
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">{t}Suche in Kommentaren:{/t}</label>
                <div class="col-md-10">
                    <input type="search" value="{$search}" name="search" class="form-control">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">{t}Zieltyp:{/t}</label>
                <div class="col-md-4">
                    <select name="target_type" class="form-control selectpicker" data-live-search="true">
                        <option value="-1">{t}Kein Filter{/t}</option>
                        <optgroup label="{t}Typ{/t}">
                            <option value="1" {if $target_type == 1}selected{/if}>{t}Benutzer{/t}</option>
                            <option value="2" {if $target_type == 2}selected{/if}>{t}Dateianhang{/t}</option>
                            <option value="3" {if $target_type == 3}selected{/if}>{t}Dateityp{/t}</option>
                            <option value="4" {if $target_type == 4}selected{/if}>{t}Kategorie{/t}</option>
                            <option value="5" {if $target_type == 5}selected{/if}>{t}Baugruppe{/t}</option>
                            <option value="6" {if $target_type == 6}selected{/if}>{t}Baugruppenteil{/t}</option>
                            <option value="7" {if $target_type == 7}selected{/if}>{t}Footprint{/t}</option>
                            <option value="8" {if $target_type == 8}selected{/if}>{t}Benutzergruppe{/t}</option>
                            <option value="9" {if $target_type == 9}selected{/if}>{t}Hersteller{/t}</option>
                            <option value="10" {if $target_type == 10}selected{/if}>{t}Bauteil{/t}</option>
                            <option value="11" {if $target_type == 11}selected{/if}>{t}Lagerort{/t}</option>
                            <option value="12" {if $target_type == 12}selected{/if}>{t}Lieferant{/t}</option>
                        </optgroup>
                    </select>
                </div>
                <label class="col-md-2 col-form-label">{t}Ziel ID:{/t}</label>
                <div class="col-md-4">
                    <input type="number" class="form-control" name="target_id" value="{if $target_id >0}{$target_id}{/if}" min="0">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">{t}Zeitraum:{/t}</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control datetimepicker-input datetime" autocomplete="off" data-default-date="{$datetime_min}" name="datetime_min" id="datetimepicker_from" data-toggle="datetimepicker" data-target="#datetimepicker_from"/>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" onclick="$('#datetimepicker_from').val('');"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
                <div class="col-form-label col-md-2">{t}bis{/t}</div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control datetimepicker-input datetime" autocomplete="off" data-default-date="{$datetime_max}" name="datetime_max" id="datetimepicker_to" data-toggle="datetimepicker" data-target="#datetimepicker_to"/>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" onclick="$('#datetimepicker_to').val('');"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="offset-md-2 col-md-10">
                    <button class="btn btn-primary">{t}Aktualisieren{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<form method="post" class="no-progbar">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl" log_delete=$can_delete_entries}
</form>

<div class="card border-primary">
    <div class="card-header bg-primary text-white"><i class="fas fa-crosshairs fa-fw"></i>
        {t}Eventlog{/t}
    </div>
    {include file="../smarty_eventlog.tpl" log_delete=$can_delete_entries}
</div>

<form method="post" class="no-progbar">
    <input type="hidden" name="page" value="1">

    {include "../smarty_pagination.tpl" log_delete=$can_delete_entries}
</form>