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
            <div class="panel-heading">{t}Kategorie:{/t} {$cat.category_name}</div>
            <div class="panel-body">
                {counter start=0 assign="count"}
                {foreach $cat.pictures_loop as $pic}
                    {if $count%4==0}
                        <div class="row">
                    {/if}
                    <div class="col-xs-3">
                        <div class="thumbnail" >
                            <img src="{$pic.filename}"  title="{$pic.title}" alt="">
                            <div class="caption"><p>{$pic.title}</p></div>
                        </div>
                    </div>
                    {counter}
                    {if $count%4==0}
                        </div>
                    {/if}
                {/foreach}

                {if $count%4>=0}
                </div>
                {/if}
        </div>

        </div>
    {/foreach}
{/if}
