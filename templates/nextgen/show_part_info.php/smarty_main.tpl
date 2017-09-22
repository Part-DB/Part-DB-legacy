{locale path="nextgen/locale" domain="partdb"}

<!--suppress Annotator -->
<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;
        {t}Detailinfo zu{/t} <b>"{$name}"</b>
        <div class="pull-right">
            {t}ID:{/t} {$pid}
        </div>

    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-9">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">{t}Name:{/t}</label>
                        <div class="col-sm-9">
                            {if !empty($manufacturer_product_url)}
                                <a class="form-control-link  hidden-print-href" title="{$manufacturer_product_url}" href="{$manufacturer_product_url}">{$name}</a>
                            {else}
                                <p class="form-control-static">{$name}</p>
                            {/if}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{t}Beschreibung:{/t}</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                {if isset($description)}{$description nofilter}{else}-{/if}
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{t}Vorhanden:{/t}</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">{$instock}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{t}Min. Bestand:{/t}</label>
                        <div class="col-sm-9"><p class="form-control-static">{$mininstock}</p></div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{t}Kategorie:{/t}</label>
                        <div class="col-sm-9"><a href="show_category_parts.php?cid={$category_id}" class="form-control-link hidden-print-href">{$category_full_path}</a></div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{t}Lagerort:{/t}</label>
                        <div class="col-sm-9">
                            {if $storelocation_id == 0}
                                <p class="form-control-static">{$storelocation_full_path}{if $storelocation_is_full} [voll]{/if}</p>
                            {else}
                                <a class="form-control-link hidden-print-href" href="show_location_parts.php?lid={$storelocation_id}&subloc=0">{$storelocation_full_path}{if $storelocation_is_full} [voll]{/if}</a>
                            {/if}

                        </div>
                    </div>

                    {if !$disable_manufacturers}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{t}Hersteller:{/t}</label>
                            <div class="col-sm-9">
                                {if $manufacturer_id == 0}
                                    <p class="form-control-static">{$manufacturer_full_path}</p>
                                {else}
                                    <a class="form-control-link  hidden-print-href" href="show_manufacturer_parts.php?mid={$manufacturer_id}&subman=0">{$manufacturer_full_path}</a>
                                {/if}
                            </div>
                        </div>
                    {/if}

                    {if !$disable_footprints}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{t}Footprint:{/t}</label>
                            <div class="col-sm-9">
                                {if $footprint_id == 0}
                                    <p class="form-control-static">{$footprint_full_path}</p>
                                {else}
                                    <a class="form-control-link hidden-print-href" href="show_footprint_parts.php?fid={$footprint_id}&subfoot=0">{$footprint_full_path}</a>
                                {/if}
                            </div>
                        </div>

                        {if !empty($footprint_filename) && $footprint_valid}
                            <div class="form-group">
                                <div class="col-sm-9 col-md-offset-3">
                                    <img align="middle" rel="popover" src="{$footprint_filename}" alt="" height="70">
                                </div>
                            </div>
                        {/if}

                        {if $foot3d_active && !empty($foot3d_filename) && $foot3d_valid}
                            <div class="form-group">
                                <div class="col-sm-9 col-md-offset-3">
                                    <x3d id="foot3d" class="img-thumbnail" height="150" width="500" >
                                        <scene >
                                            <!-- <Viewpoint id="front" position="0 0 10" orientation="-0.01451 0.99989 0.00319 3.15833" description="camera"></Viewpoint> -->
                                            <transform>
                                                <inline url="{$foot3d_filename}"> </inline>
                                            </transform>
                                        </scene>
                                        <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#fullscreen"><i class="fa fa-arrows-alt" aria-hidden="true"></i></button>
                                    </x3d>
                                </div>
                            </div>
                        {/if}

                    {/if}

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{t}Kommentar:{/t}</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">{if !empty($comment)}{$comment nofilter}{else}-{/if}</p>
                        </div>
                    </div>

                    <div class="form-group hidden-print">
                        <div class="col-sm-9 col-sm-offset-3">
                            <a class="btn btn-primary" href="edit_part_info.php?pid={$pid}" {if !$can_edit}disabled="disabled"{/if}>
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i> {t}Angaben verändern{/t}</a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-3">
                <form action="" method="post" class="hidden-print no-progbar">
                    <input type="hidden" name="pid" value="{$pid}">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="n_less">{t}Teile entnehmen:{/t}</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="n_less" min="0" max="999" value="1" placeholder="Anzahl" {if !$can_instock}disabled{/if}>
                                <span class="input-group-btn">
                                            <button type="submit" class="btn btn-default" name="dec" {if !$can_instock}disabled{/if}>{t}Entnehmen{/t}</button>
                                        </span>
                            </div>
                        </div>
                    </div>
                </form>

                <p></p>

                <form action="" method="post" class="hidden-print no-progbar">
                    <input type="hidden" name="pid" value="{$pid}">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="n_more">{t}Teile hinzufügen{/t}</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="n_more" min="0" max="999" value="1" {if !$can_instock}disabled{/if}>
                                <span class="input-group-btn">
                                            <button type="submit" class="btn btn-default" name="inc" {if !$can_instock}disabled{/if}>{t}Hinzufügen{/t}</button>
                                        </span>
                            </div>
                        </div>
                    </div>
                </form>

                <p></p>

                {if $can_order_read}
                <form action="" method="post" class="hidden-print no-progbar">
                    <input type="hidden" name="pid" value="{$pid}">
                    <div class="row">
                        <div class="col-md-12">
                            {if $manual_order_exists}
                                <label for="remove_mark_to_order">{t}Bauteil wurde manuell zum Bestellen vorgemerkt.{/t}</label>
                                <button type="submit" class="btn btn-default"
                                        name="remove_mark_to_order" {if !$can_order_read}disabled{/if}>
                                    {t}Aufheben{/t}</button>
                            {else}
                                {if $auto_order_exists}
                                    <i>{t}Das Bauteil wird unter "Zu bestellende Teile"aufgelistet, da der Bestand kleiner als der Mindestbestand ist.{/t}</i>
                                {else}
                                    <label for="order_quantity">{t}Zum Bestellen vormerken:{/t}</label>
                                    <div class="input-group">
                                        <input type="number" min="0" max="999" class="form-control" value="1" name="order_quantity"
                                               placeholder="Bestellmenge" {if !$can_order_edit}disabled{/if}><br>
                                        <span class="input-group-btn">
                                                    <button type="submit" class="btn btn-default"
                                                            name="mark_to_order" {if !$can_order_edit}disabled{/if}>{t}Übernehmen{/t}</button>
                                                </span>
                                    </div>
                                {/if}
                            {/if}
                        </div>
                    </div>
                </form>
                {/if}

                <p></p>

                <div class="form-group">
                    <label>{t}Hinzugefügt:{/t}</label>
                    <p>{$datetime_added}</p>
                </div>

                <p></p>

                <div class="form-group">
                    <label>{t}Letzte Änderung:{/t}</label>
                    <p>{$last_modified}</p>
                </div>

                <p></p>

                <form action="show_part_label.php" class="hidden-print">
                    <input type="hidden" name="pid" value="{$pid}">
                    <input type="hidden" name="label_generate">
                    <button type="submit" class="btn btn-default btn-block"><i class="fa fa-barcode" aria-hidden="true"></i>
                        {t}Barcode erzeugen{/t}</button>
                </form>

                <p></p>

                <div class="form-group hidden-print">
                    <button type="button" class="btn btn-default btn-block" onclick="window.print();"><i class="fa fa-print" aria-hidden="true"></i>
                        {t}Übersicht drucken{/t}
                    </button>
                </div>


            </div>
        </div>
    </div>
</div>

