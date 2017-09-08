/**
 * Data can often be a complicated mix of numbers and letters (file names
 * are a common example) and sorting them in a natural manner is quite a
 * difficult problem.
 *
 * Fortunately a deal of work has already been done in this area by other
 * authors - the following plug-in uses the [naturalSort() function by Jim
 * Palmer](http://www.overset.com/2008/09/01/javascript-natural-sort-algorithm-with-unicode-support) to provide natural sorting in DataTables.
 *
 *  @name Natural sorting
 *  @summary Sort data with a mix of numbers and letters _naturally_.
 *  @author [Jim Palmer](http://www.overset.com/2008/09/01/javascript-natural-sort-algorithm-with-unicode-support)
 *  @author [Michael Buehler] (https://github.com/AnimusMachina)
 *
 *  @example
 *    $('#example').dataTable( {
 *       columnDefs: [
 *         { type: 'natural', targets: 0 }
 *       ]
 *    } );
 *
 *    Html can be stripped from sorting by using 'natural-nohtml' such as
 *
 *    $('#example').dataTable( {
 *       columnDefs: [
 *    	   { type: 'natural-nohtml', targets: 0 }
 *       ]
 *    } );
 *
 */

(function() {
    /**
	 * Converts a string containing a SI Prefix, to its E-Notation equivalent.
     * @param x The string which should be converted
     */
    function siToE(x) {
    	//Remove whitespace
    	x = x.trim();
		//Replace comma with dot, so they are grouped together
        x = x.replace(/,/g, '.')

    	//Pico
    	x = x.replace(/\b(\d*[\,\.]?\d+)p([a-z]?)\b/ig,"$1e-12");
        //Nano
        x = x.replace(/\b(\d*[\,\.]?\d+)n([a-z]?)\b/ig,"$1e-09");
        //Micro
        x = x.replace(/\b(\d*[\,\.]?\d+)u([a-z]?)\b/ig,"$1e-06");
        x = x.replace(/\b(\d*[\,\.]?\d+)\u00b5([a-z]?)\b/ig,"$1e-06");
        x = x.replace(/\b(\d*[\,\.]?\d+)\u03bc([a-z]?)\b/ig,"$1e-06");
		//Milli
        x = x.replace(/\b(\d*[\,\.]?\d+)m([a-z]?)\b/g,"$1e-03");
        //Centi
        x = x.replace(/\b(\d*[\,\.]?\d+)c([a-z]?)\b/ig,"$1e-02");
        //Kilo
        x = x.replace(/\b(\d*[\,\.]?\d+)k([a-z]?)\b/ig,"$1e03");
        //Mega
        x = x.replace(/\b(\d*[\,\.]?\d+)M([a-z]?)\b/g,"$1e06");

        return x;
	}

    /*
     * Natural Sort algorithm for Javascript - Version 0.8.1 - Released under MIT license
     * Author: Jim Palmer (based on chunking idea from Dave Koelle)
     * modified by Jan Böhmer, for HTML striping and support of SI prefixes
     */
    function naturalSort (a, b, html) {
        var re = /(^([+\-]?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?(?=\D|\s|$))|^0x[\da-fA-F]+$|\d+)/g,
            sre = /^\s+|\s+$/g,   // trim pre-post whitespace
            snre = /\s+/g,        // normalize all whitespace to single ' ' character
            dre = /(^([\w ]+,?[\w ]+)?[\w ]+,?[\w ]+\d+:\d+(:\d+)?[\w ]?|^\d{1,4}[\/\-]\d{1,4}[\/\-]\d{1,4}|^\w+, \w+ \d+, \d{4})/,
            hre = /^0x[0-9a-f]+$/i,
            ore = /^0/,
            i = function(s) {
                return (naturalSort.insensitive && ('' + s).toLowerCase() || '' + s).replace(sre, '');
            },
            htmre = /(<([^>]+)>)/ig,
            h = function (s) {
                if (!html) {
                    return s.replace(htmre, '');
                }
            },
            x = h(a),
            y = h(b),
            //Convert SI prefixes
            x = siToE(x),
            y = siToE(y),
            // convert all to strings strip whitespace
            x = i(x),
            y = i(y),
            //Strip HTML tags if wished
            x = h(x),
			y = h(y),
            // chunk/tokenize
            xN = x.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
            yN = y.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
            // numeric, hex or date detection
            xD = parseInt(x.match(hre), 16) || (xN.length !== 1 && Date.parse(x)),
            yD = parseInt(y.match(hre), 16) || xD && y.match(dre) && Date.parse(y) || null,
            normChunk = function(s, l) {
                // normalize spaces; find floats not starting with '0', string or 0 if not defined (Clint Priest)
                return (!s.match(ore) || l == 1) && parseFloat(s) || s.replace(snre, ' ').replace(sre, '') || 0;
            },
            oFxNcL, oFyNcL;
        // first try and sort Hex codes or Dates
        if (yD) {
            if (xD < yD) { return -1; }
            else if (xD > yD) { return 1; }
        }
        // natural sorting through split numeric strings and default strings
        for(var cLoc = 0, xNl = xN.length, yNl = yN.length, numS = Math.max(xNl, yNl); cLoc < numS; cLoc++) {
            oFxNcL = normChunk(xN[cLoc] || '', xNl);
            oFyNcL = normChunk(yN[cLoc] || '', yNl);
            // handle numeric vs string comparison - number < string - (Kyle Adams)
            if (isNaN(oFxNcL) !== isNaN(oFyNcL)) {
                return isNaN(oFxNcL) ? 1 : -1;
            }
            // if unicode use locale comparison
            if (/[^\x00-\x80]/.test(oFxNcL + oFyNcL) && oFxNcL.localeCompare) {
                var comp = oFxNcL.localeCompare(oFyNcL);
                return comp / Math.abs(comp);
            }
            if (oFxNcL < oFyNcL) { return -1; }
            else if (oFxNcL > oFyNcL) { return 1; }
        }
    }

    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
        "natural-asc": function ( a, b ) {
            return naturalSort(a,b,true);
        },

        "natural-desc": function ( a, b ) {
            return naturalSort(a,b,true) * -1;
        },

        "natural-nohtml-asc": function( a, b ) {
            return naturalSort(a,b,false);
        },

        "natural-nohtml-desc": function( a, b ) {
            return naturalSort(a,b,false) * -1;
        },

        "natural-ci-asc": function( a, b ) {
            a = a.toString().toLowerCase();
            b = b.toString().toLowerCase();

            return naturalSort(a,b,true);
        },

        "natural-ci-desc": function( a, b ) {
            a = a.toString().toLowerCase();
            b = b.toString().toLowerCase();

            return naturalSort(a,b,true) * -1;
        },

        "natural-nohtml-ci-asc": function( a, b ) {
            a = a.toString().toLowerCase();
            b = b.toString().toLowerCase();

            return naturalSort(a,b,false);
        },

        "natural-nohtml-ci-desc": function( a, b ) {
            a = a.toString().toLowerCase();
            b = b.toString().toLowerCase();

            return naturalSort(a,b,false) * -1;
        }
    } );

}());
