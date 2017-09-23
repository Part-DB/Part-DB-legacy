{locale path="nextgen/locale" domain="partdb"}

<div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-bar-chart" aria-hidden="true"></i>
        {t}Statistik{/t}
    </div>
    <div class="panel-body table-responsive">
        <table>
            <tr>
                <td width="300"><strong>{t}Mit Preis erfasste Bauteile:{/t}</strong></td>
                <td>{$parts_count_with_prices}</td>
            </tr>
            <tr>
                <td width="300"><strong>{t}Wert aller mit Preis erfassten Bauteile:{/t}</strong></td>
                <td>{$parts_count_sum_value}</td>
            </tr>
        </table>
        <br>
        <table>
            <tr><td width="300"><strong>{t}Anzahl der verschiedenen Bauteile:{/t}</strong></td><td>{$parts_count}</td></tr>
            <tr><td width="300"><strong>{t}Anzahl der vorhandenen Bauteile:{/t}</strong></td><td>{$parts_count_sum_instock}</td></tr>
        </table>
        <br>
        <table>
            <tr><td width="300"><strong>{t}Anzahl der Kategorien:{/t}</strong></td><td>{$categories_count}</td></tr>
            <tr><td width="300"><strong>{t}Anzahl der Footprints:{/t}</strong></td><td>{$footprint_count}</td></tr>
            <tr><td width="300"><strong>{t}Anzahl der Lagerorte:{/t}</strong></td><td>{$location_count}</td></tr>
            <tr><td width="300"><strong>{t}Anzahl der Lieferanten:{/t}</strong></td><td>{$suppliers_count}</td></tr>
            <tr><td width="300"><strong>{t}Anzahl der Hersteller:{/t}</strong></td><td>{$manufacturers_count}</td></tr>
            <tr><td width="300"><strong>{t}Anzahl der Baugruppen:{/t}</strong></td><td>{$devices_count}</td></tr>
            <tr><td width="300"><strong>{t}Anzahl der Dateianhänge:{/t}</strong></td><td>{$attachements_count}</td></tr>
        </table>
        <br>
        <table>
            <tr><td width="300"><strong>{t}Anzahl der Footprint Bilder:{/t}</strong></td><td>{$footprint_picture_count}</td></tr>
            <tr><td width="300"><strong>{t}Anzahl der Hersteller Logos:{/t}</strong></td><td>{$iclogos_picture_count}</td></tr>
        </table>
    </div>
</div>
