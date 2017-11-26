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

            </select>

            <br><br>

            <x3d id="foot3d" class="img-thumbnail" height="500" width="750" >
                <scene >
                    <!-- <Viewpoint id="front" position="0 0 10" orientation="-0.01451 0.99989 0.00319 3.15833" description="camera"></Viewpoint> -->
                    <transform>
                        <inline url="" id="foot3d-model"> </inline>
                    </transform>
                </scene>
                <!-- <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#fullscreen"><i class="fa fa-arrows-alt" aria-hidden="true"></i></button> -->
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

<script>

    var dir = "";

    function update() {
        var name = $("#models-picker").val();
        var path = "models/" + dir + "/" + name;
        $("#foot3d-model").attr("url", path);

        $("#path").text(path);
    }

    $("#models-picker").change(update);

    function node_handler(event, data) {
        dir = data.href;
        $.getJSON('api.php/1.0.0/3d_models/files/' + dir, function (list) {
            $("#models-picker").empty();
            list.forEach( function (element) {
                $("<option/>").val(element).text(element).appendTo("#models-picker");
                $('#models-picker').selectpicker('refresh');

                update();
            });
        });
    }
    
    $.getJSON('api.php/1.0.0/3d_models/dir_tree', function (tree) {
        $("#tree-footprint").treeview({ data: tree, enableLinks: false, showIcon: false
        ,showBorder: true, onNodeSelected: node_handler }).treeview('collapseAll', { silent: true });
    });
</script>