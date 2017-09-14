{locale path="nextgen/locale" domain="partdb"}

{if !empty($properties_loop)}
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-table" aria-hidden="true"></i>
            {t}Bauteileeigenschaften{/t}
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sortable">
                <thead>
                <tr>
                    <th class="no-sort"></th>
                    <th>Eigenschaft</th>
                    <th>Wert</th>

                </tr>
                </thead>
                <tbody>
                {foreach $properties_loop as $property}
                    <tr>
                        <td></td>
                        <td><strong>{$property['name']}</strong></td>
                        <td>{$property['value']}</td>

                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/if}