<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        {foreach $breadcrumb as $crumb}
            <li class="breadcrumb-item{if isset($crumb.selected) && $crumb.selected} active{/if}">
                {if isset($crumb.href)}<a href="{$crumb.href}">{if isset($crumb.label)}{$crumb.label}{/if}</a>
                {else}{$crumb.label}{/if}
            </li>
        {/foreach}
    </ol>
</nav>