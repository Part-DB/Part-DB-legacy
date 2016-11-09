<div class="panel panel-primary">
    <div class="panel-heading">
        <h4>Statistik</h4>
    </div>
    <div class="panel-body table-responsive">
        <table>
            <tr>
                <td width="300"><strong>Mit Preis erfasste Bauteile:</strong></td>
                <td>{$parts_count_with_prices}</td>
            </tr>
            <tr>
                <td width="300"><strong>Wert aller mit Preis erfassten Bauteile:</strong></td>
                <td>{$parts_count_sum_value}</td>
            </tr>
        </table>
        <br>
        <table>
            <tr><td width="300"><strong>Anzahl der verschiedenen Bauteile:</strong></td><td>{$parts_count}</td></tr>
            <tr><td width="300"><strong>Anzahl der vorhandenen Bauteile:</strong></td><td>{$parts_count_sum_instock}</td></tr>
        </table>
        <br>
        <table>
            <tr><td width="300"><strong>Anzahl der Kategorien:</strong></td><td>{$categories_count}</td></tr>
            <tr><td width="300"><strong>Anzahl der Footprints:</strong></td><td>{$footprint_count}</td></tr>
            <tr><td width="300"><strong>Anzahl der Lagerorte:</strong></td><td>{$location_count}</td></tr>
            <tr><td width="300"><strong>Anzahl der Lieferanten:</strong></td><td>{$suppliers_count}</td></tr>
            <tr><td width="300"><strong>Anzahl der Hersteller:</strong></td><td>{$manufacturers_count}</td></tr>
            <tr><td width="300"><strong>Anzahl der Baugruppen:</strong></td><td>{$devices_count}</td></tr>
            <tr><td width="300"><strong>Anzahl der Dateianhänge:</strong></td><td>{$attachements_count}</td></tr>
        </table>
        <br>
        <table>
            <tr><td width="300"><strong>Anzahl der Footprint Bilder:</strong></td><td>{$footprint_picture_count}</td></tr>
            <tr><td width="300"><strong>Anzahl der Hersteller Logos:</strong></td><td>{$iclogos_picture_count}</td></tr>
        </table>
    </div>
</div>
