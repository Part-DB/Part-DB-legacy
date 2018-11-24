<!--suppress Annotator -->

{locale path="nextgen/locale" domain="partdb"}
<div class="card border-primary">
    <div class="card-header bg-primary text-white"><i class="fas fa-shoe-prints fa-fw"></i> {t}Kategorie wählen{/t}</div>
    <div class="card-body">
        <form action="" method="post" class="no-progbar">
            <input class="btn btn-outline-secondary mb-2 {if $action=="show_all"}active{/if}" type="submit" name="show_all" value="{t}Alle{/t}">
            <input class="btn btn-outline-secondary mb-2 {if $action=="show_active"}active{/if}" type="submit" name="show_active" value="{t}Aktive Bauelemente{/t}">
            <input class="btn btn-outline-secondary mb-2 {if $action=="show_passive"}active{/if}" type="submit" name="show_passive" value="{t}Passive Bauelemente{/t}">
            <input class="btn btn-outline-secondary mb-2{if $action=="show_electromechanic"}active{/if}" type="submit" name="show_electromechanic" value="{t}Elektromechanische Bauteile{/t}">
            <input class="btn btn-outline-secondary mb-2 {if $action=="show_others"}active{/if}" type="submit" name="show_others" value="{t}Akustik, Optik, Sonstiges{/t}">
        </form>
    </div>
</div>

{if isset($categories_loop)}
    {foreach $categories_loop as $cat}
        <div class="card mt-3">
            <div class="card-header">{t}Kategorie:{/t} <b>{$cat.category_name}</b></div>
            <div class="card-body">
                <div class="card-deck">
                    {foreach from=$cat.pictures_loop  item=$pic key=$n}
                        <div class="card mb-4" style="max-width: 15rem;">
                            <div class="card-header">{$pic.title}</div>
                            <img class="card-img-top img-fluid" src="{$pic.filename}" alt="">
                            <div class="card-body">
                                <a class="link-external card-link" href="{$pic.filename}" rel="noopener" target="_blank">{t}Öffnen{/t}</a>
                                {*
                                <a class="link-external" href="{$pic.filename}" rel="noopener" target="_blank">

                                </a>
                                <span class="caption text-break">{$pic.title}</span> *}
                            </div>
                        </div>
                        {if $n % 2 == 1}
                            <div class="w-100 d-none d-sm-block d-md-none"></div>
                        {/if}
                        {if $n % 3 == 2}
                            <div class="w-100 d-none d-md-block d-lg-none"><!-- wrap every 3 on md--></div>
                        {/if}
                        {if $n % 4 == 3}
                            <div class="w-100 d-none d-lg-block d-xl-none"><!-- wrap every 4 on lg--></div>
                        {/if}
                        {if $n % 5 == 4}
                            <div class="w-100 d-none d-xl-block"></div>
                        {/if}
                    {/foreach}

                </div>
            </div>
        </div>
    {/foreach}
{/if}

