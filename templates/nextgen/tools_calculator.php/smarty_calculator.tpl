{locale path="nextgen/locale" domain="partdb"}

<!-- Always include CSS for Calculator. Maybe minimize this later for better performance -->
<link rel="stylesheet" href="{$relative_path}css/calculator.css" type="text/css">



<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-calculator" aria-hidden="true"></i> {t}Widerstandsrechner{/t}</div>
    <div class="panel-body">
        <div class="col-md-6">
            <form id="resistor4ring" name="resistor4ring">
                <table class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>{t}1. Ring{/t}</th>
                            <th>{t}2. Ring{/t}</th>
                            <th>{t}3. Ring{/t}</th>
                            <th>{t}4. Ring{/t}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="ring_none">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><input type="radio" value="20" name="ring4" onclick="calculate4ring()" />{t}kein{/t}</td>
                        </tr>
                        <tr class="ring_silver">
                            <td></td>
                            <td></td>
                            <td><input type="radio" value="-2" name="ring3" onclick="calculate4ring()" />{t}silber{/t}</td>
                            <td><input type="radio" value="10" name="ring4" onclick="calculate4ring()" />{t}silber{/t}</td>
                        </tr>
                        <tr class="ring_gold">
                            <td></td>
                            <td></td>
                            <td><input type="radio" value="-1" name="ring3" onclick="calculate4ring()" />{t}gold{/t}</td>
                            <td><input type="radio" value="5" name="ring4" onclick="calculate4ring()" />{t}gold{/t}</td>
                        </tr>
                        <tr class="ring_black">
                            <td></td>
                            <td><input type="radio" value="0" name="ring2" onclick="calculate4ring()" />{t}schwarz{/t}</td>
                            <td><input type="radio" value="0" name="ring3" onclick="calculate4ring()" />{t}schwarz{/t}</td>
                            <td></td>
                        </tr>
                        <tr class="ring_brown">
                            <td><input type="radio" value="1" name="ring1" onclick="calculate4ring()" />{t}braun{/t}</td>
                            <td><input type="radio" value="1" name="ring2" onclick="calculate4ring()" />{t}braun{/t}</td>
                            <td><input type="radio" value="1" name="ring3" onclick="calculate4ring()" />{t}braun{/t}</td>
                            <td><input type="radio" value="1" name="ring4" onclick="calculate4ring()" />{t}braun{/t}</td>
                        </tr>
                        <tr class="ring_red">
                            <td><input type="radio" value="2" name="ring1" onclick="calculate4ring()" />{t}rot{/t}</td>
                            <td><input type="radio" value="2" name="ring2" onclick="calculate4ring()" />{t}rot{/t}</td>
                            <td><input type="radio" value="2" name="ring3" onclick="calculate4ring()" />{t}rot{/t}</td>
                            <td><input type="radio" value="2" name="ring4" onclick="calculate4ring()" />{t}rot{/t}</td>
                        </tr>
                        <tr class="ring_orange">
                            <td><input type="radio" value="3" name="ring1" onclick="calculate4ring()" />{t}orange{/t}</td>
                            <td><input type="radio" value="3" name="ring2" onclick="calculate4ring()"  />{t}orange{/t}</td>
                            <td><input type="radio" value="3" name="ring3" onclick="calculate4ring()" />{t}orange{/t}</td>
                            <td></td>
                        </tr>
                        <tr class="ring_yellow">
                            <td><input type="radio" value="4" name="ring1" onclick="calculate4ring()" />{t}gelb{/t}</td>
                            <td><input type="radio" value="4" name="ring2" onclick="calculate4ring()" />{t}gelb{/t}</td>
                            <td><input type="radio" value="4" name="ring3" onclick="calculate4ring()" />{t}gelb{/t}</td>
                            <td></td>
                        </tr>
                        <tr class="ring_green">
                            <td><input type="radio" value="5" name="ring1" onclick="calculate4ring()" />{t}grün{/t}</td>
                            <td><input type="radio" value="5" name="ring2" onclick="calculate4ring()" />{t}grün{/t}</td>
                            <td><input type="radio" value="5" name="ring3" onclick="calculate4ring()" />{t}grün{/t}</td>
                            <td><input type="radio" value="0.5" name="ring4" onclick="calculate4ring()" />{t}grün{/t}</td>
                        </tr>
                        <tr class="ring_blue">
                            <td><input type="radio" value="6" name="ring1" onclick="calculate4ring()" />{t}blau{/t}</td>
                            <td><input type="radio" value="6" name="ring2" onclick="calculate4ring()" />{t}blau{/t}</td>
                            <td><input type="radio" value="6" name="ring3" onclick="calculate4ring()" />{t}blau{/t}</td>
                            <td><input type="radio" value="0.25" name="ring4" onclick="calculate4ring()" />{t}blau{/t}</td>
                        </tr>
                        <tr class="ring_violet">
                            <td><input type="radio" value="7" name="ring1" onclick="calculate4ring()" />{t}violett{/t}</td>
                            <td><input type="radio" value="7" name="ring2" onclick="calculate4ring()" />{t}violett{/t}</td>
                            <td><input type="radio" value="7" name="ring3" onclick="calculate4ring()" />{t}violett{/t}</td>
                            <td><input type="radio" value="0.1" name="ring4" onclick="calculate4ring()" />{t}violett{/t}</td>
                        </tr>
                        <tr class="ring_gray">
                            <td><input type="radio" value="8" name="ring1" onclick="calculate4ring()" />{t}grau{/t}</td>
                            <td><input type="radio" value="8" name="ring2" onclick="calculate4ring()" />{t}grau{/t}</td>
                            <td><input type="radio" value="8" name="ring3" onclick="calculate4ring()" />{t}grau{/t}</td>
                            <td><input type="radio" value="0.05" name="ring4" onclick="calculate4ring()" />{t}grau{/t}</td>
                        </tr>
                        <tr class="ring_white">
                            <td><input type="radio" value="9" name="ring1" onclick="calculate4ring()" />{t}weiß{/t}</td>
                            <td><input type="radio" value="9" name="ring2" onclick="calculate4ring()"  />{t}weiß{/t}</td>
                            <td><input type="radio" value="9" name="ring3" onclick="calculate4ring()" />{t}weiß{/t}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div class="">
                    <div class="row">
                        <label class=" col-md-3">{t}Widerstand:{/t}</label>
                        <p class="col-md-9"><span id="resistance4ring" class="value">100</span><span id="resistance_unit4ring" class="unit">{t}Ohm{/t}</span></p>
                    </div>
                    <div class="row">
                        <label class="col-md-3">{t}Toleranz{/t}</label>
                        <p class="col-md-9"><span id="tolerance4ring" class="value">20</span><span id="tolerance_unit4ring" class="unit">%</span></p>
                    </div>
                    <div class="row">
                        <div class="col-md-9">
                            <button class="btn btn-default" type="button" onclick="reset4ring()">{t}Rücksetzen{/t}</button>
                        </div>
                    </div>
                </div>
            </form>
            </div>
            <div class="col-md-6">
                    <form id="resistor6ring" name="resistor6ring">
                        <table class="table table-bordered table-condensed" >
                            <thead>
                                <tr>
                                    <th>{t}1. Ring{/t}</th>
                                    <th>{t}2. Ring{/t}</th>
                                    <th>{t}3. Ring{/t}</th>
                                    <th>{t}4. Ring{/t}</th>
                                    <th>{t}5. Ring{/t}</th>
                                    <th>{t}6. Ring{/t}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="ring_none">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input type="radio" value="" name="ring6" onclick="calculate6ring()" />{t}kein{/t}</td>
                                </tr>
                                <tr class="ring_silver">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input type="radio" value="-2" name="ring4" onclick="calculate6ring()" />{t}silber{/t}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr class="ring_{t}gold{/t}">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input type="radio" value="-1" name="ring4" onclick="calculate6ring()" />{t}gold{/t}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr class="ring_black">
                                    <td></td>
                                    <td><input type="radio" value="0" name="ring2" onclick="calculate6ring()" />{t}schwarz{/t}</td>
                                    <td><input type="radio" value="0" name="ring3" onclick="calculate6ring()" />{t}schwarz{/t}</td>
                                    <td><input type="radio" value="0" name="ring4" onclick="calculate6ring()" />{t}schwarz{/t}</td>
                                    <td></td>
                                    <td><input type="radio" value="200" name="ring6" onclick="calculate6ring()" />{t}schwarz{/t}</td>
                                </tr>
                                <tr class="ring_brown">
                                    <td><input type="radio" value="1" name="ring1" onclick="calculate6ring()" />{t}braun{/t}</td>
                                    <td><input type="radio" value="1" name="ring2" onclick="calculate6ring()" />{t}braun{/t}</td>
                                    <td><input type="radio" value="1" name="ring3" onclick="calculate6ring()" />{t}braun{/t}</td>
                                    <td><input type="radio" value="1" name="ring4" onclick="calculate6ring()" />{t}braun{/t}</td>
                                    <td><input type="radio" value="1" name="ring5" onclick="calculate6ring()" />{t}braun{/t}</td>
                                    <td><input type="radio" value="100" name="ring6" onclick="calculate6ring()" />{t}braun{/t}</td>
                                </tr>
                                <tr class="ring_red">
                                    <td><input type="radio" value="2" name="ring1" onclick="calculate6ring()" />{t}rot{/t}</td>
                                    <td><input type="radio" value="2" name="ring2" onclick="calculate6ring()" />{t}rot{/t}</td>
                                    <td><input type="radio" value="2" name="ring3" onclick="calculate6ring()" />{t}rot{/t}</td>
                                    <td><input type="radio" value="2" name="ring4" onclick="calculate6ring()" />{t}rot{/t}</td>
                                    <td><input type="radio" value="2" name="ring5" onclick="calculate6ring()" />{t}rot{/t}</td>
                                    <td><input type="radio" value="50" name="ring6" onclick="calculate6ring()" />{t}rot{/t}</td>
                                </tr>
                                <tr class="ring_orange">
                                    <td><input type="radio" value="3" name="ring1" onclick="calculate6ring()" />{t}orange{/t}</td>
                                    <td><input type="radio" value="3" name="ring2" onclick="calculate6ring()" />{t}orange{/t}</td>
                                    <td><input type="radio" value="3" name="ring3" onclick="calculate6ring()" />{t}orange{/t}</td>
                                    <td><input type="radio" value="3" name="ring4" onclick="calculate6ring()" />{t}orange{/t}</td>
                                    <td></td>
                                    <td><input type="radio" value="15" name="ring6" onclick="calculate6ring()" />{t}orange{/t}</td>
                                </tr>
                                <tr class="ring_yellow">
                                    <td><input type="radio" value="4" name="ring1" onclick="calculate6ring()" />{t}gelb{/t}</td>
                                    <td><input type="radio" value="4" name="ring2" onclick="calculate6ring()" />{t}gelb{/t}</td>
                                    <td><input type="radio" value="4" name="ring3" onclick="calculate6ring()" />{t}gelb{/t}</td>
                                    <td><input type="radio" value="4" name="ring4" onclick="calculate6ring()" />{t}gelb{/t}</td>
                                    <td></td>
                                    <td><input type="radio" value="25" name="ring6" onclick="calculate6ring()" />{t}gelb{/t}</td>
                                </tr>
                                <tr class="ring_green">
                                    <td><input type="radio" value="5" name="ring1" onclick="calculate6ring()" />{t}grün{/t}</td>
                                    <td><input type="radio" value="5" name="ring2" onclick="calculate6ring()" />{t}grün{/t}</td>
                                    <td><input type="radio" value="5" name="ring3" onclick="calculate6ring()" />{t}grün{/t}</td>
                                    <td><input type="radio" value="5" name="ring4" onclick="calculate6ring()" />{t}grün{/t}</td>
                                    <td><input type="radio" value="0.5" name="ring5" onclick="calculate6ring()" />{t}grün{/t}</td>
                                    <td></td>
                                </tr>
                                <tr class="ring_blue">
                                    <td><input type="radio" value="6" name="ring1" onclick="calculate6ring()" />{t}blau{/t}</td>
                                    <td><input type="radio" value="6" name="ring2" onclick="calculate6ring()" />{t}blau{/t}</td>
                                    <td><input type="radio" value="6" name="ring3" onclick="calculate6ring()" />{t}blau{/t}</td>
                                    <td><input type="radio" value="6" name="ring4" onclick="calculate6ring()" />{t}blau{/t}</td>
                                    <td><input type="radio" value="0.25" name="ring5" onclick="calculate6ring()" />{t}blau{/t}</td>
                                    <td><input type="radio" value="10" name="ring6" onclick="calculate6ring()" />{t}blau{/t}</td>
                                </tr>
                                <tr class="ring_violet">
                                    <td><input type="radio" value="7" name="ring1" onclick="calculate6ring()" />{t}violett{/t}</td>
                                    <td><input type="radio" value="7" name="ring2" onclick="calculate6ring()" />{t}violett{/t}</td>
                                    <td><input type="radio" value="7" name="ring3" onclick="calculate6ring()" />{t}violett{/t}</td>
                                    <td></td>
                                    <td><input type="radio" value="0.1" name="ring5" onclick="calculate6ring()" />{t}violett{/t}</td>
                                    <td><input type="radio" value="5" name="ring6" onclick="calculate6ring()" />{t}violett{/t}</td>
                                </tr>
                                <tr class="ring_gray">
                                    <td><input type="radio" value="8" name="ring1" onclick="calculate6ring()" />{t}grau{/t}</td>
                                    <td><input type="radio" value="8" name="ring2" onclick="calculate6ring()" />{t}grau{/t}</td>
                                    <td><input type="radio" value="8" name="ring3" onclick="calculate6ring()" />{t}grau{/t}</td>
                                    <td></td>
                                    <td><input type="radio" value="0.05" name="ring5" onclick="calculate6ring()" />{t}grau{/t}</td>
                                    <td></td>
                                </tr>
                                <tr class="ring_white">
                                    <td><input type="radio" value="9" name="ring1" onclick="calculate6ring()" />{t}weiß{/t}</td>
                                    <td><input type="radio" value="9" name="ring2" onclick="calculate6ring()" />{t}weiß{/t}</td>
                                    <td><input type="radio" value="9" name="ring3" onclick="calculate6ring()" />{t}weiß{/t}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="">
                           <div class="row"
                            <div class="row">
                                <label class="col-md-4">{t}Widerstand:{/t}</label>
                                <p class="col-md-8"><span id="resistance6ring" class="value">100</span><span id="resistance_unit6ring" class="unit">{t}Ohm{/t}</span></p>
                            </div>
                            <div class="row">
                                <label class="col-md-4">{t}Toleranz:{/t}</label>
                                <p class="col-md-8"><span id="tolerance6ring" class="value">20</span><span id="tolerance_unit6ring" class="unit">%</span></p>
                            </div>
                            <div class="row">
                                <label class="col-md-4">{t}Temperaturkoeffizient:{/t}</label>
                                <p class="col-md-8"><span id="tempcoefficient6ring" class="value">20</span><span id="tempcoefficient_unit6ring" class="unit">&sdot; 10<sup>-6</sup> K<sup>-1</sup></span></p>
                            </div>
                            <div class="row">
                                <div class="col-md-9">
                                    <button class="btn btn-default" type="button" onclick="reset6ring()">{t}Rücksetzten{/t}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">{t}Widerstand wählen{/t}</div>
    <div class="panel-body">
        <form id="resistor" name="resistor">
            <div class="form-group">
                <label>{t}Widerstandsreihe:{/t}</label>
                <div>
                    <div class="radio radio-inline">
                        <input type="radio" value="3" name="resistor_series" onclick="resistor_calculate()"/>
                        <label>E3</label>
                        </div>
                    <div class="radio radio-inline">
                        <input type="radio" value="6" name="resistor_series" onclick="resistor_calculate()" />
                        <label>E6</label>
                    </div>
                    <div class="radio radio-inline">
                        <input type="radio" value="12" name="resistor_series" onclick="resistor_calculate()" />
                        <label>E12</label>
                    </div>
                    <div class="radio radio-inline">
                        <input type="radio" value="24"  name="resistor_series" onclick="resistor_calculate()"/>
                        <label>E24</label>
                    </div>
                    <div class="radio radio-inline">
                        <input type="radio" value="48"  name="resistor_series" onclick="resistor_calculate()"/>
                        <label>E48</label>
                    </div>
                    <div class="radio radio-inline">
                        <input type="radio" value="96"  name="resistor_series" onclick="resistor_calculate()" />
                        <label>E96</label>
                    </div>
                    <div class="radio radio-inline">
                        <input type="radio" value="192" name="resistor_series" onclick="resistor_calculate()"/>
                        <label>E192</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>{t}Widerstandswert:{/t}</label>
                <input type="number" min="0" class="form-control" name="resistor_input">
            </div>
            <div class="form-group">
                <p>{t}Beste Wahl:{/t} <span id="resistor_value">?</span><span id="resistor_unit" class="unit">{t}Ohm{/t}</span></p>
            </div>
            <div class="form-goup">
                <p>{t}Fehler:{/t} <span id="resistor_error">?</span><span class="unit">%</span></p>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary" onclick="resistor_calculate()">{t}Berechne{/t}</button>
            </div>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">{t}Widerstandsverhältnis{/t}</div>
    <div class="panel-body">
       <div class="col-md-2">
            <img src="{$relative_path}img/calculator/ratio.png" alt="{t}Spannungsteiler{/t}"/>
        </div>
        <div class="col-md-10">
            <form id="ratio" name="ratio"> 
                <div class="form-group">
                    <label>{t}Widerstandsreihe:{/t}</label>
                    <div>
                        <div class="radio radio-inline">
                            <input type="radio" value="3" name="ratio_series" onclick="ratio_calculate()" />
                            <label>E3</label>
                        </div>
                        <div class="radio radio-inline">
                            <input type="radio" value="6"   name="ratio_series" onclick="ratio_calculate()"/>
                            <label>E6</label>
                        </div>
                        <div class="radio radio-inline">
                            <input type="radio" value="12"  name="ratio_series" onclick="ratio_calculate()"/>
                            <label>E12</label>
                        </div>
                        <div class="radio radio-inline">
                            <input type="radio" value="24"  name="ratio_series" onclick="ratio_calculate()" />
                            <label>E24</label>
                        </div>
                        <div class="radio radio-inline">
                             <input type="radio" value="48"  name="ratio_series" onclick="ratio_calculate()" />
                             <label>E48</label>
                        </div>
                        <div class="radio radio-inline">    
                             <input type="radio" value="96"  name="ratio_series" onclick="ratio_calculate()" />
                             <label>E96</label>
                        </div>
                        <div class="radio radio-inline">
                            <input type="radio" value="192" name="ratio_series" onclick="ratio_calculate()" />
                            <label>E192</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>{t}Typ:{/t}</label>
                    <div>
                        <div class="radio radio-inline">
                            <input type="radio" value="1" name="ratio_type" onclick="ratio_calculate()" />
                            <label>{t}Verhältnis{/t}</label> 
                            </div>&nbsp;
                            <img src="{$relative_path}img/calculator/v1.png" alt="V=R1/R2"/>&nbsp;
                        <div class="radio radio-inline">
                            <input type="radio" value="2" name="ratio_type" onclick="ratio_calculate()" />
                            <label>{t}Spannungsteiler{/t}</label>
                        </div>&nbsp;
                        <img src="{$relative_path}img/calculator/v2.png" alt="V=R1/(R1+R2)"/>
                    </div>
                </div>
                <div class="form-group">
                    <label>{t}Verhältnis:{/t}</label> 
                    <input type="number" class="form-control" value="" name="ratio_value" size="10"/> 
                    <div class="checkbox">
                        <input type="checkbox" name="ratio_reciprocal" onclick="ratio_calculate()"/>
                        <label>{t}Kehrwert{/t}</label>
                    </div>
                </div>
                <!--
                <div class="form-group">
                    <span>R<sub>1</sub>=<span id="ratio_r1_value">?</span><span id="ratio_r1_unit" class="unit">kOhm</span>
                    <span>R<sub>2</sub>=<span id="ratio_r2_value">?</span><span id="ratio_r2_unit" class="unit">kOhm</span>
                    Fehler: <span id="ratio_error">?</span><span id="ratio_error_unit" class="unit">%</span>
                </div> -->
                <div class="form-group">
                    <button type="button" class="btn btn-primary" value="" onclick="ratio_calculate()">{t}Berechne{/t}</button>
                </div>
            </form>
        </div>
    </div>
</div>
