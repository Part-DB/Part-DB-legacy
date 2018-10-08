{locale path="nextgen/locale" domain="partdb"}

<div class="card d-print-none mt-3">
    <div class="card-header">
        <a data-toggle="collapse" class="link-collapse text-default" href="#panel-history"><i class="fas fa-history fa-fw" aria-hidden="true"></i>
            {t}Historie{/t}</a>
    </div>
    <div class="card-collapse collapse show" id="panel-history">
        <ul class="nav nav-tabs mt-1 ml-1">
            <li class="nav-item">
                <a class="nav-link link-anchor active" data-toggle="tab" href="#table" role="tab"><i class="fas fa-table fa-fw"></i> {t}Tabelle{/t}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link link-anchor" data-toggle="tab" href="#graph" role="tab"><i class="fas fa-chart-line fa-fw"></i> {t}Graph{/t}</a>
            </li>
        </ul>
        <div class="tab-content " id="myTabContent">
            <div class="tab-pane fade show active" id="table" role="tabpanel">
                {include "../smarty_history.tpl" history=$history}
            </div>

            <div class="tab-pane fade " id="graph" role="tabpanel">
                <div class="chart-container" style="position: relative;">
                    <canvas id="historyGraph" class="historychart" data-type="line" data-data='{$graph_history}' height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="card-body p-0 pr-2">
            <form method="get">
                <input type="hidden" name="pid" value="{$pid}">
                <input type="hidden" name="page" value="1">

                {include "../smarty_pagination.tpl"}
            </form>
        </div>
    </div>
</div>
