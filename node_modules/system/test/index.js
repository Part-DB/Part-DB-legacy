/*eslint-env node*/
/*eslint no-console:[0]*/
/*global console*/
"use strict";

var Path = require("path");
var System = require("../system");
var Location = require("../location");

var failures = 0;

var test = {
    comment: function (message) {
        console.log(message);
    },
    assert: function (ok, message) {
        if (ok) {
            console.log("ok - " + message);
        } else {
            console.log("not ok - " + message);
            failures++;
        }
    }
};

[
    "case-sensitive",
    "comments",
    "cyclic",
    "determinism",
    "dev-dependencies",
    "exact-exports",
    "hasOwnProperty",
    "main-name",
    "main",
    "method",
    "missing",
    "module-exports",
    "monkeys",
    "nested",
    "redirects",
    "redirect-extension",
    "relative",
    "transitive",
    "translator",
    "translator-package",
    "analyzer",
    "analyzer-package",
    "introduction"
].reduce(function (prev, name) {
    return prev.then(function () {
        console.log("# " + name);
        var location = Location.fromDirectory(Path.join(__dirname, name));
        return System.load(location, {
            modules: {
                test: { exports: test }
            }
        }).then(function (system) {
            return system.import("./program");
        }).catch(function (error) {
            console.log("not ok - test terminated with error");
            console.log(error.stack);
        });
    });
}, Promise.resolve()).then(function () {
    process.exit(Math.min(failures, 255));
});
