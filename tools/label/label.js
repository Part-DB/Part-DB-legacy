function getValue(radioButtons) {
    for (var i = 0; i < radioButtons.length; ++i) {
        if (radioButtons[i].checked) {
            return radioButtons[i].value;
        }
    }
}

units = new Array("", "k", "M", "G");

function calculate4ring() {        
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

function reset4ring() {
    document.forms["resistor4ring"].elements["ring1"][0].checked = true;
    document.forms["resistor4ring"].elements["ring2"][0].checked = true;
    document.forms["resistor4ring"].elements["ring3"][4].checked = true;
    document.forms["resistor4ring"].elements["ring4"][2].checked = true;
    
    calculate4ring();
}

function calculate6ring() {        
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
    var tempcoeff = ring6
    
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

function reset6ring() {
    document.forms["resistor6ring"].elements["ring1"][0].checked = true;
    document.forms["resistor6ring"].elements["ring2"][0].checked = true;
    document.forms["resistor6ring"].elements["ring3"][0].checked = true;
    document.forms["resistor6ring"].elements["ring4"][4].checked = true;
    document.forms["resistor6ring"].elements["ring5"][0].checked = true;
    document.forms["resistor6ring"].elements["ring6"][0].checked = true;
    
    calculate6ring();
}
