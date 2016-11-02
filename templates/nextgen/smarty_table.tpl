{*
    This file is used to create tables for Part or DevicePart objects, or for check the import data (tools_import.php).
    Maybe later we can use it also for other objects, if this is required.
    The table columns are very flexible, you have to declare them in config.php.

    Quite important methods for creating the loop arrays are:
    - Part::build_template_table_row_array()
    - Part::build_template_table_array()
    - DevicePart::build_template_table_row_array()
    - DevicePart::build_template_table_array()
    - lib.import.php::data_array_to_template_loop()

    Please Note: There are no empty lines between the {TMPL_IF}{/if} groups, because they would produce extremely large HTML output files (because of the loops)!
*}

{foreach $table as $t}
    {if $t.print_header}   
      <thead class="thead-default">
       <tr class="trcat">
            {foreach $t.columns as $col}
                {if $col.caption=="row"}<th>Nr.</th>{/if}
                {if $col.caption=="hover_picture"}<th></th>{/if}
                {if $col.caption=="id"}<th class="idclass">ID</th>{/if}
                {if $col.caption=="row_index"}<th class="idclass">Nr.</th>{/if} {*  only for import parts  *}
                {if $col.caption=="name"}<th>Name</th>{/if}
                {if $col.caption=="name_edit"}<th>Name</th>{/if} {*  only for import parts  *}
                {if $col.caption=="description"}<th>Beschreibung</th>{/if}
                {if $col.caption=="description_edit"}<th>Beschreibung</th>{/if} {*  only for import parts  *}
                {if $col.caption=="comment"}<th>Kommentar</th>{/if}
                {if $col.caption=="comment_edit"}<th>Kommentar</th>{/if} {*  only for import parts  *}
                {if $col.caption=="name_description"}<th>Name / Beschreibung</th>{/if}
                {if $col.caption=="instock"}<th>Bestand</th>{/if}
                {if $col.caption=="instock_edit"}<th>Bestand</th>{/if} {*  only for import parts  *}
                {if $col.caption=="instock_edit_buttons"}<th>Bestand ändern</th>{/if}
                {if $col.caption=="order_quantity_edit"}<th>Bestell-<br>menge</th>{/if} {*  only for order parts  *}
                {if $col.caption=="mininstock"}<th>Mindest-<br>bestand</th>{/if}
                {if $col.caption=="mininstock_edit"}<th>Mindest-<br>bestand</th>{/if} {*  only for import parts  *}
                {if $col.caption=="instock_mininstock"}<th>Vorh./<br>Min.Best</th>{/if}
                {if $col.caption=="category"}<th>Kategorie</th>{/if}
                {if $col.caption=="category_edit"}<th>Kategorie</th>{/if} {*  only for import parts  *}
                {if !isset($col.disable_footprints)}
                    {if $col.caption=="footprint"}<th>Footprint</th>{/if}
                    {if $col.caption=="footprint_edit"}<th>Footprint</th>{/if} {*  only for import parts  *}
                {/if}
                {if !isset($col.disable_manufacturers)}
                    {if $col.caption=="manufacturer"}<th>Hersteller</th>{/if}
                    {if $col.caption=="manufacturer_edit"}<th>Hersteller</th>{/if} {*  only for import parts  *}
                {/if}
                {if $col.caption=="storelocation"}<th>Lagerort</th>{/if}
                {if $col.caption=="storelocation_edit"}<th>Lagerort</th>{/if} {*  only for import parts  *}
                {if $col.caption=="suppliers"}<th>Lieferanten</th>{/if}
                {if $col.caption=="supplier_edit"}<th>Lieferant</th>{/if} {*  only for import parts  *}
                {if $col.caption=="suppliers_radiobuttons"}<th>Lieferanten</th>{/if} {*  only for order parts  *}
                {if $col.caption=="datasheets"}{if !$row.disable_auto_datasheets}<th>Datenblätter</th>{/if}{/if}
                {if $col.caption=="button_decrement"}<th align="center">-</th>{/if}
                {if $col.caption=="button_increment"}<th align="center">+</th>{/if}
                {if $col.caption=="order_options"}<th>Optionen</th>{/if} {*  only for order parts  *}
                {if $col.caption=="quantity_edit"}<th>Anzahl</th>{/if} {*  only for device parts  *}
                {if $col.caption=="mountnames_edit"}<th>Bestückungs-<br>daten</th>{/if} {*  only for device parts  *}
                {if $col.caption=="price_edit"}<th>Preis</th>{/if} {*  only for import parts  *}
                {if $col.caption=="average_single_price"}<th>Einzel-<br>preis Ø</th>{/if}
                {if $col.caption=="single_prices"}<th>Einzel-<br>preise</th>{/if}
                {if $col.caption=="total_prices"}<th>Gesamt-<br>preise</th>{/if} {*  only for device parts  *}
                {if $col.caption=="supplier_partnrs"}<th>Bestell-<br>nummern</th>{/if}
                {if $col.caption=="supplier_partnr_edit"}<th>Bestell-<br>nummer</th>{/if} {*  only for import parts  *}
                {if $col.caption=="attachements"}<th>Dateianhänge</th>{/if}
                {if $col.caption=="systemupdate_from_version"}<th>Von Version</th>{/if}
                {if $col.caption=="systemupdate_to_version"}<th>Auf Version</th>{/if}
                {if $col.caption=="systemupdate_release_date"}<th>Veröffentlichung</th>{/if}
                {if $col.caption=="systemupdate_changelog"}<th>Changelog</th>{/if}
            {/foreach}
        </tr>
      </thead>
    {else}
      <tbody>
        <input type="hidden" name="id_{$row_index}" value="{$id}">
        {* the alternating background colors are created here *}
        <tr class="{if isset($row_odd)}trlist_odd{else}trlist_even{/if}">
        {foreach $t.row_fields as $row}
            {if $row.caption =="row"}
                {* row number *}
                <td class="tdrow1">{$row.row}</td>
            {/if}
            {if $row.caption =="hover_picture"}
                {* Pictures *}
                <td class="tdrow0">
                    {if $row.caption =="hover_picture"}
                        <a href="javascript:popUp('{$hover_picture}',
                                                    {if $row.use_modal_popup}true {else}false {/if},
                                                    {$popup_width}, {$popup_height})">
                            <img class="hoverpic" src="{$small_picture}" alt="{$picture_name}">
                        </a>
                    {else}
                        {if $row.small_picture}
                            <img class="hoverpic" src="{$small_picture}" alt="">
                        {/if}
                    {/if}
                </td>
            {/if}
            {if $row.caption =="id"}
                {* id (note: "part_not_found" is used in lib.import.php::build_deviceparts_import_template_loop() )*}
                <td class="tdrow4 idclass{if $row.part_not_found} backred{/if}">{$row.id}</td>
            {/if}
            {if $row.caption == "row_index"}
                {* row index *}
                <td class="tdrow4 idclass">{$row.row_index}</td>
            {/if}
            {if $row.caption == "name"}
                {* name/comment with link *}
                <td class="tdrow1{if $row.caption == "obsolete"} backred{/if}">
                    <a title="{if $row.caption == "obsolete"}(nicht mehr erhätlich) {/if}{if isset($row.comment)}Kommentar: {$row.comment}{/if}"
                        href="show_part_info.php?pid={$row.id}"
                        onclick="return popUp('show_part_info.php?pid={$row.id}', {if $row.use_modal_popup}true {else}false {/if},
                                {$popup_width}, {$popup_height});">{$row.name}
                    </a>
                </td>
            {/if}
            {if $row.caption == "name_edit"}
                {* name edit *}
                <td class="tdrow1"><input type="text" style="width:150px" name="name_{$row.row_index}" value="{$row.name}"></td>
            {/if}
            {if $row.caption == "description"}
                {* description *}
                <td class="tdrow1{if $row.obsolete} backred{/if}">{$row.description}</td>
            {/if}
            {if $row.caption == "description_edit"}
                {* description edit *}
                <td class="tdrow1"><input type="text" style="width:150px" name="description_{$row.row_index}" value="{$row.description}"></td>
            {/if}
            {if $row.caption == "comment"}
                {* comment *}
                <td class="tdrow1">{$row.comment}</td>
            {/if}
            {if $row.caption == "comment_edit"}
                {* comment edit *}
                <td class="tdrow1"><input type="text" style="width:150px" name="comment_{$row.row_index}" value="{$row.comment}"></td>
            {/if}
            {if $row.caption == "name_description"}
                {* name/comment/description *}
                <td class="tdrow1{if $row.obsolete} backred{/if}">
                    <a title="{if $row.obsolete}(nicht mehr erhätlich) {/if}{if $row.comment}Kommentar: {$row.comment}{/if}"
                        href="javascript:popUp('show_part_info.php?pid={$id}', {if $row.use_modal_popup}true {else}false {/if},
                                {$popup_width}, {$popup_height});">
                        {$name}{if isset($row.description)}&nbsp;{$row.description}{/if}
                    </a>
                </td>
            {/if}
            {if $row.caption == "instock"}
                {* instock *}
                <td class="tdrow2 {if $row.not_enought_instock} backred{/if}">
                    <div title="min. Bestand: {$row.mininstock}">{$row.instock}</div>
                </td>
            {/if}
            {if $row.caption == "instock_edit"}
                {* instock edit *}
                <td class="tdrow1"><input type="text" style="width:45px" name="instock_{$row.row_index}" value="{$row.instock}" onkeypress="validatePosIntNumber(event)"></td>
            {/if}
            {if $row.caption == "order_quantity_edit"}
                {* order quantity edit (only for order parts)  *}
                <td class="tdrow1" nowrap>
                    <input type="text" style="width:45px" name="order_quantity_{$row.row_index}" value="{$row.order_quantity}" onkeypress="validatePosIntNumber(event)"><br>
                    (mind. {$row.min_order_quantity})
                </td>
            {/if}
            {if $row.caption == "mininstock"}
                {* mininstock *}
                <td class="tdrow2">
                    {$row.mininstock}
                </td>
            {/if}
            {if $row.caption == "mininstock_edit"}
                {* instock edit *}
                <td class="tdrow1"><input type="text" style="width:45px" name="mininstock_{$row.row_index}" value="{$row.mininstock}" onkeypress="validatePosIntNumber(event)"></td>
            {/if}
            {if $row.caption == "instock_mininstock"}
                {* instock/mininstock *}
                <td class="tdrow2 {if $row.not_enought_instock} backred{/if}">
                    {$row.instock}/{$row.mininstock}
                </td>
            {/if}
            {if $row.caption == "category"}
                {* category *}
                <td class="tdrow1">
                    <div title="{$row.category_path}">{$row.category_name}</div>
                </td>
            {/if}
            {if $row.caption == "category_edit"}
                {* category edit *}
                <td class="tdrow1"><input type="text" style="width:100px" name="category_{$row.row_index}" value="{$row.category_name}"></td>
            {/if}
            {if !$disable_footprints}
                {if $row.caption == "footprint"}
                    {* footprint *}
                    <td class="tdrow1">
                        <div title="{$row.footprint_path}">{$row.footprint_name}</div>
                    </td>
                {/if}
                {if $row.caption == "footprint_edit"}
                    {* footprint edit (only for import parts) *}
                    <td class="tdrow1"><input type="text" style="width:100px" name="footprint_{$row.row_index}" value="{$row.footprint_name}"></td>
                {/if}
            {/if}
            {if $row.caption == "disable_manufacturers"}
                {if $row.caption == "manufacturer"}
                    {* manufacturer *}
                    <td class="tdrow1">
                        <div title="{$row.manufacturer_path}">{$row.manufacturer_name}</div>
                    </td>
                {/if}
                {if $row.caption == "manufacturer_edit"}
                    {* manufacturer edit *}
                    <td class="tdrow1"><input type="text" style="width:100px" name="manufacturer_{$row.row_index}" value="{$row.manufacturer_name}"></td>
                {/if}
            {/if}
            {if $row.caption == "storelocation"}
                {* storelocation *}
                <td class="tdrow1">
                    <div title="{$row.storelocation_path}">{$row.storelocation_name}</div>
                </td>
            {/if}
            {if $row.caption == "storelocation_edit"}
                {* storelocation edit *}
                <td class="tdrow1"><input type="text" style="width:100px" name="storelocation_{$row.row_index}" value="{$row.storelocation_name}"></td>
            {/if}
            {if $row.caption == "datasheets"}
                {if !$row.disable_auto_datasheets}
                    {* datasheet links with icons *}
                    <td class="tdrow5">
                        {foreach $row.datasheets as $sheet }
                            <a title="{$sheet.name}" href="{$sheet.url}" target="_blank"><img class="companypic" src="{$relative_path}{$sheet.image}" alt="{$sheet.name}"></a>
                        {/foreach}
                    </td>
                {/if}
            {/if}
            {if $row.caption == "button_decrement"}
                {* build the "-" button, only if more than 0 parts on stock *}
                <td class="tdrow6">
                   <input type="submit" class="btn btn-sm btn-outline-secondary" name="decrement_{$row.row_index}" value="-"{if $row.decrement_disabled}disabled="disabled"{/if}>
                </td>
            {/if}
            {if $row.caption == "button_increment"}
                {* build the "+" button *}
                <td class="tdrow7">
                    <input type="submit" class="btn btn-sm btn-outline-secondary" name="increment_{$row.row_index}" value="+">
                </td>
            {/if}
            {if $row.caption == "order_options"}
                {* build the order options (e.g. the "to stock" checkbox) (only for order parts) *}
                <td class="tdrow1" nowrap>
                    <input type="checkbox" name="tostock_{$row_index}">Einbuchen<br>
                    {if $row.enable_remove}<input type="checkbox" name="remove_{$row.row_index}">Aus Liste löschen{/if}
                </td>
            {/if}
            {if $row.caption == "quantity_edit"}
                {* quantity for DevicePart elements *}
                <td class="tdrow1" nowrap>
                    <input type="text" style="width:45px" name="quantity_{$row.row_index}" onkeypress="validatePosIntNumber(event)" value="{if isset($row.quantity)}{$row.quantity}{else}0{/if}">
                    <input type="button" value="X" onClick="elements['quantity_{$row.row_index}'].value=0">
                </td>
            {/if}
            {if $row.caption == "mountnames_edit"}
                {* mountnames for DevicePart elements *}
                <td class="tdrow1">
                    <input type="text" size="8" name="mountnames_{$row.row_index}" value="{$row.mountnames}">
                </td>
            {/if}
            {if $row.caption == "suppliers"}
                {* suppliers *}
                <td class="tdrow4" nowrap valign="top">
                    {foreach $row.suppliers as $sup}
                        <div style="display:inline-block; height:1.7em; line-height:1.7em;">
                            {$sup.supplier_name}
                        </div><br>
                    {/foreach}
                </td>
            {/if}
            {if $row.caption == "supplier_edit"}
                {* supplier edit (only for import parts) *}
                <td class="tdrow1"><input type="text" style="width:100px" name="supplier_{$row.row_index}" value="{$row.supplier_name}"></td>
            {/if}
            {if $row.caption == "suppliers_radiobuttons"}
                {* supplier-radiobuttons (only for order parts) *}
                <td class="tdrow1" nowrap valign="top">
                    {foreach $row.suppliers_radiobuttons as $radio}
                        <div style="display:inline-block; height:1.7em; line-height:1.7em;">
                            <input type="radio" name="orderdetails_{$row.row_index}" value="{$row.orderdetails_id}" {if $radio.selected}checked{/if}>{$radio.supplier_name}<br>
                        </div><br>
                    {/foreach}
                </td>
            {/if}
            {if $row.caption == "price_edit"}
                {* price edit *}
                <td class="tdrow1"><input type="text" style="width:45px" name="price_{$row.row_index}" value="{$row.price}" onkeypress="validatePosFloatNumber(event)"></td>
            {/if}
            {if $row.caption == "average_single_price"}
                {* average single price for one piece *}
                <td class="tdrow4" nowrap>
                    {$row.average_single_price}
                </td>
            {/if}
            {if $row.caption == "single_prices"}
                {* single prices *}
                <td class="tdrow4" nowrap valign="top">
                    {foreach $row.single_prices as $price}
                        <div style="display:inline-block; height:1.7em; line-height:1.7em;">
                            {$price.single_price}
                        </div><br>
                    {/foreach}
                </td>
            {/if}
            {if $row.caption == "total_prices"}
                {* total prices *}
                <td class="tdrow4" nowrap valign="top">
                    {foreach $row.total_prices as $price}
                        <div style="display:inline-block; height:1.7em; line-height:1.7em;">
                            {$price.total_price}
                        </div><br>
                    {/foreach}
                </td>
            {/if}
            {if $row.caption == "supplier_partnrs"}
                {* supplier part-nrs *}
                <td class="tdrow1" nowrap valign="top">
                    {foreach $row.supplier_partnrs as $sup}
                        <div style="display:inline-block; height:1.7em; line-height:1.7em;">
                            {if isset($sup.supplier_product_url)}
                                <a title="{$sup.supplier_product_url}" href="{$sup.supplier_product_url}" target="_blank">{$sup.supplier_partnr}</a>
                            {else}
                                {$sup.supplier_partnr}
                            {/if}
                        </div><br>
                    {/foreach}
                </td>
            {/if}
            {if $row.caption == "supplier_partnr_edit"}
                {* supplier part-nr edit *}
                <td class="tdrow1"><input type="text" style="width:120px" name="supplier_partnr_{$row.row_index}" value="{$row.supplier_partnr}"></td>
            {/if}
            {if $row.caption == "attachements"}
                {* attachements (names with hyperlinks) *}
                <td class="tdrow5">
                    {foreach $row.attachements as $attach}
                        <a title="{$attach.type}" href="{$attach.filename}" target="_blank">{$attach.name}</a><br>
                    {/foreach}
                </td>
            {/if}
            {if $row.caption == "systemupdate_from_version"}
                {* only for systemupdates *}
                <td class="tdrow1{if  $row.stable} backgreen{/if}" style="min-width:100px">
                    {$from_version}
                </td>
            {/if}
            {if $row.caption == "systemupdate_to_version"}
                {* only for systemupdates *}
                <td class="tdrow1{if $row.stable} backgreen{/if}" style="min-width:100px">
                    <b>{$to_version}</b>
                </td>
            {/if}
            {if $row.caption == "systemupdate_release_date"}
                {* only for systemupdates *}
                <td class="tdrow1{if $row.stable} backgreen{/if}" style="min-width:100px">
                    {$release_date}
                </td>
            {/if}
            {if $row.caption == "systemupdate_changelog"}
                {* only for systemupdates *}
                <td class="tdrow1{if $row.stable} backgreen{/if}" style="min-width:100px">
                    {foreach $changelog as $change}
                        {if isset($change.log_item)}&nbsp;&bull;&nbsp;{$change.log_item}<br>{/if}
                    {/foreach}
                </td>
            {/if}
        {/foreach}
        </tr>
      </tbody>
    {/if}
{/foreach}
