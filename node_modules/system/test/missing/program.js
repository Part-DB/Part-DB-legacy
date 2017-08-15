var test = require("test");
try {
    require("bogus");
    test.assert(false, "require throws error when module missing");
} catch (exception) {
    test.assert(true, "require throws error when module missing");
}
