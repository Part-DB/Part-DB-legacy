/*eslint no-console:[0]*/
/*global console*/
"use strict";

var Identifier = require("../identifier");

console.log(Identifier.resolve("", "") === "");
console.log(Identifier.extension(".js") === "js");
console.log(Identifier.extension("html") === "");
