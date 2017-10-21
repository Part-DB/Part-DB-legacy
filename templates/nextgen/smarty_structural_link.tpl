{if $link != null}
    <ul class="structural_link">
        {foreach $link as $crumb}
            <li {if isset($crumb.selected) && $crumb.selected}class="active"{/if}>
                {if isset($crumb.href)}<a href="{$crumb.href}">{if isset($crumb.label)}{$crumb.label}{/if}</a>
                {else}{$crumb.label}{/if}
            </li>
        {/foreach}
    </ul>
{else}
    <div class="form-control-static">-</div>
{/if}