{locale path="nextgen/locale" domain="partdb"}

<div class="card d-print-none mt-3">
    <div class="card-header">
        <a data-toggle="collapse" class="link-collapse text-default" href="#panel-history"><i class="fas fa-history fa-fw" aria-hidden="true"></i>
            {t}Historie{/t}</a>
    </div>
    <div class="card-collapse collapse" id="panel-history">
        {include "../smarty_history.tpl" history=$history}
        <div class="card-body p-0">
            <form method="get">
                <input type="hidden" name="lid" value="{$lid}">
                <input type="hidden" name="page" value="1">

                {include "../smarty_pagination.tpl"}
            </form>
        </div>
    </div>
</div>
