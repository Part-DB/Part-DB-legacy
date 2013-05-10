/*
    ---------------------------------------------------------------------------------------

    phpBookWorm: Ajax

    Copyright: CC-BY-SA 3.0, 2012-2013
    Author: Udo Neist (webmaster@singollo.de, GPG-Fingerprint 4A8F B229 2AE9 9634 04D1 E2F0 21F2 E27D FE97 D87F)

    Part: ajax.js

    Info: Klasse für XMLHttpRequest

    ---------------------------------------------------------------------------------------

    xx.xx.2012: Erste Version (Udo Neist)
    07.01.2013: Funktion für XMLHttpRequest erstellt (Udo Neist)
                Diverse Änderungen (Udo Neist)
    08.01.2013: Funktion für XMLHttpRequest überarbeitet (Udo Neist)
                Prüfung auf Dojo-Elemente in XMLHttpRequest.responseText und Übergabe der Daten an Dojo, falls vorhanden (Udo Neist)
    15.01.2013: Eventhandler ergänzt (Udo Neist)
                Load-Event prüft auf Status 200 und führt data.load() aus, 3xx werden per data.redirect() behandelt, 2xx (ausser 200) und 4xx werden als Fehler angesehen (Udo Neist)
                Statusmeldung in die Funktion Status() ausgelagert (Udo Neist)
    24.01.2013: Kleinere Optimierungen (Udo Neist)
    24.02.2013: Event-Handler wegen IE-Problematik überarbeitet (Udo Neist)
    03.03.2013: Key zur Absicherung hinzugefügt (Udo Neist)
    30.03.2013: Upload per POST hinzugefügt (Udo Neist)
    06.04.2013: Kommentare hinzugefügt bzw. geändert(Udo Neist)
                Style-Guide für Programmierung erstellt (Udo Neist)
*/

/*
    Style-Guide

    - Einrückung 1 Tab = 4 Space
    - Trennung von Funktionsnamen/Variablen mit einem Space vor und nach dem Doppelpunkt von der Funktion/dem Wert
    - Funktionsnamen: erstes Wort klein, alle weiteren mit erstem Buchstaben groß. Soll die Funktion schon im Namen andeuten! z.B. showDialog() für das Anzeigen eines modalen Dialogs
    - Interne Funktionen/Variablen mit einem Unterstrich vor dem Namen kennzeichnen. Ein Aufruf von extern sollte vermieden werden!
*/

var ajax = {

    /*
    *   Globale Variablen
    */

    /*
    *
    *   http://www.ipaste.org/GJd
    *
    *   parms()
    *
    *   Erzeugt einen URL-String aus einem Array.
    *
    *   Variablen:
    *   al: JSON-Array
    */

    parms : function (a1) {
        t=[];
        for(x in a1) {
            t.push(x+"="+encodeURI(a1[x]));
        }
        return t.join("&");
    },

    /*
    *
    *   xhr()
    *
    *   XMLHttpRequest
    *
    *   Variablen:
    *   data: Objekt
    *
    *       - Alle Methoden
    *       method: POST oder GET
    *       sync: Sync/Async-Modus
    *
    *       - Kein Upload
    *       value: Werte für die Datenübergabe
    *
    *       - Upload
    *       id: ID eines HTML-Elements für Fortschrittbalken bei Upload
    *       file: File für Upload
    *       target: Zielverzeichnis bei Upload
    *
    *       - Externe Funktionen, soweit vom Browser unterstützt
    *       load() (normal und Upload)
    *       error() (normal und Upload)
    *       progress() (normal und Upload)
    *       abort() (normal und Upload)
    *       loadstart() (ohne Upload)
    *       loadend() (ohne Upload)
    */

    xhr : function (data) {

        var xmlHttp;
        /*
            Korrekten Aufruf für XMLHttpRequest herausfinden.
        */
        if (window.XMLHttpRequest) {
            xmlHttp=new XMLHttpRequest();
        } else {
            try {
                xmlHttp=new ActiveXObject('Microsoft.XMLHTTP');
            } catch (e) {
                try {
                    xmlHttp=new ActiveXObject('Msxml2.XMLHTTP');
                } catch (e) {
                    try {
                        xmlHttp=new ActiveXObject('Msxml3.XMLHTTP');
                    } catch (e) {
                        return false;
                    }
                }
            }
        }
        /*
            Handler definieren
        */
        if (typeof xmlHttp.addEventListener == 'function') {
            xmlHttp.addEventListener('loadstart',function(e){if (typeof data.loadstart == 'function') {data.loadstart(xmlHttp.response,xmlHttp.status)}},false);
            xmlHttp.addEventListener('loadend',function(e){if (typeof data.loadend == 'function') {data.loadend(xmlHttp.response,xmlHttp.status)}},false);
            xmlHttp.addEventListener('load',
                function(e){
                    if (xmlHttp.status >= 400 && typeof data.error == 'function') {
                        // Fehlercodes
                        data.error(xmlHttp.response,xmlHttp.status);
                    }else if (xmlHttp.status >= 300 && typeof data.redirect == 'function') {
                        // Redirects
                        data.redirect(xmlHttp.response,xmlHttp.status);
                    }else if (xmlHttp.status > 200 && typeof data.error == 'function') {
                        // Es werden zwar Daten übergeben, aber sie sind nicht unbedingt nutzbar
                        data.error(xmlHttp.response,xmlHttp.status);
                    }else if (xmlHttp.status == 200 && typeof data.load == 'function') {
                        // Alles OK
                        data.load(xmlHttp.response,xmlHttp.status);
                    }
                },
                false
            );
            xmlHttp.addEventListener('error',function(e){if (typeof data.error == 'function') {data.error(xmlHttp.response,xmlHttp.status)}},false);
            xmlHttp.addEventListener('progress',function(e){if (typeof data.progress == 'function') {data.progress(xmlHttp.response,xmlHttp.status)}},false);
            xmlHttp.addEventListener('abort',function(e){if (typeof data.abort == 'function') {data.abort(xmlHttp.response,xmlHttp.status)}},false);
        }else{
            xmlHttp.onreadystatechange = function() {
                if (xmlHttp.readyState == 4) {
                    if (xmlHttp.status >= 400 && typeof data.error == 'function') {
                        // Fehlercodes
                        data.error(xmlHttp.responseText,xmlHttp.status);
                    }else if (xmlHttp.status >= 300 && typeof data.redirect == 'function') {
                        // Redirects
                        data.redirect(xmlHttp.responseText,xmlHttp.status);
                    }else if (xmlHttp.status > 200 && typeof data.error == 'function') {
                        // Es werden zwar Daten übergeben, aber sie sind nicht unbedingt nutzbar
                        data.error(xmlHttp.responseText,xmlHttp.status);
                    }else if (xmlHttp.status == 200 && typeof data.load == 'function') {
                        // Alles OK
                        data.load(xmlHttp.responseText,xmlHttp.status);
                    }
                }
            }
        }
        if (typeof xmlHttp.upload.addEventListener == 'function') {
            var id=data.id;
            xmlHttp.upload.addEventListener('progress',function(e){if (typeof data.progress == 'function') {data.progress(xmlHttp.response,xmlHttp.status)}},false);
            xmlHttp.upload.addEventListener('load',function(e){if (typeof data.load == 'function') {data.load(xmlHttp.response,xmlHttp.status)}},false);
            xmlHttp.upload.addEventListener('error',function(e){if (typeof data.load == 'function') {data.error(xmlHttp.response,xmlHttp.status)}}, false);
            xmlHttp.upload.addEventListener('abort',function(e){if (typeof data.load == 'function') {data.abort(xmlHttp.response,xmlHttp.status)}}, false);
        }
        /*
            XMLHttpRequest
        */
        if (!data.sync) data.sync=true;
        xmlHttp.open(data.method,data.url,data.sync);
/*        xmlHttp.setRequestHeader('User-Agent','XMLHTTP/1.0');*/
        data.value=ajax.parms(data.value);
        if (data.value && data.method=='POST') {
            xmlHttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xmlHttp.setRequestHeader('Content-length', data.value.length);
            xmlHttp.setRequestHeader('Connection', 'close');
        }
        if (data.file && data.method=='POST') {
            xmlHttp.overrideMimeType('text/plain; charset=x-user-defined-binary');
            var fd = new FormData;
            fd.append('File',data.file);
            fd.append('target',data.target);
            xmlHttp.send(fd);
        }else{
            xmlHttp.send(data.value);
        }
    }

}
