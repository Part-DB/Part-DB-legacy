/*
    ---------------------------------------------------------------------------------------

    phpBookWorm: Filemanager

    Copyright: CC-BY-SA 3.0, 2012-2013
    Author: Udo Neist (webmaster@singollo.de, GPG-Fingerprint 4A8F B229 2AE9 9634 04D1 E2F0 21F2 E27D FE97 D87F)

    Part: filemanager.js

    Info: Benötigt vlibTemplate-Engine im PHP-Teil

    ---------------------------------------------------------------------------------------

    03.01.2013: Erste Version (Udo Neist)
    04.01.2013: Verlegung der Funktionen in einen eigenen Namensraum (Udo Neist)
                Kopfzeilen geändert (Udo Neist)
    07.01.2013: Umstellung auf dom.js (Udo Neist)
    08.01.2013: Überprüfung der Variable _file in showFiles() hinzugefügt (Udo Neist)
    24.01.2013: Kleinere Optimierungen (Udo Neist)
    26.01.2013: command() erstellt (Udo Neist)
                showFileInfo() markiert jetzt das ausgewählte File im Explorer (Udo Neist)
    28.01.2013: Dialog auf dom.showDialog() umgestellt (Udo Neist)
    03.03.2013: Einige Verbesserungen eingebaut (Udo Neist)
                Token für Zugriffsschutz (Udo Neist)
    30.03.2013: Upload-Link unabhängig vom Rest der Commands gemacht (Udo Neist)
                Upload von mehrere Files mit Angabe eines Zielverzeichnisses (Udo Neist)
                Funktion _getDirs() nach command() verschoben (Udo Neist)
    31.03.2013: Bugfix bei der change2dir-Funktion innerhalb upload() (Udo Neist)
                Umbenennung des Upload-Elementes (Udo Neist)
                _getDirs() durch eine neue Funktion ersetzt (Udo Neist)
                Interner Viewer erlaubt die Anzeige von PDF und Images über dom.showViewer() (Udo Neist)
    06.04.2013: Kommentare hinzugefügt bzw. geändert(Udo Neist)
                Style-Guide für Programmierung erstellt (Udo Neist)
                fcommand() und download() als private Funktionen deklariert (Udo Neist)
                Grafik für Warteschleife geändert (Udo Neist)
                showFiles() erlaubt jetzt auch das in filemanager.php definierte Hauptverzeichnis zu nutzen (Udo Neist)
    07.04.2013: Kleine Änderung in viewer() (Udo Neist)
                Breadcrumb-Navigation hinzugefügt (Udo Neist)
*/

/*
    Style-Guide

    - Einrückung 1 Tab = 4 Space
    - Trennung von Funktionsnamen/Variablen mit einem Space vor und nach dem Doppelpunkt von der Funktion/dem Wert
    - Funktionsnamen: erstes Wort klein, alle weiteren mit erstem Buchstaben groß. Soll die Funktion schon im Namen andeuten! z.B. showDialog() für das Anzeigen eines modalen Dialogs
    - Interne Funktionen/Variablen mit einem Unterstrich vor dem Namen kennzeichnen. Ein Aufruf von extern sollte vermieden werden!
*/

var filemanager = {

    /*
    *   Globale Variablen:
    *
    *   token: Beinhaltet einen String zur Identifizierung des gesendeten Kommandos an den Server
    *   loading (HTML-String): Grafischer Platzhalter für den Inhalt (Warteschleife)
    */

    token : '',
    loading : '<img src="css/gfx/ajax-loader.gif">',
    _userootdir : true,

    /*
    *   showFiles()
    *
    *   Holt eine Liste von Dateien vom Server, wird als fertige Webseite übergeben.
    *
    *   Variablen:
    *   _file: File
    *   _type: Mime-Typ für die Auswahl des Viewers
    */

    showFiles : function (_file,_type) {

        dom.setStyle('file_commands',{'display':'none'});
        dom.setStyle('file_upload',{'display':'block'});
        dom.setContent('file_explorer',filemanager.loading);

        _file = filemanager._clrDirNames(_file);

        if (_file.length>0 || (!_file && filemanager._userootdir)) {
            filemanager.xhrGet('file_explorer','filemanager.php?token='+filemanager.token+'&list='+_file+'&type='+_type);
            filemanager.breadcrumb(((_file.length==0)?'/':_file));
        }else{
            filemanager.breadcrumb();
            dom.clrContent('file_explorer');
        }

        dom.setContent('file_info','Bitte wählen Sie eine Datei aus.');
    },

    /*
    *   showFileInfo()
    *
    *   Zeigt Infos zu einem File an. Wird als fertige Webseite übergeben.
    *
    *   Variablen:
    *   _file: File
    *   _id: HTML-Objekt des ausgewählten Files (ändert die CSS-Klasse von "hover" nach "selected")
    */

    showFileInfo : function (_file,_id) {
        if (dom.byId(_id).className == 'selected') return false;
        dom.setContent('file_info',filemanager.loading);
        filemanager.xhrGet('file_info','filemanager.php?token='+filemanager.token+'&info='+_file);
        dom.setStyle('file_upload',{'display':'none'});
        dom.setStyle('file_commands',{'display':'block'});
        var obj = dom.byId('file_explorer');
        var list = obj.getElementsByTagName('li');
        for(var i=0; i<list.length; i++) {
            if (list[i].id.indexOf('fm_dir_')==-1) {dom.byId(list[i].id).className = 'hover';}
        }
        dom.byId(_id).className = 'selected';
    },

    /*
    *   command()
    *
    *   Kommandofunktionen
    *
    *   Variablen:
    *   _command: Kommando (download, upload, rename, delete, copy, move)
    *   _files: Zeigt einen Filetransfer an
    */

    command : function (_command,_files) {
        dom.clrDialog();
        if (_command == 'download') return filemanager._download();
        if (_command == 'upload') {filemanager._getDirs((function(){dom.showDialog('Filemanager','Datei(en) hochladen','prompt','cancel','upload');})());}

        if (!_files) {
            if (_command == 'rename') {
               dom.setValue('select_filename',filemanager._selectedFile());
               dom.showDialog('Filemanager','Datei »'+filemanager._selectedFile()+'« umbenennen nach','prompt','docancel','file',"filemanager._fcommand('rename')");
            };
            if (_command == 'delete') {dom.showDialog('Filemanager','Datei »'+filemanager._selectedFile()+'« löschen','prompt','docancel','',"filemanager._fcommand('delete')");};
            if (_command == 'copy') {filemanager._getDirs((function(){dom.showDialog('Filemanager','Datei »'+filemanager._selectedFile()+'« kopieren nach','prompt','docancel','dir+file',"filemanager._fcommand('copy')");})());};
            if (_command == 'move') {filemanager._getDirs((function(){dom.showDialog('Filemanager','Datei »'+filemanager._selectedFile()+'« verschieben nach','prompt','docancel','dir',"filemanager._fcommand('move')");})());};
        }
    },

    /*
    *   upload()
    *
    *   Zeigt den Upload von Dateien an. Ruft zum Upload eines Files aus der Liste die Unterfunktion _upload() per new() auf, da sonst der Upload nicht richtig angezeigt werden kann!
    *
    *   Variablen:
    *   _files: Liste von Dateien aus dem Formular
    */


    upload: function (_files) {
        dom.hideDialog();
        var text ="<p>Upload...</p><input type='hidden' id='uploadcounter' value='0' maxcount='0'><table>";
        // Jede einzelne ausgewählte Datei wird über XMLHttpRequest versendet.
        for (var i=0;i<_files.length;i++) {
            text += "<tr><td class='upload_file'><span class='upload_file'>"+_files[i].name+"</span></td><td class='upload_bar'><div class='upload_bar' id='upload_bar"+i+"'><span class='upload_text' id='upload_text"+i+"'></span></div></td></tr>";
        }
        text+='</table>';
        dom.byId('file_explorer').innerHTML=text;
        dom.setAttribute('uploadcounter','maxcount',_files.length);
        //dom.addEvent('uploadcounter','change', filemanager.change());
        for (var i=0;i<_files.length;i++) {
            new filemanager._upload(_files[i],i);
        }
    },

    /*
    *   viewer()
    *
    *   Ruft den Viewer aus der DOM-Klasse auf.
    */

    viewer : function () {
        var file = dom.getValue('base')+'/'+dom.getValue('path')+'/'+filemanager._selectedFile(),mime = dom.byId('filemanager_mime').innerHTML;
        dom.clrContent('dom_textviewer');
        dom.setAttribute('dom_objectviewer','data','');
        dom.setAttribute('dom_objectviewer','type','');
        dom.setAttribute('dom_imageviewer','src','');
        if (mime.indexOf("text") !=-1 && mime.length>0) {
            filemanager.xhrGet('dom_textviewer','filemanager.php?token='+filemanager.token+'&geshi='+file+'&mime='+mime);
            dom.showViewer('Viewer (Klick hier zum Schliessen)','',mime);
        }else{
            dom.showViewer('Viewer (Klick hier zum Schliessen)',file,mime);
        }
    },

    /*
    *   xhrCmd()
    *
    *   Aufruf des Kommandos per XMLHttpRequest über AJAX-Klasse.
    *
    *   Variablen:
    *   _url: URL des Aufrufs
    */

    xhrCmd : function (_url) {
        // xHr-Aufruf
        var data = {
            method:'GET',
            url:path()+_url,
            load: function(response, ioArgs) {
                var response = eval('(' + response + ')');
                if (ioArgs=='200') {
                    filemanager.showFiles(dom.getValue('path'),dom.getValue('type'));
                    dom.showDialog('Filemanager','Befehl >'+response.command +'< erfolgreich ausgeführt.','success');
                }else{
                    dom.showDialog('Filemanager','Befehl >'+response.command +'< konnte nicht ausgeführt werden.','error');
                }
                return response;
            },
            error: function(response, ioArgs) {
                showDialog('Filemanager','Befehl konnte nicht ausgeführt werden.','error');
                return response;
            }
        };
        return ajax.xhr(data);
    },

    /*
    *   xhrGet()
    *
    *   GET-Aufruf per XMLHttpRequest über AJAX-Klasse.
    *
    *   Variablen:
    *   _element: ID des Objektes für die Speicherung der Rückgabe
    *   _url: URL des Aufrufs
    */

    xhrGet : function (_element,_url) {
        // Templates
        Ergebnis = _url.search(/vlib_.+/);
        if (Ergebnis != -1) _url='templates/'+_url+'.html';
        // xHr-Aufruf
        var data = {
            method:'GET',
            url:path()+_url,
            load: function(response, ioArgs) {
                dom.setContent(_element,response);
                return response;
            },
            error: function(response, ioArgs) {
                dom.setContent(_element,"Ein Fehler ist aufgetreten: " + response + " [Funktion: " + _element + "::xhrGet]");
                return response;
            }
        };
        // Ergebnis des Aufrufs zurückgeben
        return ajax.xhr(data);
    },

    /*
    *   xhrPost()
    *
    *   POST-Aufruf per XMLHttpRequest über AJAX-Klasse.
    *
    *   Variablen:
    *   _element: ID des Objektes für die Speicherung der Rückgabe
    *   _url: URL des Aufrufs
    *   _values: Daten
    */

    xhrPost : function (_element,_url,_values) {
        // xHr-Aufruf
        var data = {
            method:'POST',
            url:path()+_url,
            value: _values,
            load: function(response, ioArgs) {
                dom.setContent(_element,response);
                return response;
            },
            error: function(response, ioArgs) {
                dom.setContent(_element,"Ein Fehler ist aufgetreten: " + response + " [Funktion: " + _element + "::xhrGet]");
                return response;
            }
        };
        // Ergebnis des Aufrufs zurückgeben
        return ajax.xhr(data);
    },

    /*
    *
    *   Breadcrumb-Navigation (ohne Links)
    *
    *   Variablen:
    *   _text: Verzeichnis
    */

    breadcrumb : function(_text) {
        if (!dom.byId('file_breadcrumb')) return false;
        if (_text=='') {
            _text = 'keines ausgewählt';
        }else{
        }
        dom.setContent('file_breadcrumb',_text);
    },

    // Interne Funktionen

    /*
    *   _fcommand()
    *
    *   Unterfunktion von command(): Übergabe des Befehls.
    *
    *   Variablen:
    *   _command: Kommando (download, upload, rename, delete, copy, move)
    */

    _fcommand: function (_command) {
        filemanager.xhrCmd('filemanager.php?token='+filemanager.token+'&cmd='+Base64.encode(JSON.stringify(
            {
                'command'    : _command,
                'dir_source' : dom.getValue('path'),
                'dir_target' : dom.getValue('select_dir'),
                'source'     : filemanager._selectedFile(),
                'target'     : dom.getValue('select_filename')
            }
        )));
    },

    /*
    *   _download()
    *
    *   Unterfunktion von command(): Download des ausgewählten Files.
    *
    *   Variablen:
    *   keine
    */

    _download : function () {
        dom.byId('file_iframe').src = path()+'filemanager.php?token='+filemanager.token+'&get='+dom.getValue('filemanager_b64filename')+'&mime='+dom.getValue('filemanager_b64mime');
        return false;
    },

    /*
    *   _upload()
    *
    *   Upload von Dateien.
    *
    *   Variablen:
    *   _file: Datei
    *   _id: ID eines Objekts, der das obige File darstellt. Wird für den Fortschrittsbalken gebraucht.
    */

    _upload: function (_file,_id) {
            var data = {
                method:'POST',
                sync: false,
                id: _id,
                url:path() + 'filemanager.php',
                file: _file,
                target: dom.getValue('select_dir'),
                progress: function(response, ioArgs) {
                    var prozent;
                    if (response.lengthComputable) {
                        prozent=Math.round((response.loaded*100)/response.total);
                        if (dom.byId('upload_bar'+data.id)) {dom.setStyle('upload_bar'+data.id,{'width': prozent+'%'});}
                        if (dom.byId('upload_text'+data.id)) {dom.byId('upload_text'+data.id).innerHTML=prozent+'%';}
                    }
                },
                load: function(response, ioArgs) {
                    if (ioArgs!=0) {
                        dom.setValue('uploadcounter',parseInt(dom.getValue('uploadcounter'))+1);
                        if (dom.byId('upload_bar'+data.id)) {dom.setStyle('upload_bar'+data.id,{'width': '100%'});}
                        if (dom.byId('upload_text'+data.id)) {dom.byId('upload_text'+data.id).innerHTML='100%';}
                        if (parseInt(dom.getValue('uploadcounter')) == parseInt(dom.getAttribute('uploadcounter','maxcount')) && dom.getValue('select_change2dir') == true) {
                             setTimeout("filemanager.showFiles('"+dom.getValue('select_dir')+"')",5000);
                        }
                    }
                }
            };
            ajax.xhr(data);
    },

    /*
    *   _getDirs()
    *
    *   Holt eine Liste von Verzeichnissen für ein Formular.
    *
    *   Variablen:
    *   keine
    */

    _getDirs : function () {
        var data = {
            method:'GET',
            url:path()+'filemanager.php?token='+filemanager.token+'&dir',
            load: function(response, ioArgs) {
                if (response.length>0) {
                    var response = eval('(' + response + ')');
                    dom.addOptions('select_dir',response.dir);
                    dom.setValue('select_filename',filemanager._selectedFile());
                }
                return response;
            },
            error: function(response, ioArgs) {
                return false;
            }
        };
        // Ergebnis des Aufrufs zurückgeben
        return ajax.xhr(data);
    },

    /*
    *   _selectedFile()
    *
    *   Übergibt den Filenamen des ausgewählten Files (gespeichert im Attribut "file").
    *
    *   Variablen:
    *   keine
    */

    _selectedFile : function () {
        var file,obj = dom.byId('file_explorer'),list = obj.getElementsByTagName('li');
        for(var i=0; i<list.length; i++) {
            if (dom.byId(list[i].id).className == 'selected') {
                file=dom.byId(list[i].id).getAttribute('file');
                break;
            }
        }
        return file;
    },

    /*
    *   _getToken()
    *
    *   Holt ein neues Token für die Authentifizierung der Datenübergabe.
    *
    *   Variablen:
    *   keine
    */

    _getToken : function () {
        var data = {
            method:'POST',
            url:path()+'filemanager.php',
            value: {'get_token':1},
            load: function(response, ioArgs) {
                if (response.length>0) {
                    var response = eval('(' + response + ')');
                    filemanager.token = response.token;
                }
            },
            error: function(response, ioArgs) {
                return response;
            }
        };
        ajax.xhr(data);
    },

    _clrDirNames : function (_text) {
        _text = _text.replace(/[/\\*]/g, ' ');  // Alle Backslashes durch Leerzeichen ersetzen...
        _text = _text.replace(/\s+/g, ' ');     // ... anschliessend alle mehrfach auftretende Leerzeichen durch ein einzelnes austauschen ...
        _text = _text.replace(/\s+/g, '/');     // ... und zum Schluss wieder zurück zum Backslash
        return _text;
    }

}

