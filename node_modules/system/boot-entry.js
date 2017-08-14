/*eslint-env browser*/
"use strict";

var System = require("./system");
var URL = require("./url");
var getParams = require("./script-params");

module.exports = boot;
function boot(params) {
    params = params || getParams("boot.js");
    var moduleLocation = URL.resolve(window.location, ".");
    var systemLocation = URL.resolve(window.location, params.package || ".");

    var abs = "";
    if (moduleLocation.lastIndexOf(systemLocation, 0) === 0) {
        abs = moduleLocation.slice(systemLocation.length);
    }

    var rel = params.import || "";

    return System.load(systemLocation, {
        browser: true
    }).then(function onSystemLoaded(system) {
        window.system = system;
        return system.import(rel, abs);
    });
}

if (require.main === module) {
    boot();
}
