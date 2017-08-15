var test = require("test");
var hello = require("./hello.text");
test.assert(hello === "Hello, World!\n", "requires translated text");
