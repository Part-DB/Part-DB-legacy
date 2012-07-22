<?php
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA

    $Id: startup.php 446 2012-06-16 06:21:08Z bubbles.red@gmail.com $

*/

    require_once ('db_update.php');
    require_once ('authors.php');
    require_once ('lib.php');

    $tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
    $tmpl -> setVar('head_title', $title);
    $tmpl -> setVar('head_charset', $http_charset);
    $tmpl -> setVar('head_theme', $theme);
    $tmpl -> setVar('head_css', $css);
    $tmpl -> setVar('head_menu', true);
    $tmpl -> pparse();

    // catch output to do fine formating later
    if ( checkDBUpdateNeeded())
    {
        $tmpl -> setVar('db_version', getDBVersion());
        if ( getDBAutomaticUpdateActive())
        {
            doDBUpdate();
        }
        else
        {
            $tmpl -> setVar('disabled_autoupdate', true);
        }
    }

    /*
     * This variable determines wheater the user is reminded to add
     * add least one loc, one footprint, one category and one supplier.
     */
    $display_warning = false;
    // predefines
    $good = "&#x2714; ";
    $bad  = "&#x2718; ";

    $tmpl = new vlibTemplate(BASE."/templates/$theme/startup.php/vlib_startup.tmpl");
    $tmpl -> setVar('startup_title', $startup_title);
    $tmpl -> setVar('get_svn_revision', get_svn_revision());

    if (categories_count() == 0 || location_count() == 0 || footprint_count() == 0 || suppliers_count() == 0)
    {
        $tmpl -> setVar('display_warning', true);
        $tmpl -> setVar('missing_category', ((categories_count()==0)?$bad:$good));
        $tmpl -> setVar('missing_storeloc', ((location_count()==0)?$bad:$good));
        $tmpl -> setVar('missing_footprint', ((footprint_count()==0)?$bad:$good));
        $tmpl -> setVar('missing_supplier', ((suppliers_count()==0)?$bad:$good));
    }
    $tmpl -> setVar('database_update', $database_update);
    $tmpl -> setVar('banner', $banner);

    if (! $disable_update_list)
    {
        $rss_file   = join ( ' ', file ("http://code.google.com/feeds/p/part-db/downloads/basic"));
        $rss_zeilen = array ( "title", "updated", "id" );
        $rss_array  = explode ( "<entry>", $rss_file );

        // show only the last actual versions
        $count = 4;
        $rss_text = array();
        foreach ( $rss_array as $string )
        {
            // show all lines from rss feed
            foreach ( $rss_zeilen as $zeile )
            {
                // find tags
                preg_match_all( "|<$zeile>(.*)</$zeile>|Usim", $string, $preg_match);
                $$zeile = $preg_match [1] [0];
                // make clickable if http url
                $$zeile = preg_replace('`((?:http)://\S+[[:alnum:]]/?)`si', '<a href="\\1">\\1</a>', $$zeile);
                $rss_text[]['zeile'] = $$zeile;
            }
            if (!(--$count)) break;
        }
        $tmpl -> setLoop('update_list', $rss_text);
    }
    $tmpl -> setLoop('authors', $authors);
    $tmpl -> pparse();

    $tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_foot.tmpl");
    $tmpl -> pparse();
?>
