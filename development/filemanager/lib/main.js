/*
    ---------------------------------------------------------------------------------------

    phpBookWorm: Filemanager

    Copyright: CC-BY-SA 3.0, 2013
    Author: Udo Neist (webmaster@singollo.de, GPG-Fingerprint 4A8F B229 2AE9 9634 04D1 E2F0 21F2 E27D FE97 D87F)

    Part: main.js

    Info: Aufruf des Filemanagers


    ---------------------------------------------------------------------------------------

    07.04.2013: Erste Version (Udo Neist)
    27.07.2013: Popup überarbeitet (Udo Neist)
    28.07.2013: Initialisierung durch das asynchrone Nachladen von Daten geringfügig geändert (Udo Neist)
*/

var fenster;

/*  DOM und Ajax */
addJavascript(path()+'/lib/dom.js','head');
addJavascript(path()+'/lib/ajax.js','head');

/* Filemanager, basierend auf DOM- und AJAX-Klasse */
addJavascript(path()+'/lib/filemanager.js','head');

/* Weitere Funktionen/Klassen */
addJavascript(path()+'/lib/base64.xd.js','head');

/* Aufruf aller wichtigen Funktionen nach dem Laden der Seite */
window.onload = function() {
    // Token für Filemanager laden
    setTimeout('filemanager._getToken();',100);
    // Elemente an der Fenstergröße anpassen
    resize();
    if (dom.byId('popup')) {
        setTimeout('startFilemanager()',250);
    }
}
window.onresize=function() {
    // Content an Breite des Fensters anpassen
    resize()
};

window.onfocus=function() {
    // Token für Filemanager laden
    setTimeout('filemanager._getToken();',100);
}

function path() {
    /*
    *    Ermittelt den Installationspfad
    */
	var path = window.location.pathname;
	path = path.substring(0,path.lastIndexOf("/")+1);
	return path;
}

function popup(_mode) {
    /*
    *   Filemanager starten
    *
    *   Variablen:
    *   _mode = true: In einem neuen Fenster öffnen
    *   _mode = false: In einem Element (vorzugsweise DIV) öffnen
    */
    dom.removeElement('dom_overlay');
    if (_mode === false) {
        if (fenster) {
            fenster.close();
            fenster = null;
        }
        setTimeout('startFilemanager()',250);
    }else{
        if (fenster) return;
        dom.clrContent('content');
        fenster = window.open(path()+'popup.html', "Filemanager", "width=800,height=800,status=no,scrollbars=no,resizable=no");
        fenster.focus();
    }
}

function startFilemanager() {
    /*
    *
    *   Lädt den Filemanager abhängig vom Modus
    *
    */

    // Initialisiere Filemanager
    filemanager.init();

    setTimeout('loadDialog()',250);
    setTimeout('loadDirs()',250);
    
    // Filemanager starten
    if (dom.byId('popup')) {
        var data = {
            method:'GET',
            url:path()+'templates/vlib_filemanager_popup.html',
            load:function(response,status) {
                dom.setContent('popup',response);
            },
            error:function(response,status) {
                dom.setContent('popup',"Fehler: "+status);
            }
        };
        ajax.xhr(data);
    }else{
        // Filemanager starten
        var data = {
            method:'GET',
            url:path()+'templates/vlib_filemanager.html',
            load:function(response,status) {
                dom.setContent('content',response);
            },
            error:function(response,status) {
                dom.setContent('content',"Fehler: "+status);
           }
        };
        ajax.xhr(data);
    }
}

function loadDialog() {
    // Dialoge nachladen
    var data = {
        method:'GET',
        url:path()+'templates/vlib/vlib_filemanager_dialog.html',
        load:function(response,status) {
            dom.setContent('dom_overlay',response);
            if (!dom.byId('popup')) dom.setStyle('dom_viewer', {'left':'15%'});
        }
    };
    ajax.xhr(data);
}

function loadDirs() {
    // Lade die Verzeichnisliste zur Auswahl
    var data = {
        method:'GET',
        url:path()+'filemanager.php?token='+filemanager.token+'&lsdir',
        load:function(response,status) {
            dom.setContent('file_dirs',response);
        }
    };
    ajax.xhr(data); 
}

/*
*
*   Funktionen für die Webseite
*
*/
function addJavascript(jsname,pos) {
    var th = document.getElementsByTagName(pos)[0], s = document.createElement('script');
    s.setAttribute('type','text/javascript');
    s.setAttribute('charset','utf-8');
    s.setAttribute('src',jsname);
    th.appendChild(s);
}

function resize() {
    /*
        Passt die Größe einiger Elemente an die Fenstergröße an
    */
    var h1, h2, s, w1, w2;
    // Breite berechnen
    s = getWinSize();
    w1 = dom.byId('content').offsetLeft;
    w2 = s.width-25;
    // Content entsprechend setzen
    dom.setStyle('content', {'width':(w2-w1)+'px'});
}

function getWinSize(win) {
    /*
        Ermittelt die Größe des Fensters
    */
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
