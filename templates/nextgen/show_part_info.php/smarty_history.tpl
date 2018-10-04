{locale path="nextgen/locale" domain="partdb"}

<div class="card d-print-none mt-3">
    <div class="card-header">
        <a data-toggle="collapse" class="link-collapse text-default" href="#panel-history"><i class="fas fa-history fa-fw" aria-hidden="true"></i>
            {t}Historie{/t}</a>
    </div>
    <div class="card-collapse collapse" id="panel-history">
        {include "../smarty_history.tpl" history=$history}
    </div>
</div>
