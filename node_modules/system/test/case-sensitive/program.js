var test = require("test");
try {
    require("./a");
    require("./A");
    test.assert(false, "should fail to require alternate spelling");
} catch (error) {
    test.assert(/Can't refer to single module with multiple case conventions/.test(error.message), "error message for inconsistent case");
}
