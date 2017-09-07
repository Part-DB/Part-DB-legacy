<!--suppress Annotator -->

{locale path="nextgen/locale" domain="partdb"}
<div class="panel panel-primary">
    <div class="panel-heading">{t}Kategorie wählen{/t}</div>
    <div class="panel-body">
        <form action="" method="post" class="no-progbar">
            <input class="btn btn-default {if $action=="show_all"}active{/if}" type="submit" name="show_all" value="{t}Alle{/t}">
            <input class="btn btn-default {if $action=="show_active"}active{/if}" type="submit" name="show_active" value="{t}Aktive Bauelemente{/t}">
            <input class="btn btn-default {if $action=="show_passive"}active{/if}" type="submit" name="show_passive" value="{t}Passive Bauelemente{/t}">
            <input class="btn btn-default {if $action=="show_electromechanic"}active{/if}" type="submit" name="show_electromechanic" value="{t}Elektromechanische Bauteile{/t}">
            <input class="btn btn-default {if $action=="show_others"}active{/if}" type="submit" name="show_others" value="{t}Akustik, Optik, Sonstiges{/t}">
        </form>
    </div>
</div>

{if isset($categories_loop)}
    {foreach $categories_loop as $cat}
        <div class="panel panel-default">
            <div class="panel-heading">{t}Kategorie:{/t} <b>{$cat.category_name}</b></div>
            <div class="panel-body">
                {foreach $cat.pictures_loop as $pic}
                    <div class="col-lg-2 col-md-3 col-xs-4">
                        <div class="thumbnail">
                            <a class="link-external" href="{$pic.filename}" target="_blank">
                                <img class="img-responsive" src="{$pic.filename}" alt="">
                            </a>
                            <span class="caption text-break">{$pic.title}</span>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    {/foreach}
{/if}

