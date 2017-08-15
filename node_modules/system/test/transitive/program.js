var test = require("test");
test.assert(require("./a").foo() === 1, "transitive");
