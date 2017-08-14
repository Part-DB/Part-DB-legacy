var test = require("test");
test.assert(require.lookup("bar.foo").id === "foo-bar");
test.assert(require("bar.foo") === "Hello, Foo!");
test.print("DONE", "info");
