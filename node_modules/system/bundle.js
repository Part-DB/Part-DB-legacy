#!/usr/bin/env node
/*eslint-env node*/
"use strict";

var FS = require("fs");
var Path = require("path");
var Location = require("./location");
var Identifier = require("./identifier");
var System = require("./system");

var boilerplate = FS.readFileSync(Path.join(__dirname, "boilerplate.js"), "utf-8");
boilerplate = boilerplate.replace(/\s*$/, "");

exports.bundleSystemId = bundleSystemId;
function bundleSystemId(system, id) {
    return system.load(id)
    .then(function () {

        var names = Object.keys(system.modules).sort();
        var bundle = [];
        var index = 0;
        for (var nameIndex = 0; nameIndex < names.length; nameIndex++) {
            var name = names[nameIndex];
            var module = system.modules[name];
            if (module.error) {
                throw module.error;
            }
            if (module.text != null && !module.bundled) {
                module.index = index++;
                module.bundled = true;
                bundle.push(module);
            }
        }

        var payload = "[" + bundle.map(function (module) {
            var dependencies = {};
            module.dependencies.forEach(function (dependencyId) {
                var dependency = module.system.lookup(dependencyId, module.id);
                dependencies[dependencyId] = dependency.index;
            });
            var title = module.filename;
            var lead = "\n// ";
            var rule = Array(title.length + 1).join("-");
            var heading = lead + title + lead + rule + "\n\n";
            var text = heading + module.text;
            return "[" +
                JSON.stringify(module.id) + "," +
                JSON.stringify(module.dirname) + "," +
                JSON.stringify(Identifier.basename(module.filename)) + "," +
                JSON.stringify(dependencies) + "," +
                "function (require, exports, module, __filename, __dirname){\n" + text + "\n}" +
            "]";
        }).join(",") + "]";

        var main = system.lookup(id);

        return (
            "// @generated\n" +
            boilerplate +
            "(" + payload + ")" +
            "(" + JSON.stringify(main.filename) + ")"
        );
    });
}

exports.bundleLocationId = bundleLocationId;
function bundleLocationId(location, id) {
    return System.load(location, {
        node: true
    }).then(function (buildSystem) {
        return System.load(location, {
            browser: true,
            buildSystem: buildSystem
        }).then(function (system) {
            return bundleSystemId(system, id);
        });
    });
}

exports.bundleDirectoryId = bundleDirectoryId;
function bundleDirectoryId(path, id) {
    return bundleLocationId(Location.fromDirectory(path), id);
}

exports.bundleFile = bundleFile;
function bundleFile(path) {
    return System.findSystemLocationAndModuleId(path)
    .then(function (pair) {
        return bundleLocationId(pair.location, pair.id);
    });
}

exports.main = main;
function main() {
    return bundleFile(Path.resolve(process.cwd(), process.argv[2]))
    .then(function (bundle) {
        process.stdout.write(bundle);
    });
}

if (require.main === module) {
    main();
}
