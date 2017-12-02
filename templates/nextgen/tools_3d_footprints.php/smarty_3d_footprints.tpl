<div class="panel panel-primary">
    <div class="panel-heading">{t}3D Footprints{/t}</div>
    <div class="panel-body">
        <div class="col-md-4" id="dir_select">
                <div class="dropdown">
                    <button class="btn-text dropdown-toggle" type="button" id="dropdownCat" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <div class="sidebar-title">{t}Verzeichnis{/t}
                            <span class="caret"></span></div>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownCat">
                        <li><a href="#" class="tree-btns" data-mode="expand" data-target="tree-categories">{t}Alle ausklappen{/t}</a></li>
                        <li><a href="#" class="tree-btns" data-mode="collapse" data-target="tree-categories">{t}Alle einklappen{/t}</a></li>
                    </ul>
                </div>
                <div id="tree-footprint"></div>
        </div>

        <div class="col-md-8">
            <select class="form-control selectpicker" data-live-search="true" id="models-picker">
                {* This is filled dynamically by JQuery *}
            </select>

            <br><br>

            <x3d id="foot3d" class="img-thumbnail" height="500" width="750" >
                <scene >
                    <!-- <Viewpoint id="front" position="0 0 10" orientation="-0.01451 0.99989 0.00319 3.15833" description="camera"></Viewpoint> -->
                    <transform>
                        <inline url="" id="foot3d-model"> </inline>
                    </transform>
                </scene>
                <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#fullscreen"><i class="fa fa-arrows-alt" aria-hidden="true"></i></button>
            </x3d>

            <br><br>

            <div class="form-horizontal">
                <div class="form-group">
                    <label class="control-label col-md-2">{t}Dateipfad:{/t}</label>
                    <div class="col-md-10">
                        <p class="form-control-static" id="path"></p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

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
                                <inline id="foot3d-model2"> </inline>
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