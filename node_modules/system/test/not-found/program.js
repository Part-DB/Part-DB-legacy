var test = require("test");
test.print("Can't XHR a.js expected", "info");
try {
    require("a");
} catch (exception) {
    test.print(exception.message);
    test.assert(/Can't require module "a" via "program" in "not-found-spec" because Can't XHR /.test(exception.message));
}
test.print("DONE", "info");
