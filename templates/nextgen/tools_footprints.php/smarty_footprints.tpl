<div class="panel panel-body">
    <div class="panel-heading"><h4>{t}Kategorie wählen{/t}</h4></div>
    <div class="panel-body">
        <form action="" method="post">
            <input type="submit" name="show_all" value="Alle">
            <input type="submit" name="show_active" value="Aktive Bauelemente">
            <input type="submit" name="show_passive" value="Passive Bauelemente">
            <input type="submit" name="show_electromechanic" value="Elektromechanische Bauteile">
            <input type="submit" name="show_others" value="Akustik, Optik, Sonstiges">
        </form>
    </div>
</div>

{if isset($categories_loop)}
    {foreach $categories_loop as $cat}
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Kategorie: {$cat.category_name}</h4></div>
            <div class="panel-body">
                {foreach $cat.pictures_loop as $pic}
                    <div class="">
                        <img src="{$pic.filename}" title="{$pic.title}" alt="">
                        <div class="caption"><p>{$pic.title}</p></div>
                    </div>
                {/foreach}
            
            </div>
            
        </div>
    {/foreach}
{/if}
