{locale path="nextgen/locale" domain="partdb"}

<div class="panel panel-primary">
    <div class="panel-heading"><i class="fas fa-chart-bar" aria-hidden="true"></i>
        {t}Statistik{/t}
    </div>
    <div class="panel-body table-responsive form-horizontal">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#home" data-toggle="tab" class="link-anchor">{t}Übersicht{/t}</a></li>
            <li><a href="#graph_categories" data-toggle="tab" class="link-anchor">{t}Meistbenutzte Kategorien{/t}</a></li>
            <li><a href="#graph_locations" data-toggle="tab" class="link-anchor">{t}Meistbenutzte Lagerorte{/t}</a></li>
            <li><a href="#graph_footprints" data-toggle="tab" class="link-anchor">{t}Meistbenutzte Footprints{/t}</a></li>
            <li><a href="#graph_manufacturers" data-toggle="tab" class="link-anchor">{t}Meistbenutzte Hersteller{/t}</a></li>
        </ul>

        <br>

        <div class="tab-content">

            <div id="home" class="tab-pane fade in active">
                <div class="form-horizontal">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Mit Preis erfasste Bauteile:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$parts_count_with_prices}</p>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Wert aller mit Preis erfassten Bauteile:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$parts_count_sum_value}</p>
                        </div>
                    </div>

                    <br>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Anzahl der verschiedenen Bauteile:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$parts_count}</p>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Anzahl der vorhandenen Bauteile:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$parts_count_sum_instock}</p>
                        </div>
                    </div>

                    <br>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Anzahl der Kategorien:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$categories_count}</p>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Anzahl der Footprints:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$footprint_count}</p>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Anzahl der Lagerorte:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$location_count}</p>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Anzahl der Lieferanten:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$suppliers_count}</p>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Anzahl der Hersteller:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$manufacturers_count}</p>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Anzahl der Baugruppen:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$devices_count}</p>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Anzahl der Dateianhänge:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$attachements_count}</p>
                        </div>
                    </div>

                    <br>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Anzahl der Footprint Bilder:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$footprint_picture_count}</p>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Anzahl der Footprint 3D Modelle:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$footprint_models_count}</p>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="col-md-4 control-label">{t}Anzahl der Hersteller Logos:{/t}</label>
                        <div class="col-md-8">
                            <p class="form-control-static">{$iclogos_picture_count}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="graph_categories" class="tab-pane fade">
                <canvas class="chart" data-type="bar" data-data='{$graph_categories}' width="100" height="50"></canvas>
            </div>

            <div id="graph_locations" class="tab-pane fade">
                <canvas class="chart" data-type="bar" data-data='{$graph_locations}' width="100" height="50"></canvas>
            </div>

            <div id="graph_footprints" class="tab-pane fade">
                <canvas class="chart" data-type="bar" data-data='{$graph_footprints}' width="100" height="50"></canvas>
            </div>

            <div id="graph_manufacturers" class="tab-pane fade">
                <canvas class="chart" data-type="bar" data-data='{$graph_manufacturer}' width="100" height="50"></canvas>
            </div>
        </div>
    </div>
</div>