<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Widerstandsrechner</title>
    <link rel="StyleSheet" href="../css/partdb.css" type="text/css">
    <link rel="StyleSheet" href="rechner.css"       type="text/css">
    <script src="rechner.js" type="text/javascript"></script>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<body class="body" onload="reset4ring(); reset6ring(); ratio_reset(); resistor_reset()" >

<div class="outer">
    <h2>Widerstandsrechner</h2>
    <div class="inner">
        <table><tr>
                <td>
                    <form id="resistor4ring" name="resistor4ring">
                        <table class="ringtable">
                            <thead>
                                <tr>
                                    <th>1. Ring</th>
                                    <th>2. Ring</th>
                                    <th>3. Ring</th>
                                    <th>4. Ring</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="ring_none">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input type="radio" value="20" name="ring4" onclick="calculate4ring()" />kein</td>
                                </tr>
                                <tr class="ring_silver">
                                    <td></td>
                                    <td></td>
                                    <td><input type="radio" value="-2" name="ring3" onclick="calculate4ring()" />silber</td>
                                    <td><input type="radio" value="10" name="ring4" onclick="calculate4ring()" />silber</td>
                                </tr>
                                <tr class="ring_gold">
                                    <td></td>
                                    <td></td>
                                    <td><input type="radio" value="-1" name="ring3" onclick="calculate4ring()" />gold</td>
                                    <td><input type="radio" value="5" name="ring4" onclick="calculate4ring()" />gold</td>
                                </tr>
                                <tr class="ring_black">
                                    <td></td>
                                    <td><input type="radio" value="0" name="ring2" onclick="calculate4ring()" />schwarz</td>
                                    <td><input type="radio" value="0" name="ring3" onclick="calculate4ring()" />schwarz</td>
                                    <td></td>
                                </tr>
                                <tr class="ring_brown">
                                    <td><input type="radio" value="1" name="ring1" onclick="calculate4ring()" />braun</td>
                                    <td><input type="radio" value="1" name="ring2" onclick="calculate4ring()" />braun</td>
                                    <td><input type="radio" value="1" name="ring3" onclick="calculate4ring()" />braun</td>
                                    <td><input type="radio" value="1" name="ring4" onclick="calculate4ring()" />braun</td>
                                </tr>
                                <tr class="ring_red">
                                    <td><input type="radio" value="2" name="ring1" onclick="calculate4ring()" />rot</td>
                                    <td><input type="radio" value="2" name="ring2" onclick="calculate4ring()" />rot</td>
                                    <td><input type="radio" value="2" name="ring3" onclick="calculate4ring()" />rot</td>
                                    <td><input type="radio" value="2" name="ring4" onclick="calculate4ring()" />rot</td>
                                </tr>
                                <tr class="ring_orange">
                                    <td><input type="radio" value="3" name="ring1" onclick="calculate4ring()" />orange</td>
                                    <td><input type="radio" value="3" name="ring2" onclick="calculate4ring()"  />orange</td>
                                    <td><input type="radio" value="3" name="ring3" onclick="calculate4ring()" />orange</td>
                                    <td></td>
                                </tr>
                                <tr class="ring_yellow">
                                    <td><input type="radio" value="4" name="ring1" onclick="calculate4ring()" />gelb</td>
                                    <td><input type="radio" value="4" name="ring2" onclick="calculate4ring()" />gelb</td>
                                    <td><input type="radio" value="4" name="ring3" onclick="calculate4ring()" />gelb</td>
                                    <td></td>
                                </tr>
                                <tr class="ring_green">
                                    <td><input type="radio" value="5" name="ring1" onclick="calculate4ring()" />gr&uuml;n</td>
                                    <td><input type="radio" value="5" name="ring2" onclick="calculate4ring()" />gr&uuml;n</td>
                                    <td><input type="radio" value="5" name="ring3" onclick="calculate4ring()" />gr&uuml;n</td>
                                    <td><input type="radio" value="0.5" name="ring4" onclick="calculate4ring()" />gr&uuml;n</td>
                                </tr>
                                <tr class="ring_blue">
                                    <td><input type="radio" value="6" name="ring1" onclick="calculate4ring()" />blau</td>
                                    <td><input type="radio" value="6" name="ring2" onclick="calculate4ring()" />blau</td>
                                    <td><input type="radio" value="6" name="ring3" onclick="calculate4ring()" />blau</td>
                                    <td><input type="radio" value="0.25" name="ring4" onclick="calculate4ring()" />blau</td>
                                </tr>
                                <tr class="ring_violet">
                                    <td><input type="radio" value="7" name="ring1" onclick="calculate4ring()" />violett</td>
                                    <td><input type="radio" value="7" name="ring2" onclick="calculate4ring()" />violett</td>
                                    <td><input type="radio" value="7" name="ring3" onclick="calculate4ring()" />violett</td>
                                    <td><input type="radio" value="0.1" name="ring4" onclick="calculate4ring()" />violett</td>
                                </tr>
                                <tr class="ring_gray">
                                    <td><input type="radio" value="8" name="ring1" onclick="calculate4ring()" />grau</td>
                                    <td><input type="radio" value="8" name="ring2" onclick="calculate4ring()" />grau</td>
                                    <td><input type="radio" value="8" name="ring3" onclick="calculate4ring()" />grau</td>
                                    <td><input type="radio" value="0.05" name="ring4" onclick="calculate4ring()" />grau</td>
                                </tr>
                                <tr class="ring_white">
                                    <td><input type="radio" value="9" name="ring1" onclick="calculate4ring()" />wei&szlig;</td>
                                    <td><input type="radio" value="9" name="ring2" onclick="calculate4ring()"  />wei&szlig;</td>
                                    <td><input type="radio" value="9" name="ring3" onclick="calculate4ring()" />wei&szlig;</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <p></p>
                        <table class="blind">
                            <tr>
                                <td>Widerstand:</td>
                                <td><span id="resistance4ring" class="value">100</span><span id="resistance_unit4ring" class="unit">Ohm</span></td>
                            </tr>
                            <tr>
                                <td>Toleranz:</td>
                                <td><span id="tolerance4ring" class="value">20</span><span id="tolerance_unit4ring" class="unit">%</span></td>
                            </tr>
                        </table>
                        <input type="button" onclick="reset4ring()" value="R&uuml;cksetzten" />
                    </form>
                </td>
                <td>
                    <form id="resistor6ring" name="resistor6ring">
                        <table class="ringtable" >
                            <thead>
                                <tr>
                                    <th>1. Ring</th>
                                    <th>2. Ring</th>
                                    <th>3. Ring</th>
                                    <th>4. Ring</th>
                                    <th>5. Ring</th>
                                    <th>6. Ring</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="ring_none">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input type="radio" value="" name="ring6" onclick="calculate6ring()" />kein</td>
                                </tr>
                                <tr class="ring_silver">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input type="radio" value="-2" name="ring4" onclick="calculate6ring()" />silber</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr class="ring_gold">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input type="radio" value="-1" name="ring4" onclick="calculate6ring()" />gold</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr class="ring_black">
                                    <td></td>
                                    <td><input type="radio" value="0" name="ring2" onclick="calculate6ring()" />schwarz</td>
                                    <td><input type="radio" value="0" name="ring3" onclick="calculate6ring()" />schwarz</td>
                                    <td><input type="radio" value="0" name="ring4" onclick="calculate6ring()" />schwarz</td>
                                    <td></td>
                                    <td><input type="radio" value="200" name="ring6" onclick="calculate6ring()" />schwarz</td>
                                </tr>
                                <tr class="ring_brown">
                                    <td><input type="radio" value="1" name="ring1" onclick="calculate6ring()" />braun</td>
                                    <td><input type="radio" value="1" name="ring2" onclick="calculate6ring()" />braun</td>
                                    <td><input type="radio" value="1" name="ring3" onclick="calculate6ring()" />braun</td>
                                    <td><input type="radio" value="1" name="ring4" onclick="calculate6ring()" />braun</td>
                                    <td><input type="radio" value="1" name="ring5" onclick="calculate6ring()" />braun</td>
                                    <td><input type="radio" value="100" name="ring6" onclick="calculate6ring()" />braun</td>
                                </tr>
                                <tr class="ring_red">
                                    <td><input type="radio" value="2" name="ring1" onclick="calculate6ring()" />rot</td>
                                    <td><input type="radio" value="2" name="ring2" onclick="calculate6ring()" />rot</td>
                                    <td><input type="radio" value="2" name="ring3" onclick="calculate6ring()" />rot</td>
                                    <td><input type="radio" value="2" name="ring4" onclick="calculate6ring()" />rot</td>
                                    <td><input type="radio" value="2" name="ring5" onclick="calculate6ring()" />rot</td>
                                    <td><input type="radio" value="50" name="ring6" onclick="calculate6ring()" />rot</td>
                                </tr>
                                <tr class="ring_orange">
                                    <td><input type="radio" value="3" name="ring1" onclick="calculate6ring()" />orange</td>
                                    <td><input type="radio" value="3" name="ring2" onclick="calculate6ring()" />orange</td>
                                    <td><input type="radio" value="3" name="ring3" onclick="calculate6ring()" />orange</td>
                                    <td><input type="radio" value="3" name="ring4" onclick="calculate6ring()" />orange</td>
                                    <td></td>
                                    <td><input type="radio" value="15" name="ring6" onclick="calculate6ring()" />orange</td>
                                </tr>
                                <tr class="ring_yellow">
                                    <td><input type="radio" value="4" name="ring1" onclick="calculate6ring()" />gelb</td>
                                    <td><input type="radio" value="4" name="ring2" onclick="calculate6ring()" />gelb</td>
                                    <td><input type="radio" value="4" name="ring3" onclick="calculate6ring()" />gelb</td>
                                    <td><input type="radio" value="4" name="ring4" onclick="calculate6ring()" />gelb</td>
                                    <td></td>
                                    <td><input type="radio" value="25" name="ring6" onclick="calculate6ring()" />gelb</td>
                                </tr>
                                <tr class="ring_green">
                                    <td><input type="radio" value="5" name="ring1" onclick="calculate6ring()" />gr&uuml;n</td>
                                    <td><input type="radio" value="5" name="ring2" onclick="calculate6ring()" />gr&uuml;n</td>
                                    <td><input type="radio" value="5" name="ring3" onclick="calculate6ring()" />gr&uuml;n</td>
                                    <td><input type="radio" value="5" name="ring4" onclick="calculate6ring()" />gr&uuml;n</td>
                                    <td><input type="radio" value="0.5" name="ring5" onclick="calculate6ring()" />gr&uuml;n</td>
                                    <td></td>
                                </tr>
                                <tr class="ring_blue">
                                    <td><input type="radio" value="6" name="ring1" onclick="calculate6ring()" />blau</td>
                                    <td><input type="radio" value="6" name="ring2" onclick="calculate6ring()" />blau</td>
                                    <td><input type="radio" value="6" name="ring3" onclick="calculate6ring()" />blau</td>
                                    <td><input type="radio" value="6" name="ring4" onclick="calculate6ring()" />blau</td>
                                    <td><input type="radio" value="0.25" name="ring5" onclick="calculate6ring()" />blau</td>
                                    <td><input type="radio" value="10" name="ring6" onclick="calculate6ring()" />blau</td>
                                </tr>
                                <tr class="ring_violet">
                                    <td><input type="radio" value="7" name="ring1" onclick="calculate6ring()" />violett</td>
                                    <td><input type="radio" value="7" name="ring2" onclick="calculate6ring()" />violett</td>
                                    <td><input type="radio" value="7" name="ring3" onclick="calculate6ring()" />violett</td>
                                    <td></td>
                                    <td><input type="radio" value="0.1" name="ring5" onclick="calculate6ring()" />violett</td>
                                    <td><input type="radio" value="5" name="ring6" onclick="calculate6ring()" />violett</td>
                                </tr>
                                <tr class="ring_gray">
                                    <td><input type="radio" value="8" name="ring1" onclick="calculate6ring()" />grau</td>
                                    <td><input type="radio" value="8" name="ring2" onclick="calculate6ring()" />grau</td>
                                    <td><input type="radio" value="8" name="ring3" onclick="calculate6ring()" />grau</td>
                                    <td></td>
                                    <td><input type="radio" value="0.05" name="ring5" onclick="calculate6ring()" />grau</td>
                                    <td></td>
                                </tr>
                                <tr class="ring_white">
                                    <td><input type="radio" value="9" name="ring1" onclick="calculate6ring()" />wei&szlig;</td>
                                    <td><input type="radio" value="9" name="ring2" onclick="calculate6ring()" />wei&szlig;</td>
                                    <td><input type="radio" value="9" name="ring3" onclick="calculate6ring()" />wei&szlig;</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <p></p>
                        <table class="blind">
                            <tr>
                                <td>Widerstand:</td>
                                <td><span id="resistance6ring" class="value">100</span><span id="resistance_unit6ring" class="unit">Ohm</span></td>
                            </tr>
                            <tr>
                                <td>Toleranz:</td>
                                <td><span id="tolerance6ring" class="value">20</span><span id="tolerance_unit6ring" class="unit">%</span></td>
                            </tr>
                            <tr>
                                <td>Temperaturkoeffizient:</td>
                                <td><span id="tempcoefficient6ring" class="value">20</span><span id="tempcoefficient_unit6ring" class="unit">&sdot; 10<sup>-6</sup> K<sup>-1</sup></span></td>
                            </tr>
                        </table>
                        <input type="button" onclick="reset6ring()" value="R&uuml;cksetzten" />
                    </form>
                </td>
        </tr></table>
    </div>
</div>

<div class="outer">
    <h2>Widerstand w&auml;hlen</h2>
    <div class="inner">
        <form id="resistor" name="resistor">
            <p><b>Widerstandsreihe:</b><br/>
              <span><input type="radio" value="3"   name="resistor_series" onclick="resistor_calculate()" />E3</span>
              <span><input type="radio" value="6"   name="resistor_series" onclick="resistor_calculate()" />E6</span>
              <span><input type="radio" value="12"  name="resistor_series" onclick="resistor_calculate()" />E12</span>
              <span><input type="radio" value="24"  name="resistor_series" onclick="resistor_calculate()" />E24</span>
              <span><input type="radio" value="48"  name="resistor_series" onclick="resistor_calculate()" />E48</span>
              <span><input type="radio" value="96"  name="resistor_series" onclick="resistor_calculate()" />E96</span>
              <span><input type="radio" value="192" name="resistor_series" onclick="resistor_calculate()" />E192</span>
          </p>
            <p>Widerstandswert: <input type="text" name="resistor_input" size="10"/></p>
            <p>Beste Wahl: <span id="resistor_value">?</span><span id="resistor_unit" class="unit">Ohm</span></p>
            <p>Fehler: <span id="resistor_error">?</span><span class="unit">%</span></p>
            <p><input type="button" value="Berechne" onclick="resistor_calculate()" /></p>
        </form>
    </div>
</div>

<div class="outer">
    <h2>Widerstandsverh&auml;ltnis</h2>
    <div class="inner">
      <table class="blind"><tr>
        <td>
          <img src="rechner/ratio.png" alt="Spannungsteiler"/>
        </td>
        <td>
          <form id="ratio" name="ratio">
              <p><b>Widerstandsreihe:</b><br/>
                  <span><input type="radio" value="3"   name="ratio_series" onclick="ratio_calculate()" />E3</span>
                  <span><input type="radio" value="6"   name="ratio_series" onclick="ratio_calculate()" />E6</span>
                  <span><input type="radio" value="12"  name="ratio_series" onclick="ratio_calculate()" />E12</span>
                  <span><input type="radio" value="24"  name="ratio_series" onclick="ratio_calculate()" />E24</span>
                  <span><input type="radio" value="48"  name="ratio_series" onclick="ratio_calculate()" />E48</span>
                  <span><input type="radio" value="96"  name="ratio_series" onclick="ratio_calculate()" />E96</span>
                  <span><input type="radio" value="192" name="ratio_series" onclick="ratio_calculate()" />E192</span>
              </p>
              <p><b>Typ:</b><br/>
                      <span><input type="radio" value="1" name="ratio_type" onclick="ratio_calculate()" />Verh&auml;nis <img src="rechner/v1.png" alt="V=R1/R2"/></span>
                      <span><input type="radio" value="2" name="ratio_type" onclick="ratio_calculate()" />Spannungsteiler <img src="rechner/v2.png" alt="V=R1/(R1+R2)"/></span>
              </p>
              <p>
                  <b>Verh&auml;ltnis:</b> <input type="text" value="" name="ratio_value" size="10"/> <span><input type="checkbox" name="ratio_reciprocal" onclick="ratio_calculate()"/> Kehrwert</span>
              </p>
              <p>
                  <span>R<sub>1</sub>=<span id="ratio_r1_value">?</span><span id="ratio_r1_unit" class="unit">kOhm</span><br/>
                  <span>R<sub>2</sub>=<span id="ratio_r2_value">?</span><span id="ratio_r2_unit" class="unit">kOhm</span><br/>
                  <span>Fehler: <span id="ratio_error">?</span><span id="ratio_error_unit" class="unit">%</span>
              </p>
              <p>
                  <input type="button" value="Berechne" onclick="ratio_calculate()"/>
              </p>
          </form>
        </td>
      </tr></table>
    </div>
</div>


</body>
</html>
