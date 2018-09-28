{locale path="nextgen/locale" domain="partdb"}

{if isset($refresh_navigation_frame)}
    <script type="text/javascript">
        location.reload();
    </script>
{/if}

<div class="card border-primary">
    <div class="card-header bg-primary text-white"><i class="fa fa-cogs" aria-hidden="true"></i>
        {t}Benutzereinstellungen{/t}</div>
    <div class="card-body">
        <form class="form form-horizontal" method="post" class="no-progbar">
            <ul class="nav nav-tabs">
                <li class="nav-item"><a class="link-anchor active nav-link" data-toggle="tab" href="#tab1">{t}Persönliche Daten{/t}</a></li>
                <li class="nav-item"><a data-toggle="tab" class="link-anchor nav-link" href="#tab2">{t}Konfiguration{/t}</a></li>
            </ul>

            <div class="tab-content">

                <br>

                <div id="tab1" class="tab-pane fade show active">

                    <div class="form-group row">
                        <label class="col-form-label col-md-3">{t}Benutzername:{/t}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="{$username}" name="username"
                                   placeholder="{t}z.B. m.muster{/t}" required {if !$can_username}disabled{/if}>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-md-3">{t}Vorname:{/t}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="{$firstname}"  name="firstname"
                                   placeholder="{t}z.B. Max{/t}" {if !$can_infos}disabled{/if}>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-md-3">{t}Nachname:{/t}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="{$lastname}" name="lastname"
                                   placeholder="{t}z.B. Muster{/t}" {if !$can_infos}disabled{/if}>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-md-3">{t}Email:{/t}</label>
                        <div class="col-md-9">
                            <input type="email" class="form-control" value="{$email}"  name="email"
                                   placeholder="{t}z.B. m.muster@ecorp.com{/t}" {if !$can_infos}disabled{/if}>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-md-3">{t}Abteilung:{/t}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="{$department}"  name="department"
                                   placeholder="{t}z.B. Entwicklung{/t}" {if !$can_infos}disabled{/if}>
                        </div>
                    </div>

                </div>

                <div id="tab2" class="tab-pane fade">
                    <div class="form-group row">
                        <label class="col-form-label col-md-3" for="custom_css">{t}Theme:{/t}</label>
                        <div class="col-md-9">
                            <select class="form-control" name="custom_css">
                                <option value="">{t}Benutze das serverweite Theme{/t}</option>
                                {foreach $custom_css_loop as $css}
                                    <option value="{$css.value}" {if $css.selected}selected{/if}>{$css.text}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-md-3" for="timezon">{t}Zeitzone:{/t}</label>
                        <div class="col-sm-9">
                            <select class="form-control selectpicker" data-live-search="true" name="timezone">
                                <option value="">{t}Benutze die serverweite Zeitzone{/t}</option>
                                {foreach $timezone_loop as $timezone}
                                    <option value="{$timezone.value}" {if $timezone.selected}selected{/if}>{$timezone.text}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-md-3" for="language">{t}Sprache:{/t}</label>
                        <div class="col-md-9">
                            <select class="form-control" name="language">
                                <option value="">{t}Benutze die serverweite Sprache{/t}</option>
                                {foreach $language_loop as $lang}
                                    <option value="{$lang.value}" {if $lang.selected}selected{/if}>{$lang.text}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-9 offset-md-3 mt-2">
                        <button class="btn btn-primary" type="submit" name="apply_settings" {if !$can_infos && !$can_username}disabled{/if}>
                            {t}Änderungen übernehmen{/t}</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>