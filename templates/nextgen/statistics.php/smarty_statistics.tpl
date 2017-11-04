{locale path="nextgen/locale" domain="partdb"}

<div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-bar-chart" aria-hidden="true"></i>
        {t}Statistik{/t}
    </div>
    <div class="panel-body table-responsive form-horizontal">

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
