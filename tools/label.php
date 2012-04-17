<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Labels</title>
    <link rel="StyleSheet" href="../css/partdb.css" type="text/css">
    <link rel="StyleSheet" href="label/label.css"   type="text/css">
    <script src="label/label.js" type="text/javascript"></script>
</head>
<body class="body" onload="reset4ring(); reset6ring();" >

<div class="outer">
    <h2>Widerstandsrechner</h2>
    <div class="inner">
        <table>
            <tr>
                <td>
                    <form id="resistor4ring" name="resistor4ring">
                        <table class="ringtable" >
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
            </tr>
        </table>
    </div>
</div>


<div class="outer">
    <h2>SMD-Widerst&auml;nde</h2>
    <div class="inner">
        <table>         
            <tr>
               <td><b>3-stellig</b><br>Die ersten beiden Stellen sind der Wert,<br> die letzte Stelle ist der Exponent z.B.</td>
                <td><img src="label/100.png" class="labelbild" alt=""> 10 Ohm</td>
                <td><img src="label/101.png" class="labelbild" alt=""> 100 Ohm</td>
                <td><img src="label/102.png" class="labelbild" alt=""> 1K Ohm</td>
            </tr>
            <tr>
                <td><b>4-stellig</b><br>Die ersten drei Stellen sind der Wert,<br> die letzte Stelle ist der Exponent z.B.</td>
                <td><img src="label/1001.png" class="labelbild" alt=""> 1K Ohm</td>
                <td><img src="label/1002.png" class="labelbild" alt=""> 10K Ohm</td>
                <td><img src="label/1003.png" class="labelbild" alt=""> 100K Ohm</td>
            </tr>
            <tr>
                <td><b>Mehrstellig mit R</b><br>Das R z&auml;hlt als Dezimalpunkt z.B.</td>
                <td><img src="label/R10.png"  class="labelbild" alt=""> 0,10 Ohm</td>
                <td><img src="label/10R2.png" class="labelbild" alt=""> 10,2 Ohm</td>
                <td><img src="label/100R.png" class="labelbild" alt=""> 100 Ohm</td>
            </tr>
        </table>
    </div>
</div>


<div class="outer">
    <h2>SMD-Kondensatoren</h2>
    <div class="inner">
        <table>         
            <tr>
                <td><b>Tantal</b><br>Die ersten beiden Stellen sind der Wert in pF,<br>die letzte der Exponent.<br>Die untere Zahl ist die Spannungsfestigkeit in Volt</td>
                <td><img src="label/246-20.png" class="labelbild" alt="">24&mu;F/20V</td> 
            </tr>
            <tr>
                <td><b>Elkos</b><br>Die Zahlen geben den Wert in &mu;F an,<br>der Buchstabe ist das Trennzeichen,<br> und gibt gleichzeitig die Spannungsfestigkeit an<br>C=6,3V, D=10V, E=16V, F=25V, G=40V, H=63V</td>
                <td><img src="label/3F3.png" class="labelbild" alt="">3,3&mu;F/25V</td> 
            </tr>
            <tr>
                <td><b>ALU-Elko</b><br>Der obere Wert ist die Kapazitiv in &mu;F,<br>der untere die Spannung in Volt.</td>
                <td><img src="label/220.png" class="labelbild" alt=""></td>
            </tr>
        </table>
    </div>
</div>


<div class="outer">
    <h2>SMD-Spulen</h2>
    <div class="inner">
        <table>         
            <tr>
            <td><b>DO23-Spule</b><br>Der Buchstabe ist die Toleranz<br>und gleichzeitig das "," <br>die Werte werden in &mu;H angegeben<br>F=1%, G=2%, J=5%, K=10% M=20%</td>
            <td><img src="label/221K.png" class="labelbild" alt="">221&mu;H/5%</td> 
            </tr>
        </table>
    </div>
</div>


<!-- TODO
        
        <b>SMD-Dioden</b><br>
        <br>
        <br>
        <b>SMD-Transistoren</b><br>
        <br>
        <br>
        <b>SMD-LEDs</b><br>
        <br>
        <br>
        <b>SMD-ICs</b><br>
        <br>
        <br>
        -->

</body>
</html>
