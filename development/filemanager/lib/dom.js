/*
    ---------------------------------------------------------------------------------------

    phpBookWorm: DOM

    Copyright: CC-BY-SA 3.0, 2013
    Author: Udo Neist (webmaster@singollo.de, GPG-Fingerprint 4A8F B229 2AE9 9634 04D1 E2F0 21F2 E27D FE97 D87F)

    Part: dom.js

    Info: Erleichtert die Arbeit mit DOM-Objekten

    ---------------------------------------------------------------------------------------

    07.01.2013: Erste Version (Udo Neist)
    08.01.2013: Erweiterung von setContent() um das Löschen von DOM-Childs des angegebenen Elements (Udo Neist)
    26.01.2013: Neue Funktionen getValue() und setValue() (Udo Neist)
    28.01.2013: Dialogfenster hinzugefügt (Udo Neist)
    23.02.2013: Formulare im Dialog eingebaut (Udo Neist)
    30.03.2013: Aufruf einer externen Funktion bei showDialog ist nun optional (Udo Neist)
                Button "Abbruch" hinzugefügt (Udo Neist)
                addOptions() und clrOptions() für die Behandlung des Select-Tags eingebaut (Udo Neist)
                getAttribute() und setAttribute() hinzugefügt (Udo Neist) 
    31.03.2013: Beschreibung im Header geändert (Udo Neist)
                Input-Box für Rename/Move/Copy erstellt (Udo Neist)
                Upload-Formular als eigenständige Auswahl in showDialog() (Udo Neist)
                clrContent() und clrValue() hinzugefügt (Udo Neist)
                showViewer() für die Anzeige von PDF und Images eingebaut (Udo Neist)
    06.04.2013: Kommentare zu den Funktionsaufrufen hinzugefügt (Udo Neist)
                Style-Guide für Programmierung erstellt (Udo Neist)
*/

/*
    Style-Guide

    - Einrückung 1 Tab = 4 Space
    - Trennung von Funktionsnamen/Variablen mit einem Space vor und nach dem Doppelpunkt von der Funktion/dem Wert
    - Funktionsnamen: erstes Wort klein, alle weiteren mit erstem Buchstaben groß. Soll die Funktion schon im Namen andeuten! z.B. showDialog() für das Anzeigen eines modalen Dialogs
    - Interne Funktionen/Variablen mit einem Unterstrich vor dem Namen kennzeichnen. Ein Aufruf von extern sollte vermieden werden!
*/

var dom = {

    /*
    *   Globale Variablen:
    *
    *   _obj: Speicher für ein Objekt. Wird bei den Funktionsaufrufen eventuell geändert!
    *   _debug (boolean): Debug-Modus
    *   _exec: Enthält eine Funktion als String
    */

    _obj : '',
    _debug : false,
    _exec : '',

    /*
    *   showViewer()
    *
    *   Zeigt einen internen Viewer für verschiedene Objekte, unterstützt derzeit PDF, Images und Syntax-Highlightning per GeShi-Lib.
    *
    *   Variablen:
    *   _head: Text für Kopfzeile
    *   _file: File
    *   _mime: Mime-Typ für die Auswahl des Viewers
    */

    showViewer : function (_head,_file,_mime) {
        if (_mime!="application/pdf" && _mime.indexOf("image") ==-1 && _mime.indexOf("text") ==-1) {return false;}
        // Header und Text setzen
        dom.setContent('dom_vhead','.:: '+_head);
        dom.setStyle('dom_objectviewer',{'display': "none"});
        dom.setStyle('dom_imageviewer',{'display': "none"});
        dom.setStyle('dom_textviewer',{'display': "none"});
        if (_mime=="application/pdf") {
            dom.setAttribute('dom_objectviewer','data',_file);
            dom.setAttribute('dom_objectviewer','type',_mime);
            dom.setStyle('dom_objectviewer',{'width': "100%"});
            dom.setStyle('dom_objectviewer',{'height': "100%"});
            dom.setStyle('dom_objectviewer',{'display': "block"});
        }else if (_mime.indexOf("image") !=-1) {
            dom.setAttribute('dom_imageviewer','src',_file);
            dom.setStyle('dom_imageviewer',{'display': "block"});
        }else if (_mime.indexOf("text") !=-1) {
            dom.setStyle('dom_textviewer',{'display': "block"});
        }
        dom.setStyle('dom_viewer',{'display': "block"});
        // Modal
        dom.setStyle('dom_overlay',{'opacity': "0.90",'filter': "alpha(opacity=90)",'-moz-opacity': "0.90",'visibility': "visible"});
        document.body.style.overflow = "hidden";
    },

    /*
    *   hideViewer()
    *
    *   Schliesset den internen Viewer.
    *
    *   Variablen: keine
    */


    hideViewer : function () {
        dom.setStyle('dom_viewer',{'display': "none"});
        dom.setStyle('dom_overlay',{'visibility': "hidden"});
        document.body.style.overflow = "auto";
    },

    /*
    *   showDialog()
    *
    *   Dialogfenster modal über dem aktuellen Fenster anzeigen.
    *
    *   Variablen:
    *   _head: Text für Kopfzeile
    *   _text: Text für Body
    *   _type: Typ des Dialogs
    *   _button: Typ des Buttons
    *   _input: Formulartyp
    *   _func: Aufruf einer externen Funktion nach dem Ausblenden des Dialogs mit Ausnahme des Abbruch-Buttons
    */

    showDialog : function (_head,_text,_type,_button,_input,_func) {

        // Alle Dialogelemente erstmal ausblenden
        dom.setStyle('dom_buttonok',{'display': "none"});
        dom.setStyle('dom_buttonyesno',{'display': "none"});
        dom.setStyle('dom_buttonsavecancel',{'display': "none"});
        dom.setStyle('dom_buttondocancel',{'display': "none"});
        dom.setStyle('dom_buttoncancel',{'display': "none"});
        dom.setStyle('dom_foot',{'display': "none"});
        dom.setStyle('dom_selectdir',{'display': "none"});
        dom.setStyle('dom_uploadfile',{'display': "none"});
        dom.setStyle('dom_filename',{'display': "none"});
        dom.setStyle('dom_change2dir',{'display': "none"});

        // Dialogelemente aktivieren
        if (!_type || _type.length==0) dom.setStyle('dom_body',{'backgroundImage': ""});
        if (_type=='success') dom.setStyle('dom_body',{'backgroundImage': "url(css/gfx/success_bg.jpg)"});
        if (_type=='prompt') {
            if (_button=='ok') dom.setStyle('dom_buttonok',{'display': "block"});
            if (_button=='yesno') dom.setStyle('dom_buttonyesno',{'display': "block"});
            if (_button=='savecancel') dom.setStyle('dom_buttonsavecancel',{'display': "block"});
            if (_button=='docancel') dom.setStyle('dom_buttondocancel',{'display': "block"});
            if (_button=='cancel') dom.setStyle('dom_buttoncancel',{'display': "block"});
            if (_input=='upload') {
                dom.setStyle('dom_selectdir',{'display': "block"});
                dom.setStyle('dom_uploadfile',{'display': "block"});
                dom.setStyle('dom_change2dir',{'display': "block"});
            }else if (_input=='dir+file') {
                dom.setStyle('dom_selectdir',{'display': "block"});
                dom.setStyle('dom_filename',{'display': "block"});
            }else if (_input=='dir') {
                dom.setStyle('dom_selectdir',{'display': "block"});
            }else if (_input=='file') {
                dom.setStyle('dom_filename',{'display': "block"});
            }
            dom.setStyle('dom_foot',{'display': "block"});
            dom.setStyle('dom_body',{'backgroundImage': "url(css/gfx/prompt_bg.jpg)"});
        }
        if (_type=='warning') dom.setStyle('dom_body',{'backgroundImage': "url(css/gfx/warning_bg.jpg)"});
        if (_type=='error') dom.setStyle('dom_body',{'backgroundImage': "url(css/gfx/error_bg.jpg)"});

        // Header und Text setzen
        dom.setContent('dom_head','.:: '+_head);
        dom.setContent('dom_text',_text);
        dom.setStyle('dom_dialog',{'display': "block"});

        // Aufruf der externen Funktion
        dom._exec = _func;

        // Modal
        dom.setStyle('dom_overlay',{'opacity': "0.90",'filter': "alpha(opacity=90)",'-moz-opacity': "0.90",'visibility': "visible"});
        document.body.style.overflow = "hidden";

        // Automatisches Ausblenden nach 3 Sekunden
        if (_type!='prompt') setTimeout('dom.hideDialog()',3000);

        return;
    },

    /*
    *   hideDialog()
    *
    *   Schliessen des Dialogs. Aufruf einer externen Funktion nach dem Ausblenden des Dialogs mit Ausnahme des Abbruch-Buttons (siehe showDialog()).
    *
    *   Variablen:
    *   _button: Button-Typ (siehe showDialog())
    */

    hideDialog : function (_button) {
        dom.setStyle('dom_dialog',{'display': "none"});
        dom.setStyle('dom_overlay',{'visibility': "hidden"});
        document.body.style.overflow = "auto";

        // Aufruf der externen Funktion
        if (_button != "cancel" && typeof dom._exec == 'string' && dom._exec.length>0)  { eval(dom._exec); }
    },

    /*
    *   clrDialog()
    *
    *   Löscht den Inhalt der Formulare im Dialog
    */

    clrDialog : function () {
        // Formulare löschen
        dom.clrOptions('select_dir');
        dom.clrValue('select_filename');
        dom.clrValue('select_uploadfile');
    },

    /*
    *   setStyle()
    *
    *   CSS-Manipulation: Setzen einer Eigenschaft.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    *   _propertyObject: JSON-Notation von CSS-Eigenschaft und Wert
    */

    setStyle : function (_element,_propertyObject) {
        dom._obj = dom.byId(_element);
        if (dom._obj && _propertyObject) {
            if (dom._debug===true) dom.debug(_element+' '+_propertyObject);
            for (var property in _propertyObject) dom._obj.style[property] = _propertyObject[property];
            return true;
        } else {
            return false;
        }
    },

    /*
    *   getStyle()
    *
    *   CSS-Manipulation: Rückgabe der Eigenschaft.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    *   _property: CSS-Eigenschaft
    */

    getStyle : function (_element,_property) {
        dom._obj = dom.byId(_element);
        if (dom._obj && _property) {
            if (dom._debug===true) dom.debug(_element+' '+_property);
            return dom._obj.style[_property];
        } else {
            return false;
        }
    },

    /*
    *   clrOptions()
    *
    *   Löscht die Optionen eines Select-Elements.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    */

    clrOptions : function (_element) {
        dom._obj = dom.byId(_element);
        if (dom._obj) {
            if (dom._debug===true) dom.debug(_element);
            dom._obj.options.length = 0;
            return true;
        } else {
            return false;
        }
    },

    /*
    *   addOptions()
    *
    *   Fügt einem Select-Elements Optionen hinzu.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    *   _values: JSON-Notation der Optionen. Text und Wert sind identisch!
    */

    addOptions : function (_element,_values) {
        dom._obj = dom.byId(_element);
        if (dom._obj) {
            if (dom._debug===true) dom.debug(_element);
            dom.clrOptions(_element);
            for(index in _values) {
                dom._obj.options[dom._obj.options.length] = new Option(_values[index], _values[index]);
            }
            return true;
        } else {
            return false;
        }
    },

    /*
    *   clrContent()
    *
    *   Löscht den Inhalt eines Elements.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    */

    clrContent : function (_element) {
        dom.setContent(_element,'');
        return true;
    },

    /*
    *   setContent()
    *
    *   Setzt den Inhalt eines Elements.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    *   _value: Text
    */

    setContent : function (_element,_value) {
        dom._obj = dom.byId(_element);
        if (dom._obj) {
            if (dom._debug===true) dom.debug(_element+' '+_value);
            while (dom._obj.childNodes.length > 0) {
                dom._obj.removeChild(dom._obj.lastChild);
            }
            dom._obj.innerHTML=_value;
            return true;
        } else {
            return false;
        }
    },

    /*
    *   getContent()
    *
    *   Gibt den Inhalt eines Elements zurück.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    */

    getContent : function (_element) {
        dom._obj = dom.byId(_element);
        if (dom._obj) {
            if (dom._debug===true) dom.debug(_element);
            return dom._obj.innerHTML;
        } else {
            return false;
        }
    },

    /*
    *   clrValue()
    *
    *   Löscht den Wert eines Formular-Objekts.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    */

    clrValue : function (_element) {
        dom.setValue(_element,'');
        return true;
    },

    /*
    *   setValue()
    *
    *   Setzt den Wert eines Formular-Objekts.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    *   _value: Wert
    */

    setValue : function (_element,_value) {
        dom._obj = dom.byId(_element);
        if (dom._obj) {
            if (dom._debug===true) dom.debug(_element);
            if(dom._obj.checked) {
                dom._obj.checked = _value;
            }else{
                dom._obj.value = _value;
            }
            return true;
        } else {
            return false;
        }
    },

    /*
    *   getValue()
    *
    *   Gibt den Inhalt eines Formular-Objekts zurück.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    */

    getValue : function (_element) {
        dom._obj = dom.byId(_element);
        if (dom._obj) {
            if (dom._debug===true) dom.debug(_element);
            if(dom._obj.options && dom._obj.options>0) {
                return dom._obj.options[dom._obj.selectedIndex].text;
            }else if(dom._obj.checked) {
                return dom._obj.checked;
            }else{
                return dom._obj.value;
            }
        } else {
            return false;
        }
    },

    /*
    *   setAttribute()
    *
    *   Setzt ein Attribut eines Elements. Wenn es noch nicht vorhanden ist, wird es erzeugt.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    *   _attribute: Attribut
    *   _value: Wert
    */

    setAttribute : function (_element,_attribute,_value) {
        dom._obj = dom.byId(_element);
        if (dom._obj) {
            if (dom._debug===true) dom.debug(_element);
            dom._obj.setAttribute(_attribute,_value);
        } else {
            return false;
        }
    },

    /*
    *   getAttribute()
    *
    *   Gibt den Wert eines Attributes eines Elements zurück.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    *   _attribute: Attribut
    */

    getAttribute : function (_element,_attribute) {
        dom._obj = dom.byId(_element);
        if (dom._obj) {
            if (dom._debug===true) dom.debug(_element);
            if(dom._obj.getAttribute(_attribute)) {
                return dom._obj.getAttribute(_attribute);
            }else{
                return false;
            }
        } else {
            return false;
        }
    },

    

    /*
    *   byId()
    *
    *   Sucht ein Element mit einer ID und gibt das Objekt zurück.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    */

    byId : function (_element) {
        if (_element && document.getElementById(_element)) {
            if (dom._debug===true) dom.debug(_element);
            return document.getElementById(_element);
        } else {
            return false;
        }
    },

    /*
    *   query()
    *
    *   Sucht ein spezielles Tag gibt das Objekt oder mehrere Objekte zurück. Optional kann auch ein Element (ID) angegeben werden, das der Ausgangspunkt der Suche ist.
    *
    *   Variablen:
    *   _tag: Tag (HTML-Element)
    *   _element (optional): ID eines Objekts
    */

    query : function (_tag,_element) {
        var node;
        if (_element && dom.byId(_element)) {
            node = dom.byId(_element);
        }else{
            node = document.body;
        }
        return node.getElementsByTagName(_tag);
    },

    /*
    *   Events: Nach John Resig, erklärt auf Flexible Javascript Events
    */

    /*
    *   addEvent()
    *
    *   Event hinzufügen.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    *   _type: Typ des Events (ohne das "on"!)
    *   _fn: Externe Funktion, die beim Eintritt des Events aufgerufen wird.
    */

    addEvent : function (_element,_type,_fn ) {
        dom._obj = dom.byId(_element);
        if (dom._obj.addEventListener) {
            dom._obj.addEventListener(_type, _fn, false);
        } else if (dom._obj.attachEvent) {
            dom._obj["e"+_type+_fn] = _fn;
            dom._obj[_type+_fn] = function() {dom._obj["e"+_type+_fn]( window.event );}
            dom._obj.attachEvent("on"+_type, dom._obj[_type+_fn]);
        }
    },

    /*
    *   removeEvent()
    *
    *   Event löschen.
    *
    *   Variablen:
    *   _element: ID des entsprechenden Objekts
    *   _type: Typ des Events (ohne das "on"!)
    *   _fn: Externe Funktion, die beim Eintritt des Events aufgerufen wird.
    */

    removeEvent : function (_element,_type,_fn ) {
        dom._obj = dom.byId(_element);
        if (dom._obj.removeEventListener) {
            dom._obj.removeEventListener(_type,_fn,false);
        } else if (dom._obj.detachEvent) {
            dom._obj.detachEvent("on"+_type, dom._obj[_type+_fn]);
            dom._obj[_type+_fn] = null;
            dom._obj["e"+_type+_fn] = null;
        }
    },

    /*
    *   debug()
    *
    *   Gibt einen Text auf der Javascript-Konsole aus.
    *
    *   Variablen:
    *   _txt: Text
    */

    debug : function (_txt) {
        if (!_txt || _txt.length==0) return false;
        console.log(_txt);
    }

}