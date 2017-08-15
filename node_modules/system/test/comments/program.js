// require("./a");
// require("b");
var test = require("test");
if (typeof window !== "undefined") {
    test.comment("Can't XHR a.js is expected");
    test.comment("Can't XHR b is expected");
}
