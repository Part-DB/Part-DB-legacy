function round(val, prec)
{
    return Math.floor(val*Math.pow(10, prec))/Math.pow(10, prec);
}

var E3 = [1.0, 2.2, 4.7];
var E6 = [1.0, 1.5, 2.2, 3.3, 4.7, 6.8];
var E12 = [1.0, 1.2, 1.5, 1.8, 2.2, 2.7, 3.3, 3.9, 4.7, 5.6, 6.8, 8.2];
var E24 = [1.0, 1.1, 1.2, 1.3, 1.5, 1.6, 1.8, 2.0, 2.2, 2.4, 2.7, 3.0, 3.3, 3.6, 3.9, 4.3, 4.7, 5.1, 5.6, 6.2, 6.8, 7.5, 8.2, 9.1];
var E48 = [1, 1.05, 1.1, 1.15, 1.21, 1.27, 1.33, 1.4, 1.47, 1.54, 1.62, 1.69, 1.78, 1.87, 1.96, 2.05, 2.15, 2.26, 2.37, 2.49, 2.61, 2.74, 2.87, 3.01, 3.16, 3.32, 3.48, 3.65, 3.83, 4.02, 4.22, 4.42, 4.64, 4.87, 5.11, 5.36, 5.62, 5.9, 6.19, 6.49, 6.81, 7.15, 7.5, 7.87, 8.25, 8.66, 9.09, 9.53];
var E96 = [1, 1.02, 1.05, 1.07, 1.1, 1.13, 1.15, 1.18, 1.21, 1.24, 1.27, 1.3, 1.33, 1.37, 1.4, 1.43, 1.47, 1.5, 1.54, 1.58, 1.62, 1.65, 1.69, 1.74, 1.78, 1.82, 1.87, 1.91, 1.96, 2, 2.05, 2.1, 2.15, 2.21, 2.26, 2.32, 2.37, 2.43, 2.49, 2.55, 2.61, 2.67, 2.74, 2.8, 2.87, 2.94, 3.01, 3.09, 3.16, 3.24, 3.32, 3.4, 3.48, 3.57, 3.65, 3.74, 3.83, 3.92, 4.02, 4.12, 4.22, 4.32, 4.42, 4.53, 4.64, 4.75, 4.87, 4.99, 5.11, 5.23, 5.36, 5.49, 5.62, 5.76, 5.9, 6.04, 6.19, 6.34, 6.49, 6.65, 6.81, 6.98, 7.15, 7.32, 7.5, 7.68, 7.87, 8.06, 8.25, 8.45, 8.66, 8.87, 9.09, 9.31, 9.53, 9.76];
var E192 = [1, 1.01, 1.02, 1.04, 1.05, 1.06, 1.07, 1.09, 1.1, 1.11, 1.13, 1.14, 1.15, 1.17, 1.18, 1.2, 1.21, 1.23, 1.24, 1.26, 1.27, 1.29, 1.3, 1.32, 1.33, 1.35, 1.37, 1.38, 1.4, 1.42, 1.43, 1.45, 1.47, 1.49, 1.5, 1.52, 1.54, 1.56, 1.58, 1.6, 1.62, 1.64, 1.65, 1.67, 1.69, 1.72, 1.74, 1.76, 1.78, 1.8, 1.82, 1.84, 1.87, 1.89, 1.91, 1.93, 1.96, 1.98, 2, 2.03, 2.05, 2.08, 2.1, 2.13, 2.15, 2.18, 2.21, 2.23, 2.26, 2.29, 2.32, 2.34, 2.37, 2.4, 2.43, 2.46, 2.49, 2.52, 2.55, 2.58, 2.61, 2.64, 2.67, 2.71, 2.74, 2.77, 2.8, 2.84, 2.87, 2.91, 2.94, 2.98, 3.01, 3.05, 3.09, 3.12, 3.16, 3.2, 3.24, 3.28, 3.32, 3.36, 3.4, 3.44, 3.48, 3.52, 3.57, 3.61, 3.65, 3.7, 3.74, 3.79, 3.83, 3.88, 3.92, 3.97, 4.02, 4.07, 4.12, 4.17, 4.22, 4.27, 4.32, 4.37, 4.42, 4.48, 453, 4.59, 4.64, 4.7, 4.75, 4.81, 4.87, 4.93, 4.99, 5.05, 5.11, 5.17, 5.23, 5.3, 5.36, 5.42, 5.49, 5.56, 5.62, 5.69, 5.76, 5.83, 5.9, 5.97, 6.04, 6.12, 6.19, 6.26, 6.34, 6.42, 6.49, 6.57, 6.65, 6.73, 6.81, 6.9, 6.98, 7.06, 7.15, 7.23, 7.32, 7.41, 7.5, 7.59, 7.68, 7.77, 7.87, 7.96, 8.06, 8.16, 8.25, 8.35, 8.45, 8.56, 8.66, 8.76, 8.87, 8.98, 9.09, 9.2, 9.31, 9.42, 9.53, 9.65, 9.76, 9.88];

function get_series(series)
{
    switch (series) {
        case 3 :
            return E3;
        case 6 :
            return E6;
        case 12 :
            return E12;
        case 24 :
            return E24;
        case 48 :
            return E48;
        case 96 :
            return E96;
        case 192 :
            return E192;
    }
}

var units = ["", "k", "M", "G"]; // Einheit = Exponent / 3

/* Wert des ausgewählten Radiobuttons zurückgeben */
function getValue(radioButtons)
{
    for (var i = 0; i < radioButtons.length; ++i) {
        if (radioButtons[i].checked) {
            return radioButtons[i].value;
        }
    }
}

/* Erstelle aus dem Wert ein Array. Index 0 ist der Wert zwischen 0 und 1. Index 1 der Exponent.
   Die Einheitenzeichen aus dem units-Array können als Komma oder am Ende angegeben werden. Z.B.:
   1200
   1,2k
   1.2k
   1k2
*/
function r_create(value)
{
    var exp = 0;
    
    if (typeof(value) == 'string') {
        value = value.replace(/,/g, ".");
        for (var i = 1; i < units.length; ++i) {
            var regex = new RegExp(units[i], "g");
            if (value.search(regex) != -1) {
                value = value.replace(regex, ".");
                exp = 3*i;
                break;
            }
        }
        value = parseFloat(value);
    }

    var e = Math.floor(Math.log(value)/Math.LN10);
    value = value/Math.pow(10, e);
    return Array(value, exp+e);
}

function r_to_ohm(r)
{
    return r[0] * Math.pow(10, r[1]);
}

function findBestResistorMatch(r, series)
{
    // R = (10^m)^(1/n) * 10^k
    // log_10(R) = m/n + k
    // k = floor(log_10(R))
    // m = (log_10(R)-k) * n

    var k_low = r[1];
    var m_low = Math.floor(Math.log(r[0])/Math.LN10 * series.length);
    var k_high;
    var m_high;

    if (m_low < (series.length-1)) {
        k_high = k_low;
        m_high = m_low + 1;
    } else {
        k_high = k_low + 1;
        m_high = 0;
    }

    var error_low = (series[m_low]*Math.pow(10, k_low) - r_to_ohm(r)) / r_to_ohm(r);
    var error_high = (series[m_high]*Math.pow(10, k_high) - r_to_ohm(r)) / r_to_ohm(r);

    // return resistor
    if( Math.abs(error_high) < Math.abs(error_low) )
        return [series[m_high], k_high];
    else
        return [series[m_low], k_low];
}

/**************** Farbrechner ***************/
function calculate4ring() 
{        
    var ring1 = parseInt(getValue(document.forms["resistor4ring"].elements["ring1"]));
    var ring2 = parseInt(getValue(document.forms["resistor4ring"].elements["ring2"]));
    var ring3 = parseInt(getValue(document.forms["resistor4ring"].elements["ring3"])) + 1; // exponent
    var ring4 = parseFloat(getValue(document.forms["resistor4ring"].elements["ring4"]));
    
    var unit = Math.floor(ring3 / 3);
    if (unit < 0) unit = 0;
    var value = (ring1 + ring2/10) * Math.pow(10, ring3 % 3);
    var tolerance = ring4;
    
    document.getElementById("resistance4ring").firstChild.nodeValue = Math.round(value*1000)/1000;
    document.getElementById("resistance_unit4ring").firstChild.nodeValue = units[unit] + "Ohm";
    document.getElementById("tolerance4ring").firstChild.nodeValue = tolerance;
}

function reset4ring() 
{
    document.forms["resistor4ring"].elements["ring1"][0].checked = true;
    document.forms["resistor4ring"].elements["ring2"][0].checked = true;
    document.forms["resistor4ring"].elements["ring3"][4].checked = true;
    document.forms["resistor4ring"].elements["ring4"][2].checked = true;
    
    calculate4ring();
}

function calculate6ring() 
{        
    var ring1 = parseInt(getValue(document.forms["resistor6ring"].elements["ring1"]));
    var ring2 = parseInt(getValue(document.forms["resistor6ring"].elements["ring2"]));
    var ring3 = parseInt(getValue(document.forms["resistor6ring"].elements["ring3"]));
    var ring4 = parseInt(getValue(document.forms["resistor6ring"].elements["ring4"])) + 2; // exponent
    var ring5 = parseFloat(getValue(document.forms["resistor6ring"].elements["ring5"]));
    var ring6 = parseInt(getValue(document.forms["resistor6ring"].elements["ring6"]));
    
    var unit = Math.floor(ring4 / 3);
    if (unit < 0) unit = 0;
    var value = (ring1 + ring2/10 + ring3/100) * Math.pow(10, ring4 % 3);
    var tolerance = ring5;
    var tempcoeff = ring6;
    
    document.getElementById("resistance6ring").firstChild.nodeValue = Math.round(value*1000)/1000;
    document.getElementById("resistance_unit6ring").firstChild.nodeValue = units[unit] + "Ohm";
    document.getElementById("tolerance6ring").firstChild.nodeValue = tolerance;
    document.getElementById("tempcoefficient6ring").firstChild.nodeValue = tempcoeff;
    
    if (isNaN(tempcoeff)) {
        document.getElementById("tempcoefficient6ring").style.visibility = 'hidden';
        document.getElementById("tempcoefficient_unit6ring").style.visibility = 'hidden';
    } else {
        document.getElementById("tempcoefficient6ring").style.visibility = 'visible';
        document.getElementById("tempcoefficient_unit6ring").style.visibility = 'visible';
    }
}

function reset6ring() 
{
    document.forms["resistor6ring"].elements["ring1"][0].checked = true;
    document.forms["resistor6ring"].elements["ring2"][0].checked = true;
    document.forms["resistor6ring"].elements["ring3"][0].checked = true;
    document.forms["resistor6ring"].elements["ring4"][4].checked = true;
    document.forms["resistor6ring"].elements["ring5"][0].checked = true;
    document.forms["resistor6ring"].elements["ring6"][0].checked = true;
    
    calculate6ring();
}

/**************** Widerstandsauswahl ***************/
function resistor_reset() 
{
    document.forms["resistor"].elements["resistor_series"][3].checked = true;
    document.forms["resistor"].elements["resistor_input"].value = "";
    document.getElementById("resistor_value").firstChild.nodeValue = "?";
    document.getElementById("resistor_error").firstChild.nodeValue = "?";
    document.getElementById("resistor_unit").firstChild.nodeValue = "Ohm";
}

function resistor_calculate() 
{
    if (document.forms["resistor"].elements["resistor_input"].value == "") {
        return;
    }
    var series = parseInt(getValue(document.forms["resistor"].elements["resistor_series"]));
    var input_r = r_create(document.forms["resistor"].elements["resistor_input"].value);
    
    var current_series = get_series(series);
    var resistor = findBestResistorMatch(input_r, current_series);
    
    document.getElementById("resistor_value").firstChild.nodeValue = round(resistor[0]*Math.pow(10, resistor[1] % 3), 2);
    document.getElementById("resistor_unit").firstChild.nodeValue = units[Math.floor(resistor[1]/3)] + "Ohm";
    document.getElementById("resistor_error").firstChild.nodeValue = round(Math.abs(r_to_ohm(input_r)-r_to_ohm(resistor))/r_to_ohm(input_r)*100, 2);
}

/**************** Widerstandsverhältnis ***************/
function ratio_reset() 
{
    document.forms["ratio"].elements["ratio_series"][3].checked = true;
    document.forms["ratio"].elements["ratio_type"][1].checked = true;
    document.forms["ratio"].elements["ratio_reciprocal"].checked = false;
    document.forms["ratio"].elements["ratio_value"].value = "";
    document.getElementById("ratio_r1_value").firstChild.nodeValue = "?";
    document.getElementById("ratio_r2_value").firstChild.nodeValue = "?";
    document.getElementById("ratio_error").firstChild.nodeValue = "?";
}

function ratio_calculate() 
{
    var series = parseInt(getValue(document.forms["ratio"].elements["ratio_series"]));
    var type = parseInt(getValue(document.forms["ratio"].elements["ratio_type"]));
    var ratio = document.forms["ratio"].elements["ratio_value"].value;
    ratio = parseFloat(ratio.replace(/,/,"."));
    
    if (document.forms["ratio"].elements["ratio_reciprocal"].checked) {
        ratio = 1.0/ratio;
    }
    
    if (type == 2 && ratio >= 1) {
      document.getElementById("ratio_r1_value").firstChild.nodeValue = "?";
      document.getElementById("ratio_r2_value").firstChild.nodeValue = "?";
      document.getElementById("ratio_error").firstChild.nodeValue = "?";
      return;
    }
    
    var current_series = get_series(series);
    
    var best_r1;
    var best_r2;
    var best_error = 1E9;
    var k = Math.floor(Math.log(ratio)/Math.LN10);
    for (var index in current_series) {
        var r1 = current_series[index]*Math.pow(10, k+9);

        var r2;      
        switch(type) {
            case 1:
                r2 = r1 / ratio;
                break;
            case 2:
                r2 = r1 * (1-ratio) / ratio;
                break;
        }

        var resistor = findBestResistorMatch(r_create(r2), current_series);
        r2 = r_to_ohm(resistor);

        var current_ratio;      
        switch(type) {
            case 1:
                current_ratio = r1 / r2;
                break;
            case 2:
                current_ratio = r1 / (r1+r2);
                break;
        }

        var error = current_ratio > ratio ? current_ratio / ratio : ratio / current_ratio;

        if (error < best_error) {
            var tmp;
            if (r1 < r2) {
                tmp = Math.floor(Math.log(r1)/Math.LN10);          
            } else {
                tmp = Math.floor(Math.log(r2)/Math.LN10);
            }
            best_r1 = r1 / Math.pow(10, tmp);
            best_r2 = r2 / Math.pow(10, tmp);
            best_error = error;
        }
    }
    
    document.getElementById("ratio_r1_value").firstChild.nodeValue = round(best_r1, 2);
    document.getElementById("ratio_r2_value").firstChild.nodeValue = round(best_r2, 2);
    document.getElementById("ratio_error").firstChild.nodeValue = round((best_error-1)*100, 2);
}
