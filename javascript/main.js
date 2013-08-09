/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

    part-db version 0.2+
    Copyright (C) 2009 K. Jacobs and others (see authors.php)
    http://code.google.com/p/part-db/

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

    $Id: main.js 698 2013-08-09 09:36:55Z weinbauer@singollo.de $

    Changelog (sorted by date):
        [DATE]      [NICKNAME]          [CHANGES]
        2013-08-09  weinbauer73         - first version

*/


addJavascript('dom','head');
addJavascript('ajax','head');

addJavascript('util-functions','head');
addJavascript('calculator','head');
addJavascript('clear-default-text','head');
addJavascript('dtree','head');
addJavascript('popup','head');
addJavascript('toggle','head');
addJavascript('validatenumber','head');

window.onload = function()
{
    /* load content */
    loadContent('navigation.php','menu');
    loadContent('startup.php','content');
    /* resize content */
    resize();
    setTimeout('navigation()',250);
}

window.onresize=function() {
    /* resize content */
    resize()
};

function path()
{
    /* path of installation */
    var path = window.location.pathname;
    path = path.substring(0,path.lastIndexOf("/")+1);
    return path;
}

function loadContent (file,element)
{
    /* load file */
    var data = {
        method:'GET',
        url:path()+file,
        load:function(response,status) {dom.setContent(element,response);},
        error:function(response,status) {dom.setContent(element,"Fehler: "+status);}
    };
    ajax.xhr(data);
}

function addJavascript(jsname,pos)
{
    var th = document.getElementsByTagName(pos)[0], s = document.createElement('script');
    s.setAttribute('type','text/javascript');
    s.setAttribute('charset','utf-8');
    s.setAttribute('src',path()+'javascript/'+jsname+'.js');
    th.appendChild(s);
}

function startupCalculator () {
    /* startup calculator */
    reset4ring();
    reset6ring();
    ratio_reset();
    resistor_reset();
}

function navigation()
{
    menue = new dTree('menue');
    menue.add(0,-1,'');

    menue.add(1,0,'Tools','','','');
    menue.add(2,1,'Import',path()+'tools_import.php','','content');
    
    if (dom.getAttribute('navigation','labels')==='true') menue.add(3,1,'Labels',path()+'tools_labels.php','','content');
    if (dom.getAttribute('navigation','calculator')==='true') menue.add(4,1,'Widerstandsrechner',path()+'tools_calculator.php','','content');
    if (dom.getAttribute('navigation','footprints')==='true' && dom.getAttribute('navigation','footprints_tools')==='true') menue.add(5,1,'Footprints',path()+'tools_footprints.php','','content');
    if (dom.getAttribute('navigation','iclogos')==='true') menue.add(6,1,'IC-Logos',path()+'tools_iclogos.php','','content');

    menue.add(20,0,'Zeige','','','');
    menue.add(21,20,'Zu bestellende Teile',path()+'show_order_parts.php','','content');
    menue.add(22,20,'Teile ohne Preis',path()+'show_noprice_parts.php','','content');
    menue.add(23,20,'Nicht mehr erhältliche Teile',path()+'show_obsolete_parts.php','','content');
    menue.add(24,20,'Statistik',path()+'statistics.php','','content');
    menue.add(40,0,'Bearbeiten','','','');
    
    if (dom.getAttribute('navigation','devices')==='true') menue.add(41,40,'Baugruppen',path()+'edit_devices.php','','content');
 
    menue.add(42,40,'Lagerorte',path()+'edit_storelocations.php','','content');
    
    if (dom.getAttribute('navigation','footprints')==='true' && dom.getAttribute('navigation','footprints_edit')==='true') menue.add(43,40,'Footprints',path()+'edit_footprints.php','','content');

    menue.add(44,40,'Kategorien',path()+'edit_categories.php','','content');
    menue.add(45,40,'Lieferanten',path()+'edit_suppliers.php','','content');

    if (dom.getAttribute('navigation','manufacturers')==='true') menue.add(46,40,'Hersteller',path()+'edit_manufacturers.php','','content');

    menue.add(47,40,'Dateitypen',path()+'edit_attachement_types.php','','content');

    if (dom.getAttribute('navigation','config')==='true')
    {
        menue.add(60,0,'System','','','');
        menue.add(61,60,'Konfiguration',path()+'system_config.php','','content');
        /* menue.add(62,60,'Updates',path()+'system_updates.php','','content'); */
        menue.add(63,60,'Datenbank',path()+'system_database.php','','content');
        if (dom.getAttribute('navigation','db_backup_url')==='true') menue.add(64,60,'{TMPL_VAR NAME="db_backup_name"}','{TMPL_VAR NAME="db_backup_url"}','','__new');
        if (dom.getAttribute('navigation','enable_debug_link')==='true') menue.add(65,60,'Debugging',path()+'system_debug.php','','content');
    }

    if (dom.getAttribute('navigation','developer_mode')==='true')
    {
        menue.add(70,0,'Entwickler-Werkzeuge','','','');
        menue.add(71,70,'Werkzeuge',path()+'development/developer_tools.php','','content');
        menue.add(72,70,'Debugging',path()+'system_debug.php','','content');
        menue.add(73,70,'Sandkasten',path()+'development/sandbox.php','','content');
        menue.add(74,70,'Quellcode-Dokumentation',path()+'development/doxygen/html/index.html','','__new');
    }

    if (dom.getAttribute('navigation','help')==='true')
    {
        /* menue.add(80,0,'Hilfe','','',''); */
        /* menue.add(81,80,'Über Part-DB',path()+'documentation/about.php','','content'); */
        /* menue.add(82,80,'Dokumentation',path()+'documentation/help/help.php','','content'); */
        menue.add(80,0,'Hilfe',path()+'documentation/dokuwiki/index.php','','content');
    }
    dom.setContent('navigation',menue);
}

function resize() {
    /* resize content frame */
    var h1, h2, s, w1, w2;
    /* calculate width of window */
    s = getWinSize();
    w1 = dom.byId('content').offsetLeft;
    w2 = s.width-25;
    /* set width */
    dom.setStyle('content', {'width':(w2-w1)+'px'});
}

function getWinSize(win) {
    /* calculate window size */
    if(!win) win = window;
    var s = new Object(), obj;
    if(typeof win.innerWidth != 'undefined') {
        s.width = win.innerWidth;
        s.height = win.innerHeight;
    }else{
        obj = (win.document.compatMode && win.document.compatMode === "CSS1Compat") ? win.document.documentElement : win.document.body || null;
        s.width = parseInt(obj.clientWidth);
        s.height = parseInt(obj.clientHeight);
    }
    return s;
}
