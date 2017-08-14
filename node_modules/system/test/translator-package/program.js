"use strict";
var test = require("test");
var hello = require("./hello.text");
test.assert(hello === "Hello, World!\n", "imports text using packaged extension");
