{if isset($messages)}
    <div class="outer">
        <form action="" method="post">
            {foreach  from  $messages}
                {if isset($text)}
                    {if isset($strong)}<strong>{/if}
                    {if isset($color)}<font color="{$color}">{/if}
                    {$text}
                    {if isset($color)}</font>{/if}
                    {if isset($strong)}</strong>{/if}
                {/if}

                {if isset($html)}
                    {$html}
                {/if}

                {if !isset($no_linebreak)}<br>{/if}
            {/foreach}
        </form>
    </div>
{/if}


</body>
