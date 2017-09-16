{locale path="nextgen/locale" domain="partdb"}

{if $foot3d_active && !empty($foot3d_filename) && $foot3d_valid}
    <!-- Modal -->
    <div class="modal fade" id="fullscreen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{t}3D-Footprint{/t}</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <x3d id="foot3d" class="img-thumbnail x3d-fullscreen">
                            <scene>
                                <transform>
                                    <inline url="{$foot3d_filename}"> </inline>
                                </transform>
                            </scene>
                        </x3d>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{t}Schließen{/t}</button>
                </div>
            </div>
        </div>
    </div>
{/if}