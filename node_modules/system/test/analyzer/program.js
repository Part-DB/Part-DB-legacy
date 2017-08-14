var test = require("test");
var map = require("./example.map");
test.assert(map.hello === "Hello, World!\n", "requires dependency through extension");
